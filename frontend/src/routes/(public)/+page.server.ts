import type { PageServerLoad } from './$types';
import { API_URL } from '$env/static/private';
import * as m from '$lib/paraglide/messages.js';

export const load: PageServerLoad = async () => {
	const { data } = await fetch(`${API_URL}/books/top-purchased`).then((x) => x.json());

	return {
		books: data,
		title: m['title.home']()
	};
};
