import type { PageServerLoad } from './$types.js';
import { superValidate } from 'sveltekit-superforms';
import { signInSchema } from '$lib/schemas/signIn';
import { zod4 } from 'sveltekit-superforms/adapters';
import { fail } from '@sveltejs/kit';

export const load: PageServerLoad = async () => {
	return {
		titleKey: 'auth.sign_in',
		form: await superValidate(zod4(signInSchema))
	};
};

export const actions = {
	default: async (event) => {
		// TODO: Sign in action
		const form = await superValidate(event, zod4(signInSchema));

		if (!form.valid)
			return fail(400, {
				form
			});

		return {
			success: true,
			form
		};
	}
};
