import type { PageServerLoad } from './$types';
import type { MessageKey } from '$lib';

export const load: PageServerLoad = async () => ({
	titleKey: 'title.home' as MessageKey
});
