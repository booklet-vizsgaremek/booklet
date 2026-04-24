import type { Actions, PageServerLoad } from './$types';
import { dev } from '$app/environment';
import { superValidate } from 'sveltekit-superforms';
import { passwordChangeSchema } from '$lib/schemas/passwordChange';
import { zod4 } from 'sveltekit-superforms/adapters';
import { fail } from '@sveltejs/kit';
import { API_URL } from '$env/static/private';
import * as m from '$lib/paraglide/messages.js';
import { userDataChangeSchema } from '$lib/schemas/userDataChange';

export const load: PageServerLoad = async ({ locals }) => {
	return {
		title: m['title.account_settings'](),
		passwordForm: await superValidate(zod4(passwordChangeSchema)),
		userDataForm: await superValidate(
			{
				email: locals.user?.email ?? '',
				first_name: locals.user?.first_name ?? '',
				last_name: locals.user?.last_name ?? ''
			},
			zod4(userDataChangeSchema)
		)
	};
};

export const actions: Actions = {
	deleteAccount: async (event) => {
		const response = await event.fetch(`${API_URL}/users/${event.locals.user?.id}`, {
			method: 'DELETE',
			headers: {
				'Content-Type': 'application/json',
				Authorization: `Bearer ${event.cookies.get('auth_token')}`,
				'X-Requested-With': 'XMLHttpRequest'
			}
		});

		if (response.status !== 204)
			return fail(response.status, { error: m['messages.server_error']() });
		else {
			return { success: true };
		}
	},
	userDataChange: async (event) => {
		const form = await superValidate(event, zod4(userDataChangeSchema));
		if (!form.valid) return fail(400, { form, error: m['messages.server_error']() });

		const response = await event.fetch(`${API_URL}/users/${event.locals.user?.id}`, {
			method: 'PATCH',
			headers: {
				'Content-Type': 'application/json',
				Authorization: `Bearer ${event.cookies.get('auth_token')}`,
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: JSON.stringify(form.data)
		});

		if (!response.ok) return fail(response.status, { form, error: m['messages.server_error']() });

		const res = await response.json();

		if (res.message) return fail(422, { form, error: m['messages.email_taken']() });
		else {
			event.locals.user = res.data;
			return { success: true, form };
		}
	},
	passwordChange: async (event) => {
		const form = await superValidate(event, zod4(passwordChangeSchema));
		if (!form.valid) return fail(400, { form, error: m['messages.invalid_current_password']() });

		const response = await event.fetch(`${API_URL}/users/${event.locals.user?.id}/password`, {
			method: 'PATCH',
			headers: {
				'Content-Type': 'application/json',
				Authorization: `Bearer ${event.cookies.get('auth_token')}`,
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: JSON.stringify(form.data)
		});

		if (!response.ok) {
			switch (response.status) {
				case 401:
				case 422:
					return fail(response.status, {
						form,
						error: m['messages.invalid_current_password']()
					});
					break;
				default:
					return fail(response.status, {
						form,
						error: m['messages.server_error']()
					});
					break;
			}
		}

		const loginResponse = await event.fetch(`${API_URL}/auth/login`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				email: event.locals.user?.email,
				password: form.data.password
			})
		});

		if (!loginResponse.ok) {
			return fail(500, {
				form,
				error: m['messages.server_error']()
			});
		}

		const { token } = await loginResponse.json();

		event.cookies.set('auth_token', token, {
			path: '/',
			httpOnly: true,
			sameSite: 'lax',
			maxAge: 60 * 60 * 24,
			secure: !dev
		});

		return { success: true, form };
	}
};
