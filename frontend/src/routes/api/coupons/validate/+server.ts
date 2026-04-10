import { API_URL } from '$env/static/private';
import type { RequestHandler } from './$types';

export const GET: RequestHandler = async ({ url, fetch, cookies }) => {
	const code = url.searchParams.get('code');
	const token = cookies.get('auth_token');

	const response = await fetch(`${API_URL}/coupons/validate?code=${code}`, {
		headers: {
			Authorization: `Bearer ${token}`
		}
	});

	return new Response(JSON.stringify(await response.json()), {
		status: response.status,
		headers: { 'Content-Type': 'application/json' }
	});
};
