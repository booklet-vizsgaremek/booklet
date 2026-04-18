import type { PageServerLoad, Actions } from './$types';
import { API_URL } from '$env/static/private';
import { fail } from '@sveltejs/kit';
import * as m from '$lib/paraglide/messages.js';
import type { Coupon } from '$lib/types/coupon';

export const load: PageServerLoad = async ({ locals }) => {
	return {
		title: m['title.checkout'](),
		user: locals.user
	};
};

export const actions: Actions = {
	checkout: async ({ request, fetch, locals, cookies }) => {
		const form = await request.formData();
		const books = JSON.parse(form.get('books') as string);
		const coupons = JSON.parse(form.get('coupons[]') as string).map((x: Coupon) => x.id);

		const body: Record<string, unknown> = {
			user_id: locals.user?.id,
			date: new Date().toISOString(),
			books,
			coupons
		};

		const response = await fetch(`${API_URL}/receipts`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				Authorization: `Bearer ${cookies.get('auth_token')}`,
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: JSON.stringify(body)
		});

		return response.ok
			? { success: true }
			: fail(500, { error: m['messages.failed_to_place_order']() });
	}
};
