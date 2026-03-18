import * as m from '$lib/paraglide/messages.js';
export type MessageKey = {
	[K in keyof typeof m]: (typeof m)[K] extends () => string ? K : never;
}[keyof typeof m];
