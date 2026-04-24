<script lang="ts">
	import './layout.css';
	import favicon from '$lib/assets/logos/logo_minimal_white.svg';
	import { Header, Footer } from '$lib/components';
	import * as Tooltip from '$lib/components/ui/tooltip/index.js';
	import { PUBLIC_APP_NAME } from '$env/static/public';
	import { page } from '$app/state';
	import * as m from '$lib/paraglide/messages.js';
	import { ModeWatcher } from 'mode-watcher';
	import { Toaster } from '$lib/components/ui/sonner';
	import { navigating } from '$app/state';
	import { browser } from '$app/environment';
	import Spinner from '$lib/components/ui/spinner/spinner.svelte';
	import { wishlist } from '$lib/stores/wishlist.svelte';

	let { children, data } = $props();

	$effect(() => {
		if (data.user) {
			wishlist.init(data.wishlist ?? []);
		} else {
			wishlist.clear();
		}
	});

	let w = $state(0);
	let isMobile = $derived(w < 768);
</script>

<svelte:head>
	<link rel="icon" href={favicon} />
	<title
		>{`${page.data.title ? `${page.data.title} | ` : ''}${PUBLIC_APP_NAME} ${m.branding()}`}</title
	>
</svelte:head>

<svelte:window bind:innerWidth={w} />

<ModeWatcher />
<Toaster
	toastOptions={{
		class: `rounded-none! bg-background! md:bg-white! dark:bg-neutral-950! border-0! shadow-none!${isMobile ? ' mt-20' : ''}`
	}}
	position={isMobile ? 'top-center' : 'bottom-right'}
/>

{#if !browser}
	<div class="fixed top-1/2 left-1/2 flex -translate-1/2 flex-row items-center gap-2">
		<Spinner class="size-8" />
		{m['loading']()}
	</div>
{:else}
	{#if navigating.complete}
		<div
			class="fixed bottom-1/2 left-1/2 z-60 flex -translate-1/2 flex-row items-center gap-2 bg-white px-4 py-2 md:bottom-16 md:left-16 md:translate-0 dark:bg-black"
		>
			<Spinner class="size-8" />
			{m['loading']()}
		</div>
	{/if}
	<div id="app" class={navigating.complete ? 'pointer-events-none opacity-15' : ''}>
		<Header user={data.user} />
		<main class="mt-20 min-h-screen">
			<Tooltip.Provider>
				{@render children()}
			</Tooltip.Provider>
		</main>
		<Footer />
	</div>
{/if}
