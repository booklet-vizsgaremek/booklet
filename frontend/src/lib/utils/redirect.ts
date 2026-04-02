export function validateRedirect(url: string | null, fallback = '/'): string {
	if (!url) return fallback;
	if (!url.startsWith('/') || url.startsWith('//')) return fallback;
	return url;
}

export default validateRedirect;