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

	let discounts = [];

	if (response.ok) {
		const { data: coupons } = await response.json();

		const now = new Date();
		discounts = coupons.filter(
			(c: { code: string | null; starts_at: string; ends_at: string }) =>
				c.code === null && new Date(c.starts_at) <= now && new Date(c.ends_at) >= now
		);
	}

	let wishlist = [];
	if (locals.user) {
		const wishlistRes = await fetch(`${API_URL}/wishlists`, {
			headers: {
				Authorization: cookies.get('auth_token') ? `Bearer ${cookies.get('auth_token')}` : '',
				'X-Requested-With': 'XMLHttpRequest'
			}
		});
		if (wishlistRes.ok) {
			wishlist = (await wishlistRes.json()).data;
		}
	}

	return {
		user: locals.user,
		discounts,
		wishlist
	};
};
