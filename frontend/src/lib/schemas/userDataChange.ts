import { z } from 'zod';
import * as m from '$lib/paraglide/messages.js';

export const userDataChangeSchema = z.object({
	email: z.email({ error: () => m['validation.zod_invalid_email']() }),
	first_name: z
		.string({ error: () => m['validation.zod_invalid_type']() })
		.min(2, {
			error: (iss) => m['validation.zod_string_too_short']({ min: iss.minimum as number })
		})
		.max(64, {
			error: (iss) => m['validation.zod_string_too_long']({ max: iss.maximum as number })
		}),
	last_name: z
		.string({ error: () => m['validation.zod_invalid_type']() })
		.min(2, {
			error: (iss) => m['validation.zod_string_too_short']({ min: iss.minimum as number })
		})
		.max(64, {
			error: (iss) => m['validation.zod_string_too_long']({ max: iss.maximum as number })
		})
});

export type UserDataChangeSchema = typeof userDataChangeSchema;
