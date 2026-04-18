import { API_URL } from '$env/static/private';
import type { RequestHandler } from './$types';

export const GET: RequestHandler = async ({ url, fetch, cookies }) => {
	const res = await fetch(`${API_URL}/coupons/validate?code=${url.searchParams.get('code')}`, {
		headers: {
			Authorization: `Bearer ${cookies.get('auth_token')}`
		}
	});

	return new Response(JSON.stringify(await res.json()), {
		status: res.status,
		headers: { 'Content-Type': 'application/json' }
	});
};
