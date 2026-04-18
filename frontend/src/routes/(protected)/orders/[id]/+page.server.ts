import { API_URL } from '$env/static/private';
import type { PageServerLoad } from './$types';
import * as m from '$lib/paraglide/messages.js';

export const load: PageServerLoad = async ({ params, fetch, cookies }) => {
	return {
		title: m['title.order']({ id: params.id }),
		receipt: (
			await fetch(`${API_URL}/receipts/${params.id}`, {
				headers: {
					Authorization: `Bearer ${cookies.get('auth_token')}`,
					'X-Requested-With': 'XMLHttpRequest'
				}
			}).then((x) => x.json())
		).data
	};
};
