<script>
	import { page } from '$app/state';
	import {
		PUBLIC_APP_NAME,
		PUBLIC_EMAIL_ADDRESS,
		PUBLIC_LOCATION,
		PUBLIC_PHONE_NUMBER
	} from '$env/static/public';
	import * as m from '$lib/paraglide/messages.js';

	const noFooterPaths = ['/sign-in', '/sign-up', '/checkout'];
	const visible = $derived(!noFooterPaths.some((path) => page.url.pathname.startsWith(path)));
</script>

{#if visible}
	<footer
		class="flex h-max flex-col items-start justify-center bg-white px-12 py-16 text-black md:px-24 dark:bg-neutral-950 dark:text-white"
	>
		<h2 class="text-2xl">{m['footer.contact']()}</h2>
		<div class="my-6 flex flex-col text-sm md:*:text-base">
			<a href="tel:{PUBLIC_PHONE_NUMBER.replaceAll(' ', '')}"
				>{m['footer.contacts.phone_number']({ number: PUBLIC_PHONE_NUMBER })}</a
			>
			<a href="mailto:{PUBLIC_EMAIL_ADDRESS}"
				>{m['footer.contacts.email']({ email: PUBLIC_EMAIL_ADDRESS })}</a
			>
			<p>{m['footer.contacts.location']({ location: PUBLIC_LOCATION })}</p>
		</div>
		<p class="text-sm md:text-base">
			&copy; {new Date().getFullYear()}
			{PUBLIC_APP_NAME}
			{m['branding']()}.
			{m['footer.all_rights_reserved']()}.
		</p>
	</footer>
{/if}
