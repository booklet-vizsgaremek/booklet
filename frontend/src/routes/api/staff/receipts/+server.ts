import { json, error } from '@sveltejs/kit';
import { API_URL } from '$env/static/private';
import type { RequestHandler } from './$types';
import { getLocale } from '$lib/paraglide/runtime';

export const GET: RequestHandler = async ({ url, fetch, cookies }) => {
	const params = new URLSearchParams({
		page: url.searchParams.get('page') ?? '1',
		per_page: url.searchParams.get('per_page') ?? '10'
	});

	const search = url.searchParams.get('search');
	const status = url.searchParams.get('status');

	if (search) params.set('search', search);
	if (status) params.set('status', status);

	const response = await fetch(`${API_URL}/receipts?${params.toString()}`, {
		headers: {
			Authorization: `Bearer ${cookies.get('auth_token')}`,
			'X-Requested-With': 'XMLHttpRequest',
			'X-Locale': getLocale()
		}
	});

	if (!response.ok) error(response.status);

	return json(await response.json());
};
