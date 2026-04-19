import { API_URL } from '$env/static/private';
import { error } from '@sveltejs/kit';
import type { PageServerLoad } from './$types';
import * as m from '$lib/paraglide/messages.js';

export const load: PageServerLoad = async ({ fetch, params }) => {
	const response = await fetch(`${API_URL}/authors/${params.id}`);

	if (!response.ok) return error(404, m['messages.page_not_found']());

	const { data: author } = await response.json();

	return {
		title: author.name,
		author
	};
};
