import { expect, test } from '@playwright/test';

test('home page has expected h1', async ({ page }) => {
	await page.goto('http://frontend.vm1.test/');
	await expect(page.getByText('Top 10 most purchased books')).toBeVisible();
});


test('home page has expected header', async ({ page }) => {
	await page.goto('http://frontend.vm1.test/');
	await expect(page.locator('header')).toBeVisible();
});




test('home page has expected sign in button', async ({ page }) => {
	await page.goto('http://frontend.vm1.test/');
	await expect(page.getByText('Sign in')).toBeVisible();
});



test('home page has expected book lookup button', async ({ page }) => {
	await page.goto('http://frontend.vm1.test/');
	await expect(page.getByText('Book lookup')).toBeVisible();
});

test('home page has expected 10 most purchased books', async ({ page }) => {
	await page.goto('http://frontend.vm1.test/');

	const elements = page.locator('h1.ml-4');
	const counted = await elements.count();

	expect(counted).toBe(10);
});



