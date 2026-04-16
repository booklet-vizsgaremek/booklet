import { redirect } from '@sveltejs/kit';
import type { Actions, PageServerLoad } from './$types';
import { dev } from '$app/environment';
import { superValidate } from 'sveltekit-superforms';
import { passwordChangeSchema } from '$lib/schemas/passwordChange';
import { zod4 } from 'sveltekit-superforms/adapters';
import { fail } from '@sveltejs/kit';
import { API_URL } from '$env/static/private';
import * as m from '$lib/paraglide/messages.js';

export const load: PageServerLoad = async ({ locals }) => {
	return {
		title: m['title.account_settings'](),
		user: locals.user,
		form: await superValidate(zod4(passwordChangeSchema))
	};
};

export const actions: Actions = {
	signout: async ({ cookies }) => {
		cookies.delete('auth_token', {
			path: '/',
			httpOnly: true,
			sameSite: 'lax',
			secure: !dev
		});
		redirect(302, '/sign-in');
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
