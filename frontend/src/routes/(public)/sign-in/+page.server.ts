import type { PageServerLoad } from './$types.js';
import { superValidate, message } from 'sveltekit-superforms';
import { signInSchema } from '$lib/schemas/signIn';
import { zod4 } from 'sveltekit-superforms/adapters';
import { redirect } from '@sveltejs/kit';
import { API_URL } from '$env/static/private';
import * as m from '$lib/paraglide/messages.js';
import { dev } from '$app/environment';
import { validateRedirect } from '$lib/utils/redirect';

export const load: PageServerLoad = async () => {
	return {
		title: m['auth.sign_in'](),
		form: await superValidate(zod4(signInSchema))
	};
};

export const actions = {
	default: async (event) => {
		const form = await superValidate(event, zod4(signInSchema));
		if (!form.valid) return message(form, m['messages.invalid_credentials'](), { status: 400 });

		const response = await event.fetch(`${API_URL}/auth/login`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: JSON.stringify(form.data)
		});

		if (!response.ok) {
			switch (response.status) {
				case 401:
				case 422:
					return message(form, m['messages.invalid_credentials'](), { status: response.status });
					break;
				default:
					return message(form, m['messages.server_error'](), { status: 500 });
					break;
			}
		}

		const { token } = await response.json();

		event.cookies.set('auth_token', token, {
			path: '/',
			httpOnly: true,
			sameSite: 'lax',
			maxAge: 60 * 60 * 24,
			secure: !dev
		});

		const { data } = await event
			.fetch(`${API_URL}/users/self`, {
				headers: {
					'Content-Type': 'application/json',
					Authorization: `Bearer ${token}`
				}
			})
			.then((x) => x.json());

		return redirect(
			302,
			validateRedirect(
				event.url.searchParams.get('redirect'),
				data.role === 'customer' ? '/' : `/${data.role}`
			)
		);
	}
};
