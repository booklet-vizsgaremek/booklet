import type { PageServerLoad } from './$types';
import * as m from '$lib/paraglide/messages.js';
import { API_URL } from '$env/static/private';
import { getLocale } from '$lib/paraglide/runtime';

export const load: PageServerLoad = async ({ fetch, url, cookies }) => {
	const filters = url.searchParams.size ? `?${url.searchParams}` : '';

	const res = await fetch(`${API_URL}/users${filters}`, {
		headers: {
			'X-Requested-With': 'XMLHttpRequest',
			Authorization: `Bearer ${cookies.get('auth_token')}`,
			'X-Locale': getLocale()
		}
	}).then((x) => x.json());

	return {
		title: m['title.users'](),
		users: res.data,
		meta: res.meta
	};
};
