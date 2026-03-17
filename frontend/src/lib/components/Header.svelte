<script lang="ts">
	import { getLocale, locales, setLocale } from '$lib/paraglide/runtime.js';
	import * as m from '$lib/paraglide/messages.js';
	import { page } from '$app/state';
	import { onMount } from 'svelte';
	import clsx from 'clsx';

	let navOpen = $state(false);

	$effect(() => {
		if (page.url) navOpen = false;
	});

	let w = $state(0);
	let isTouchScreen = $state(false);
	let isMobile = $derived(w < 768);
	let isMobileOrTouch = $derived(isMobile || isTouchScreen);

	onMount(() => {
		isTouchScreen = window.matchMedia('(pointer: coarse)').matches;
	});

	let darkMode = $state(false);

	$effect(() => {
		const mq = window.matchMedia('(prefers-color-scheme: dark)');
		darkMode = mq.matches;
		const handler = (e: MediaQueryListEvent) => (darkMode = e.matches);
		mq.addEventListener('change', handler);
		return () => mq.removeEventListener('change', handler);
	});

	let logoPromise = $derived(
		import(
			`$lib/assets/logos/${isMobile ? 'logo_minimal' : `logo_${getLocale()}`}_${darkMode ? 'white' : 'black'}.svg?url`
		)
	);

	const links = [
		{
			title: 'Link',
			url: '#',
			isSpecial: false
		},
		{
			title: 'Link',
			url: '#',
			isSpecial: false
		},
		{
			title: m['auth.sign_in'](),
			url: '#',
			isSpecial: true
		}
	];

	let navClass = $derived(
		clsx(
			'flex',
			isMobileOrTouch &&
				'fixed left-0 z-50 h-[calc(100vh-5rem)] w-full flex-col justify-center gap-24 bg-white p-24 transition-[bottom] duration-500 md:p-56 dark:bg-neutral-950',
			!isMobileOrTouch && 'h-full flex-row items-center',
			isMobileOrTouch && (navOpen ? 'bottom-0' : '-bottom-full')
		)
	);

	let linkClass = $derived((isSpecial: boolean) =>
		clsx(
			isMobileOrTouch && 'p-4',
			!isMobileOrTouch && 'flex h-full items-center px-8',
			isSpecial && 'bg-amber-300 hover:bg-amber-200 dark:text-black',
			!isMobileOrTouch && !isSpecial && 'hover:bg-neutral-100 dark:hover:bg-neutral-900'
		)
	);
</script>

<svelte:window bind:innerWidth={w} />

<header
	class="flex h-20 w-full flex-row items-center justify-between bg-white px-12 text-black md:px-24 dark:bg-neutral-950 dark:text-white"
>
	{#await logoPromise}
		<div
			class="aspect-square h-8/12 animate-pulse bg-neutral-100 md:aspect-auto md:w-36 dark:bg-neutral-900"
		></div>
	{:then logo}
		<img id="logo" alt="Logo" src={logo.default} />
	{/await}

	<button
		aria-label={m['accessibility.nav']()}
		onclick={() => {
			navOpen = !navOpen;
		}}
		tabindex={isMobileOrTouch ? 0 : -1}
		class={isMobileOrTouch ? '' : 'hidden'}
	>
		<span class="material-symbols-sharp text-3xl!">{!navOpen ? 'menu' : 'close'}</span>
	</button>

	<nav class={navClass}>
		{#each links as link}
			<a href={link.url} class={linkClass(link.isSpecial)}>{link.title}</a>
		{/each}

		<select
			onchange={(e) => setLocale(e.currentTarget.value as (typeof locales)[number])}
			class="border-0 bg-transparent md:h-full md:px-8"
		>
			{#each locales as locale}
				<option selected={locale === getLocale()}>{locale.toUpperCase()}</option>
			{/each}
		</select>
	</nav>
</header>
