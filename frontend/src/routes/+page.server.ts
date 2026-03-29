import type { PageServerLoad } from './$types';
import type { MessageKey } from '$lib';
import { API_URL } from '$env/static/private';

export const load: PageServerLoad = async () => {
	const { data } = await fetch(`${API_URL}/books/top-purchased`).then((x) => x.json());

	return {
		books: data,
		titleKey: 'title.home' as MessageKey
	};
};
