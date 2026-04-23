import { API_URL } from '$env/static/private';
import type { PageServerLoad } from './$types';
import * as m from '$lib/paraglide/messages.js';

export const load: PageServerLoad = async ({ fetch, cookies }) => {
	const res = await fetch(`${API_URL}/receipts`, {
		headers: {
			Authorization: `Bearer ${cookies.get('auth_token')}`,
			'X-Requested-With': 'XMLHttpRequest'
		}
	}).then((x) => x.json());

	return {
		title: m['title.orders'](),
		receipts: res.data,
		meta: res.meta
	};
};
