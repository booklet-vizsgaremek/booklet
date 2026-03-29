<script lang="ts">
	import * as Item from '$lib/components/ui/item/index.js';
	import { buttonVariants } from '$lib/components/ui/button';
	import ShoppingCart from '@lucide/svelte/icons/shopping-cart';
	import Bookmark from '@lucide/svelte/icons/bookmark';
	import * as m from '$lib/paraglide/messages.js';
	import { getLocale } from '$lib/paraglide/runtime';
	import * as Tooltip from '$lib/components/ui/tooltip/index.js';

	const { book } = $props();
</script>

<Item.Root>
	<Item.Header class="hidden md:block">
		{#if book.img_path}
			<img
				src={book.img_path}
				alt={m['accessibility.book_cover']()}
				class="aspect-2/3 w-full object-cover"
			/>
		{:else}
			<div
				class="flex aspect-2/3 w-full items-start justify-center bg-blue-950 object-cover font-bold text-black"
			>
				<p class="relative top-1/4 bg-white p-1 text-center dark:bg-foreground">{book.title}</p>
			</div>
		{/if}
	</Item.Header>
	<Item.Media class="size-16! h-24! md:hidden" variant="image">
		{#if book.img_path}
			<img
				src={book.img_path}
				alt={m['accessibility.book_cover']()}
				class="aspect-2/3 w-full object-cover"
			/>
		{:else}
			<div
				class="flex aspect-2/3 w-full items-start justify-center bg-blue-950 object-cover font-bold text-black"
			>
				<p
					class="relative top-1/4 bg-white p-1 text-center text-[25%] md:text-base dark:bg-foreground"
				>
					{book.title}
				</p>
			</div>
		{/if}
	</Item.Media>
	<Item.Content>
		<Item.Title>{book.title}</Item.Title>
		<Item.Description class="text-xs"
			>{new Intl.ListFormat(getLocale(), { style: 'long', type: 'conjunction' }).format(
				book.authors.map(
					(x: { first_name: string; last_name: string }) => `${x.first_name} ${x.last_name}`
				)
			)}</Item.Description
		>
		<Item.Description class="mt-2">
			{new Intl.NumberFormat(getLocale(), {
				style: 'currency',
				currency: 'HUF',
				maximumFractionDigits: 0
			}).format(book.price)}
		</Item.Description>
	</Item.Content>
	<Item.Actions class="flex w-full justify-center md:block">
		{@const triggerClass =
			'mt-2 w-1/2 cursor-pointer md:w-max ' + buttonVariants({ variant: 'default' })}
		<Tooltip.Root>
			<Tooltip.Trigger class={triggerClass}><ShoppingCart /></Tooltip.Trigger>
			<Tooltip.Content>
				<p>{m['actions.add_to_cart']()}</p>
			</Tooltip.Content>
		</Tooltip.Root>
		<Tooltip.Root>
			<Tooltip.Trigger class={triggerClass}><Bookmark /></Tooltip.Trigger>
			<Tooltip.Content>
				<p>{m['actions.add_to_wishlist']()}</p>
			</Tooltip.Content>
		</Tooltip.Root>
	</Item.Actions>
</Item.Root>
