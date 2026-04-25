<script lang="ts">
	import * as Item from '$lib/components/ui/item/index.js';
	import * as m from '$lib/paraglide/messages.js';
	import { goto } from '$app/navigation';
	import { getDiscountedPrice } from '$lib/stores/coupon.svelte';
	import Price from './Price.svelte';
	import Authors from './Authors.svelte';
	import CartQuantityControl from '$lib/components/CartQuantityControl.svelte';
	import { page } from '$app/state';
	import { getLocale } from '$lib/paraglide/runtime';
	import WishlistToggle from './WishlistToggle.svelte';
	import { MediaQuery } from 'svelte/reactivity';
	import { PUBLIC_STORAGE_URL } from '$env/static/public';
	import DataTableActions from './books/data-table-actions.svelte';

	const { book, discounts = [], onDelete = null } = $props();

	const discountedPrice = $derived(
		getDiscountedPrice(book, discounts, page.data.user?.id as string)
	);
</script>

<Item.Root>
	<Item.Header class="hidden cursor-pointer md:block" onclick={() => goto(`/books/${book.id}`)}>
		{#if book.img_path}
			<img
				src="{PUBLIC_STORAGE_URL}/{book.img_path}"
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
				src="{PUBLIC_STORAGE_URL}/{book.img_path}"
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
		<Item.Description class="text-xs">
			<p>
				<a
					class="transition-colors hover:text-accent-foreground"
					href="/books/?genre={book.genre.id}">{book.genre[`name_${getLocale()}`]}</a
				>
				·
				<a
					class="transition-colors hover:text-accent-foreground"
					href="/books/?publisher={book.publisher.id}">{book.publisher.name}</a
				>
				· {book.release_year}
			</p>
		</Item.Description>
		<Item.Description class="text-xs">{m['pages']({ pages: book.pages })}</Item.Description>
		<Item.Description class="text-xs"
			><Authors classes="text-xs" authors={book.authors} /></Item.Description
		>
		<Item.Description class="mt-2">
			<Price price={book.price} {discountedPrice} />
			{#if page.data.user && page.data.user?.role !== 'customer'}
				{m['in_stock']({ count: book.stock })}
			{/if}
		</Item.Description>
	</Item.Content>
	<Item.Actions
		class="flex w-full flex-col justify-center gap-2 md:flex md:flex-row md:flex-wrap md:justify-start"
	>
		{#if !page.data.user || page.data.user?.role === 'customer'}
			<CartQuantityControl {book} />
			<WishlistToggle {book} showLabel={new MediaQuery('(max-width: 768px)').current} />
		{/if}
		{#if page.data.user && ['manager', 'admin'].includes(page.data.user?.role)}
			<DataTableActions {book} {onDelete} />
		{/if}
	</Item.Actions>
</Item.Root>
