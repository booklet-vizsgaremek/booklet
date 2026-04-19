import { syncZodLocale } from '$lib/zod-locale';
import type { LayoutServerLoad } from './$types';
import { API_URL } from '$env/static/private';

export const load: LayoutServerLoad = async ({ locals, fetch, cookies }) => {
	syncZodLocale();

	const response = await fetch(`${API_URL}/coupons`, {
		headers: {
			Authorization: cookies.get('auth_token') ? `Bearer ${cookies.get('auth_token')}` : '',
			'X-Requested-With': 'XMLHttpRequest'
		}
	});

	const { data: coupons } = await response.json();

	const now = new Date();
	const discounts = coupons.filter(
		(c: { code: string | null; starts_at: string; ends_at: string }) =>
			c.code === null && new Date(c.starts_at) <= now && new Date(c.ends_at) >= now
	);

	return {
		user: locals.user,
		discounts
	};
};
