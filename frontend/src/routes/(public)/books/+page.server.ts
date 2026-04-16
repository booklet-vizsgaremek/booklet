import type { PageServerLoad } from './$types';
import * as m from '$lib/paraglide/messages.js';
import { API_URL } from '$env/static/private';

export const load: PageServerLoad = async ({ fetch, url, locals }) => {
	const filters = url.searchParams.size ? `?${url.searchParams}` : '';

	const res = await fetch(`${API_URL}/books${filters}`, {
		headers: { 'X-Requested-With': 'XMLHttpRequest' }
	}).then((x) => x.json());

	return {
		title: m['title.book_lookup'](),
		books: res.data,
		authors: (await fetch(`${API_URL}/authors`).then((x) => x.json())).data,
		genres: (await fetch(`${API_URL}/genres`).then((x) => x.json())).data,
		publishers: (await fetch(`${API_URL}/publishers`).then((x) => x.json())).data,
		meta: res.meta,
		user: locals.user
	};
};
