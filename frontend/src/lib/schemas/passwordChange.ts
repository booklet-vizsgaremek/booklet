import { z } from 'zod';
import * as m from '$lib/paraglide/messages.js';

export const passwordChangeSchema = z
	.object({
		current_password: z
			.string({ error: () => m['validation.zod_invalid_type']() })
			.min(8, {
				error: (iss) => m['validation.zod_string_too_short']({ min: iss.minimum as number })
			})
			.max(64, {
				error: (iss) => m['validation.zod_string_too_long']({ max: iss.maximum as number })
			}),
		password: z
			.string({ error: () => m['validation.zod_invalid_type']() })
			.min(8, {
				error: (iss) => m['validation.zod_string_too_short']({ min: iss.minimum as number })
			})
			.max(64, {
				error: (iss) => m['validation.zod_string_too_long']({ max: iss.maximum as number })
			}),
		password_confirmation: z
			.string({ error: () => m['validation.zod_invalid_type']() })
			.min(8, {
				error: (iss) => m['validation.zod_string_too_short']({ min: iss.minimum as number })
			})
			.max(64, {
				error: (iss) => m['validation.zod_string_too_long']({ max: iss.maximum as number })
			})
	})
	.superRefine((data, ctx) => {
		if (data.password !== data.password_confirmation) {
			const error = m['validation.zod_passwords_not_matching']();
			ctx.addIssue({ code: 'custom', message: error, path: ['password'] });
			ctx.addIssue({ code: 'custom', message: error, path: ['password_confirmation'] });
		} else if (data.password === data.current_password) {
			const error = m['validation.zod_passwords_matching']();
			ctx.addIssue({ code: 'custom', message: error, path: ['password'] });
			ctx.addIssue({ code: 'custom', message: error, path: ['current_password'] });
		}
	});

export type PasswordChangeSchema = typeof passwordChangeSchema;
