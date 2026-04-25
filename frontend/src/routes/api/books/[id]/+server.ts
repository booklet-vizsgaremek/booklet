import { json } from '@sveltejs/kit';
import { API_URL } from '$env/static/private';
import type { RequestHandler } from './$types';

export const DELETE: RequestHandler = async ({ params, fetch, cookies }) => {
	const response = await fetch(`${API_URL}/books/${params.id}`, {
		method: 'DELETE',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
			Authorization: `Bearer ${cookies.get('auth_token')}`
		}
	});

	return !response.ok
		? json(await response.json(), { status: response.status })
		: new Response(null, { status: 204 });
};
