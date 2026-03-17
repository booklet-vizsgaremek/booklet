<script lang="ts">
	import './layout.css';
	import favicon from '$lib/assets/logos/logo_minimal_white.svg';
	import { Header, Footer } from '$lib/components';
	import { PUBLIC_APP_NAME } from '$env/static/public';
	import { page } from '$app/state';
	import * as m from '$lib/paraglide/messages.js';
	import { type MessageKey } from '$lib';

	let { children } = $props();
</script>

<svelte:head>
	<link rel="icon" href={favicon} />
	<title
		>{`${(page.data.titleKey as MessageKey | null) ? `${m[page.data.titleKey as MessageKey]()} | ` : ''}${PUBLIC_APP_NAME} ${m.branding()}`}</title
	>
</svelte:head>
<div id="app">
	<Header />
	<main class="mt-20">
		{@render children()}
	</main>
	<Footer />
</div>
