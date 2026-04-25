import { z } from 'zod';
import * as m from '$lib/paraglide/messages.js';

export const bookSchema = z.object({
	title: z
		.string({ error: () => m['validation.zod_invalid_type']() })
		.min(4, {
			error: (iss) => m['validation.zod_string_too_short']({ min: iss.minimum as number })
		})
		.max(255, {
			error: (iss) => m['validation.zod_string_too_long']({ max: iss.maximum as number })
		}),
	price: z.number({ error: () => m['validation.zod_invalid_type']() }).min(0, {
		error: (iss) => m['validation.zod_number_too_low']({ min: iss.minimum as number })
	}),
	pages: z.number({ error: () => m['validation.zod_invalid_type']() }).min(1, {
		error: (iss) => m['validation.zod_number_too_low']({ min: iss.minimum as number })
	}),
	release_year: z
		.number({ error: () => m['validation.zod_invalid_type']() })
		.min(1800, {
			error: (iss) => m['validation.zod_number_too_low']({ min: iss.minimum as number })
		})
		.max(new Date().getFullYear(), {
			error: (iss) => m['validation.zod_number_too_high']({ max: iss.maximum as number })
		}),
	stock: z.number({ error: () => m['validation.zod_invalid_type']() }),
	publisher_id: z.uuid({ error: () => m['validation.zod_invalid_type']() }),
	genre_id: z.uuid({ error: () => m['validation.zod_invalid_type']() }),
	author_ids: z
		.array(z.uuid({ error: () => m['validation.zod_invalid_type']() }))
		.min(1, { error: () => m['validation.zod_array_too_short']({ min: 1 }) }),
	cover: z
		.instanceof(File, { error: () => m['validation.zod_invalid_type']() })
		.refine((f) => f.size < 2_000_000, { error: () => m['validation.zod_file_too_large']() })
		.refine((f) => ['image/jpeg', 'image/png', 'image/webp'].includes(f.type), {
			error: () => m['validation.zod_invalid_file_type']()
		})
		.optional()
});

export type BookSchema = typeof bookSchema;
