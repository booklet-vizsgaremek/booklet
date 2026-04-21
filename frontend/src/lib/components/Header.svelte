<script lang="ts">
	import * as m from '$lib/paraglide/messages.js';
	import { page } from '$app/state';
	import { onMount } from 'svelte';
	import clsx from 'clsx';
	import { twMerge } from 'tailwind-merge';
	import { fly } from 'svelte/transition';
	import LocaleSwitcher from './LocaleSwitcher.svelte';
	import HeaderLogo from './HeaderLogo.svelte';
	import { getLocale } from '$lib/paraglide/runtime';
	import ShoppingCart from '@lucide/svelte/icons/shopping-cart';
	import { cart } from '$lib/stores/cart.svelte';
	import Badge from '$lib/components/ui/badge/badge.svelte';
	import SignOut from '$lib/components/SignOut.svelte';

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

	let { user } = $props();

	const links = $derived(() => {
		if (user) {
			switch (user.role) {
				default:
				case 'customer':
					return [
						{
							title: m['title.book_lookup'](),
							url: '/books',
							isSpecial: false
						},
						{
							title:
								getLocale() == 'hu'
									? `${user.last_name} ${user.first_name}`
									: `${user.first_name} ${user.last_name}`,
							url: '/profile',
							isSpecial: true
						}
					];
					break;
				case 'admin':
				case 'manager':
				case 'staff':
					return [];
					break;
			}
		} else {
			return [
				{
					title: m['title.book_lookup'](),
					url: '/books',
					isSpecial: false
				},
				{
					title: m['auth.sign_in'](),
					url: `/sign-in${page.url.pathname !== '/' ? `?redirect=${page.url.pathname}&ref=signin` : '?ref=signin'}`,
					isSpecial: true
				}
			];
		}
	});

	let navClass = $derived(
		twMerge(
			clsx(
				'flex',
				isMobileOrTouch &&
					'fixed left-0 z-50 h-[calc(100vh-5rem)] w-full flex-col justify-center gap-24 bg-white p-24 transition-[bottom,opacity]! duration-500 md:p-56 dark:bg-neutral-950',
				!isMobileOrTouch && 'h-full flex-row items-center',
				isMobileOrTouch &&
					(navOpen ? 'bottom-0 opacity-100' : '-bottom-full opacity-0 pointer-events-none')
			)
		)
	);

	let linkClass = $derived((isSpecial: boolean) =>
		twMerge(
			clsx(
				isMobileOrTouch && 'p-4',
				!isMobileOrTouch && 'flex h-full items-center px-8',
				isSpecial && 'bg-amber-300 hover:bg-amber-200 dark:text-black',
				!isMobileOrTouch && !isSpecial && 'hover:bg-background'
			)
		)
	);

	const noHeaderPaths = ['/sign-in', '/sign-up', '/checkout'];
	const visible = $derived(!noHeaderPaths.some((path) => page.url.pathname.startsWith(path)));
</script>

<svelte:window bind:innerWidth={w} />

{#if w !== 0 && visible}
	<header
		transition:fly={{ y: -80 }}
		class="fixed top-0 z-50 flex h-20 w-full flex-row items-center justify-between bg-white px-12 text-black md:px-24 dark:bg-neutral-950 dark:text-white"
	>
		<a href="/">
			<HeaderLogo {isMobile} />
		</a>

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
			{#each links() as link}
				<a href={link.url} class={linkClass(link.isSpecial)}>{link.title}</a>
			{/each}

			{#if page.data.user && page.data.user?.role !== 'customer'}
				<SignOut class={linkClass(false)} />
			{/if}

			<LocaleSwitcher
				classes="border-0 bg-transparent md:h-full md:px-8 p-4 md:py-0 hover:bg-background! cursor-pointer"
			/>

			{#if !page.data.user || page.data.user?.role === 'customer'}
				<a href="/checkout" class={`${linkClass(false)} flex flex-row justify-between gap-1`}>
					<ShoppingCart />
					{#if cart.itemCount > 0}
						<Badge class="h-5 w-5 text-xs">{cart.itemCount > 99 ? '99+' : cart.itemCount}</Badge>
					{/if}
				</a>
			{/if}
		</nav>
	</header>
{/if}
