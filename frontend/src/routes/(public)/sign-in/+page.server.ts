import type { PageServerLoad } from './$types.js';
import { superValidate } from 'sveltekit-superforms';
import { signInSchema } from '$lib/schemas/signIn';
import { zod4 } from 'sveltekit-superforms/adapters';
import { fail, redirect } from '@sveltejs/kit';
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
		if (!form.valid) return fail(400, { form, error: m['messages.invalid_credentials']() });

		const response = await event.fetch(`${API_URL}/auth/login`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(form.data)
		});

		if (!response.ok) {
			switch (response.status) {
				case 401:
				case 422:
					return fail(response.status, {
						form,
						error: m['messages.invalid_credentials']()
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

		const { token } = await response.json();

		event.cookies.set('auth_token', token, {
			path: '/',
			httpOnly: true,
			sameSite: 'lax',
			maxAge: 60 * 60 * 24,
			secure: !dev
		});

		return redirect(302, validateRedirect(event.url.searchParams.get('redirect')));
	}
};
