import { syncZodLocale } from '$lib/zod-locale';
import type { LayoutServerLoad } from './$types';

export const load: LayoutServerLoad = async ({ locals }) => {
	syncZodLocale();

	return {
		user: locals.user
	};
};
