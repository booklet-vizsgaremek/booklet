<script lang="ts">
	import { getLocale } from '$lib/paraglide/runtime';
	import { fade } from 'svelte/transition';

	let darkMode = $state(false);

	$effect(() => {
		const mq = window.matchMedia('(prefers-color-scheme: dark)');
		darkMode = mq.matches;
		const handler = (e: MediaQueryListEvent) => (darkMode = e.matches);
		mq.addEventListener('change', handler);
		return () => mq.removeEventListener('change', handler);
	});

	let logoPromise = $derived(
		import(`$lib/assets/logos/logo_${getLocale()}_${darkMode ? 'white' : 'black'}.svg?url`)
	);
</script>

{#await logoPromise}
	<div class="mb-6 h-12 w-32! animate-pulse bg-background"></div>
{:then logo}
	<img transition:fade={{ delay: 100 }} id="logo" class="mb-6 h-12" src={logo.default} alt="Logo" />
{/await}
