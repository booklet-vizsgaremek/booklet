import { error } from '@sveltejs/kit';
import type { LayoutServerLoad } from './$types';
import { API_URL } from '$env/static/private';

export const load: LayoutServerLoad = async ({ locals }) => {
	if (!locals.user || !['manager', 'admin'].includes(locals.user.role)) return error(403);

	return {
		publishers: (await fetch(`${API_URL}/publishers`).then((x) => x.json())).data,
		genres: (await fetch(`${API_URL}/genres`).then((x) => x.json())).data,
		authors: (await fetch(`${API_URL}/authors`).then((x) => x.json())).data
	};
};
