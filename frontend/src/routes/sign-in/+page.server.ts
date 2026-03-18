import type { PageServerLoad } from './$types.js';
import { superValidate } from 'sveltekit-superforms';
import { signInSchema } from '$lib/schemas/signIn';
import { zod4 } from 'sveltekit-superforms/adapters';

export const load: PageServerLoad = async () => {
	return {
		form: await superValidate(zod4(signInSchema))
	};
};

export const actions = {
	default: async ({ request }) => {
		const formData = Object.fromEntries((await request.formData()).entries());
		// TODO: Sign in action
		return { success: true, formData };
	}
};
