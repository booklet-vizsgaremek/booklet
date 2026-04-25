import { API_URL } from '$env/static/private';
import type { Actions, PageServerLoad } from './$types';
import * as m from '$lib/paraglide/messages.js';
import { fail } from '@sveltejs/kit';
import { error } from '@sveltejs/kit';

export const load: PageServerLoad = async ({ params, fetch, cookies }) => {
	const response = await fetch(`${API_URL}/receipts/${params.id}`, {
		headers: {
			Authorization: `Bearer ${cookies.get('auth_token')}`,
			'X-Requested-With': 'XMLHttpRequest'
		}
	});

	if (!response.ok) error(404, m['messages.order_not_found']());

	const { data: receipt } = await response.json();

	return {
		title: m['title.order']({ id: params.id }),
		receipt
	};
};

export const actions: Actions = {
	default: async ({ cookies, request }) => {
		const response = await fetch(
			`${API_URL}/pickups/${(await request.formData()).get('pickupId')}`,
			{
				method: 'PATCH',
				headers: {
					Authorization: `Bearer ${cookies.get('auth_token')}`,
					'X-Requested-With': 'XMLHttpRequest',
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ status: 'cancelled', completed_at: new Date().toISOString() })
			}
		);

		return response.ok
			? { success: true }
			: fail(500, { error: m['messages.failed_to_cancel_order']() });
	}
};
