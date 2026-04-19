import type { PageServerLoad } from './$types';
import { API_URL } from '$env/static/private';
import * as m from '$lib/paraglide/messages.js';

export const load: PageServerLoad = async ({ cookies }) => {
	return {
		topPurchased: (await fetch(`${API_URL}/books/top-purchased`).then((x) => x.json())).data,
		randomCategory: (await fetch(`${API_URL}/books/random-category`).then((x) => x.json())).data,
		discounted: (
			await fetch(`${API_URL}/books/discounted`, {
				headers: {
					Authorization: cookies.get('auth_token') ? `Bearer ${cookies.get('auth_token')}` : '',
					'X-Requested-With': 'XMLHttpRequest'
				}
			}).then((x) => x.json())
		).data,
		title: m['title.home']()
	};
};
