import { syncZodLocale } from '$lib/zod-locale';
import type { LayoutServerLoad } from './$types';
import { API_URL } from '$env/static/private';

export const load: LayoutServerLoad = async ({ locals, fetch, cookies }) => {
	syncZodLocale();

	const response = await fetch(`${API_URL}/coupons`);
	const { data: coupons } = await response.json();

	const discounts = coupons.filter((c: { code: string | null }) => c.code === null);

	return {
		user: locals.user,
		discounts
	};
};
