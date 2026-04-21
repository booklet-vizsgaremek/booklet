import type { PageServerLoad } from './$types.js';
import { superValidate, message } from 'sveltekit-superforms';
import { signUpSchema } from '$lib/schemas/signUp';
import { zod4 } from 'sveltekit-superforms/adapters';
import { redirect } from '@sveltejs/kit';
import { API_URL } from '$env/static/private';
import * as m from '$lib/paraglide/messages.js';
import { dev } from '$app/environment';

export const load: PageServerLoad = async () => {
	return {
		title: m['auth.sign_up'](),
		form: await superValidate(zod4(signUpSchema))
	};
};

export const actions = {
	default: async (event) => {
		const form = await superValidate(event, zod4(signUpSchema));

		if (!form.valid) return message(form, m['messages.invalid_credentials'](), { status: 400 });

		const response = await event.fetch(`${API_URL}/auth/register`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: JSON.stringify(form.data)
		});

		if (!response.ok) {
			if (response.status == 422) {
				return message(form, m['messages.email_taken'](), { status: response.status });
			} else {
				return message(form, m['messages.server_error'](), { status: 500 });
			}
		}

		const loginResponse = await event.fetch(`${API_URL}/auth/login`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				email: form.data.email,
				password: form.data.password
			})
		});

		if (!loginResponse.ok) {
			redirect(302, '/sign-in');
		}

		const { token } = await loginResponse.json();

		event.cookies.set('auth_token', token, {
			path: '/',
			httpOnly: true,
			sameSite: 'lax',
			maxAge: 60 * 60 * 24,
			secure: !dev
		});

		return { form, success: true };
	}
};
