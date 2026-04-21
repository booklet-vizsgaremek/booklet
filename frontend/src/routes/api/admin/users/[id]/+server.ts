import { json } from '@sveltejs/kit';
import type { RequestHandler } from './$types';
import { API_URL } from '$env/static/private';

export const PATCH: RequestHandler = async ({ params, request, cookies }) => {
	const body = await request.json();

	const response = await fetch(`${API_URL}/users/${params.id}/role`, {
		method: 'PATCH',
		headers: {
			'Content-Type': 'application/json',
			Accept: 'application/json',
			Authorization: `Bearer ${cookies.get('auth_token')}`
		},
		body: JSON.stringify(body)
	});

	const data = await response.json();

	return !response.ok ? json(data, { status: response.status }) : json(data);
};

export const DELETE: RequestHandler = async ({ params, cookies }) => {
	const response = await fetch(`${API_URL}/users/${params.id}`, {
		method: 'DELETE',
		headers: {
			Accept: 'application/json',
			Authorization: `Bearer ${cookies.get('auth_token')}`
		}
	});

	return !response.ok
		? json(await response.json(), { status: response.status })
		: new Response(null, { status: 204 });
};
