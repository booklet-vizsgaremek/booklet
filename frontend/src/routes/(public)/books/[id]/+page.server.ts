import type { PageServerLoad } from './$types';
import { API_URL } from '$env/static/private';
import { error } from '@sveltejs/kit';
import * as m from '$lib/paraglide/messages.js';

export const load: PageServerLoad = async ({ params, fetch }) => {
	const response = await fetch(`${API_URL}/books/${params.id}`);

	if (!response.ok) error(404, m['messages.book_not_found']());

	const { data: book } = await response.json();

	return {
		book,
		booksFromGenre: (await fetch(`${API_URL}/books?genre=${book.genre.id}`).then((x) => x.json()))
			.data,
		title: book.title
	};
};
