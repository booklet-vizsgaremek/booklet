import type { Handle } from '@sveltejs/kit';
import { sequence } from '@sveltejs/kit/hooks';
import { paraglideMiddleware } from '$lib/paraglide/server';
import { API_URL } from '$env/static/private';
import { redirect } from '@sveltejs/kit';
import { dev } from '$app/environment';
import type { User } from '$lib/types';

const handleParaglide: Handle = ({ event, resolve }) =>
	paraglideMiddleware(event.request, ({ request, locale }) => {
		event.request = request;
		return resolve(event, {
			transformPageChunk: ({ html }) => html.replace('%paraglide.lang%', locale)
		});
	});

const handleAuth: Handle = async ({ event, resolve }) => {
	const token = event.cookies.get('auth_token');

	if (token) {
		const response = await event.fetch(`${API_URL}/users/self`, {
			headers: {
				Authorization: `Bearer ${token}`,
				'X-Requested-With': 'XMLHttpRequest'
			}
		});

		if (response.ok) {
			event.locals.user = (await response.json()).data;
		} else {
			event.cookies.delete('auth_token', {
				path: '/',
				httpOnly: true,
				sameSite: 'lax',
				secure: !dev
			});
		}
	}

	const routeId = event.route.id ?? '';
	const user = event.locals.user;

	if (!routeId.includes('(public)') && !routeId.startsWith('/api') && !user) {
		redirect(302, `/sign-in?redirect=${event.url.pathname}`);
	}

	if (
		(event.url.pathname.startsWith('/sign-in') || event.url.pathname.startsWith('/sign-up')) &&
		user
	) {
		const defaultRoutes: Record<Exclude<User['role'], null>, string> = {
			admin: '/admin',
			manager: '/manager',
			staff: '/staff',
			customer: '/profile'
		};

		redirect(302, defaultRoutes[user.role as Exclude<User['role'], null>]);
	}

	return resolve(event);
};

export const handle: Handle = sequence(handleAuth, handleParaglide);
