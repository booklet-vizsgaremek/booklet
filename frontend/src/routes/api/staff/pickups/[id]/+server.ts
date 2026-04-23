import { json, error } from '@sveltejs/kit';
import { API_URL } from '$env/static/private';
import type { RequestHandler } from './$types';

export const PATCH: RequestHandler = async ({ params, request, cookies }) => {
	const { id } = params;
	const body = await request.json();

	const response = await fetch(`${API_URL}/pickups/${id}`, {
		method: 'PATCH',
		headers: {
			Authorization: `Bearer ${cookies.get('auth_token')}`,
			'X-Requested-With': 'XMLHttpRequest',
			'Content-Type': 'application/json'
		},
		body: JSON.stringify(body)
	});

	if (!response.ok) error(response.status);

	return json(await response.json());
};
