import * as z from 'zod';
import { getLocale } from '$lib/paraglide/runtime.js';

const localeMap = {
	en: z.locales.en,
	hu: z.locales.hu
};

export const syncZodLocale = () =>
	z.config((localeMap[getLocale() as keyof typeof localeMap] ?? z.locales.en)());
