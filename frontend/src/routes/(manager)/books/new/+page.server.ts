import { API_URL } from '$env/static/private';
import { zod4 } from 'sveltekit-superforms/adapters';
import type { Actions, PageServerLoad } from './$types';
import { bookSchema } from '$lib/schemas/book';
import { message, superValidate, withFiles } from 'sveltekit-superforms';
import { fail } from '@sveltejs/kit';
import * as m from '$lib/paraglide/messages.js';

export const load: PageServerLoad = async () => {
	return {
		form: await superValidate(zod4(bookSchema)),
		title: m['title.new_book']()
	};
};

export const actions: Actions = {
	default: async ({ request, fetch, cookies }) => {
		const form = await superValidate(request, zod4(bookSchema));
		if (!form.valid) return fail(400, withFiles({ form, error: m['messages.invalid_data']() }));

		const body = new FormData();
		body.append('title', form.data.title);
		body.append('price', String(form.data.price));
		body.append('pages', String(form.data.pages));
		body.append('release_year', String(form.data.release_year));
		body.append('stock', String(form.data.stock));
		body.append('publisher_id', form.data.publisher_id);
		body.append('genre_id', form.data.genre_id);
		form.data.author_ids.forEach((id) => body.append('author_ids[]', id));
		if (form.data.cover) body.append('cover', form.data.cover);

		const response = await fetch(`${API_URL}/books`, {
			method: 'POST',
			body,
			headers: {
				Authorization: `Bearer ${cookies.get('auth_token')}`,
				'X-Requested-With': 'XMLHttpRequest'
			}
		});

		if (!response.ok)
			return fail(response.status, withFiles({ form, error: m['messages.server_error']() }));
		else return withFiles(message(form, { id: (await response.json()).data.id }));
	}
};
