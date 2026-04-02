import { redirect } from '@sveltejs/kit';
import type { Actions, PageServerLoad } from './$types';
import { dev } from '$app/environment';

export const load: PageServerLoad = async ({ locals }) => {
	return {
		user: locals.user
	};
};

export const actions: Actions = {
	signout: async (event) => {
		event.cookies.delete('auth_token', {
			path: '/',
			httpOnly: true,
			sameSite: 'lax',
			secure: !dev
		});
		redirect(302, '/sign-in');
	}
};
