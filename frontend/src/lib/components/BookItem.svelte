<script lang="ts">
	import * as Item from '$lib/components/ui/item/index.js';
	import { buttonVariants } from '$lib/components/ui/button';
	import ShoppingCart from '@lucide/svelte/icons/shopping-cart';
	import Bookmark from '@lucide/svelte/icons/bookmark';
	import * as m from '$lib/paraglide/messages.js';
	import { getLocale } from '$lib/paraglide/runtime';
	import * as Tooltip from '$lib/components/ui/tooltip/index.js';
	import { goto } from '$app/navigation';
	import { cart, MAX_QUANTITY_PER_ITEM } from '$lib/stores/cart.svelte';
	import { getDiscountedPrice } from '$lib/stores/coupon.svelte';
	import { toast } from 'svelte-sonner';

	const { book, discounts = [] } = $props();

	const discountedPrice = $derived(getDiscountedPrice(book, discounts, ''));
	const cartItem = $derived(cart.items.find((i) => i.id === book.id));
	const atLimit = $derived(
		cartItem ? cartItem.quantity >= Math.min(MAX_QUANTITY_PER_ITEM, book.stock) : false
	);

	const handleAddToCart = () => {
		if (atLimit) {
			toast.error(m['cart.max_quantity']({ max: MAX_QUANTITY_PER_ITEM }));
			return;
		}

		cart.addToCart({
			id: book.id,
			title: book.title,
			price: book.price,
			stock: book.stock,
			genre_id: book.genre.id,
			img_path: book.img_path ?? null,
			quantity: 1
		});

		toast.success(m['actions.added_to_cart']());
	};
</script>

<Item.Root>
	<Item.Header class="hidden cursor-pointer md:block" onclick={() => goto(`/books/${book.id}`)}>
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
	<Item.Media
		class="size-16! h-24! md:hidden"
		variant="image"
		onclick={() => goto(`/books/${book.id}`)}
	>
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
		<a href={`/books/${book.id}`}><Item.Title>{book.title}</Item.Title></a>
		<Item.Description class="text-xs"
			>{new Intl.ListFormat(getLocale(), { style: 'long', type: 'conjunction' }).format(
				book.authors.map(
					(x: { first_name: string; last_name: string }) => `${x.first_name} ${x.last_name}`
				)
			)}</Item.Description
		>
		<Item.Description class="mt-2">
			{#if discountedPrice !== book.price}
				<span class="text-sm text-muted-foreground line-through">
					{new Intl.NumberFormat(getLocale(), {
						style: 'currency',
						currency: 'HUF',
						maximumFractionDigits: 0
					}).format(book.price)}
				</span>
				<span class="text-sm font-semibold text-foreground">
					{new Intl.NumberFormat(getLocale(), {
						style: 'currency',
						currency: 'HUF',
						maximumFractionDigits: 0
					}).format(discountedPrice)}
				</span>
			{:else}
				{new Intl.NumberFormat(getLocale(), {
					style: 'currency',
					currency: 'HUF',
					maximumFractionDigits: 0
				}).format(book.price)}
			{/if}
		</Item.Description>
	</Item.Content>
	<Item.Actions class="flex w-full justify-center md:block">
		{@const triggerClass =
			'mt-2 w-1/2 cursor-pointer md:w-max ' + buttonVariants({ variant: 'default' })}
		<Tooltip.Root>
			<Tooltip.Trigger class={triggerClass} onclick={handleAddToCart} disabled={atLimit}
				><ShoppingCart /></Tooltip.Trigger
			>
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
