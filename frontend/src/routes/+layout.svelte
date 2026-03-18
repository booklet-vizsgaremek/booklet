<script lang="ts">
	import './layout.css';
	import favicon from '$lib/assets/logos/logo_minimal_white.svg';
	import { Header, Footer } from '$lib/components';
	import { PUBLIC_APP_NAME } from '$env/static/public';
	import { page } from '$app/state';
	import * as m from '$lib/paraglide/messages.js';
	import { type MessageKey } from '$lib';
	import { ModeWatcher } from 'mode-watcher';
	import { Toaster } from '$lib/components/ui/sonner/index.js';

	let { children } = $props();

	let w = $state(0);
	let isMobile = $derived(w < 768);
</script>

<svelte:head>
	<link rel="icon" href={favicon} />
	<title
		>{`${(page.data.titleKey as MessageKey | null) ? `${m[page.data.titleKey as MessageKey]()} | ` : ''}${PUBLIC_APP_NAME} ${m.branding()}`}</title
	>
</svelte:head>

<svelte:window bind:innerWidth={w} />

<ModeWatcher />
<Toaster
	toastOptions={{
		class:
			'rounded-none! bg-background! md:bg-white! md:dark:bg-neutral-950! border-0! shadow-none!'
	}}
	position={isMobile ? 'top-center' : 'bottom-right'}
/>

<div id="app">
	<Header />
	<main class="mt-20">
		{@render children()}
	</main>
	<Footer />
</div>
