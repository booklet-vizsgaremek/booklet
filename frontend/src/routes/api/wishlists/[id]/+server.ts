import { API_URL } from '$env/static/private';
import type { RequestHandler } from './$types';

export const DELETE: RequestHandler = async ({ fetch, cookies, params }) => {
	const res = await fetch(`${API_URL}/wishlists/${params.id}`, {
		method: 'DELETE',
		headers: {
			Authorization: `Bearer ${cookies.get('auth_token')}`,
			'X-Requested-With': 'XMLHttpRequest'
		}
	});
	return new Response(null, { status: res.status });
};
