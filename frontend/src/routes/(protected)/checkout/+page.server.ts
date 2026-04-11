import type { PageServerLoad, Actions } from './$types';
import { API_URL } from '$env/static/private';
import { fail } from '@sveltejs/kit';
import * as m from '$lib/paraglide/messages.js';

export const load: PageServerLoad = async ({ locals }) => {
	return {
		title: m['title.checkout'](),
		user: locals.user
	};
};

export const actions: Actions = {
	checkout: async ({ request, fetch, locals, cookies }) => {
		const data = await request.formData();
		const books = JSON.parse(data.get('books') as string);
		const couponId = data.get('coupon_id') as string | null;

		const body: Record<string, unknown> = {
			user_id: locals.user?.id,
			date: new Date().toISOString(),
			books
		};

		if (couponId) {
			body.coupons = [couponId];
		}

		const response = await fetch(`${API_URL}/receipts`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				Authorization: `Bearer ${cookies.get('auth_token')}`,
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: JSON.stringify(body)
		});

		if (!response.ok) {
			return fail(500, { error: m['messages.failed_to_place_order']() });
		}

		return { success: true };
	}
};
