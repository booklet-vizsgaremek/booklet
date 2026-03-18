import { z } from 'zod';
import * as m from '$lib/paraglide/messages.js';

export const signInSchema = z.object({
	email: z.email({ error: () => m['validation.zod_invalid_email']() }),
	password: z
		.string({ error: () => m['validation.zod_invalid_type']() })
		.min(8, {
			error: (iss) => m['validation.zod_string_too_short']({ min: iss.minimum as number })
		})
		.max(64, {
			error: (iss) => m['validation.zod_string_too_long']({ max: iss.maximum as number })
		})
});

export type SignInSchema = typeof signInSchema;
