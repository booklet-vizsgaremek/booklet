import { error, redirect } from '@sveltejs/kit';
import type { PageServerLoad, Actions } from './$types';
import * as m from '$lib/paraglide/messages.js';
import { dev } from '$app/environment';

export const load: PageServerLoad = ({ request }) => {
	if (request.method === 'POST') return;
	error(404, m['messages.page_not_found']());
};

export const actions: Actions = {
	default: async ({ cookies }) => {
		cookies.delete('auth_token', { path: '/', httpOnly: true, sameSite: 'lax', secure: !dev });
		redirect(302, '/');
	}
};
