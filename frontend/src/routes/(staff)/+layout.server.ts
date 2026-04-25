import { error } from '@sveltejs/kit';
import type { LayoutServerLoad } from './$types';

export const load: LayoutServerLoad = async ({ locals }) => {
	if (!locals.user || !['admin', 'manager', 'staff'].includes(locals.user.role)) return error(403);
};
