<script lang="ts">
	import * as m from '$lib/paraglide/messages.js';
	import { Button } from '$lib/components/ui/button';
	import { Bookmark, BookOpen, Pen, Tag, Calendar } from '@lucide/svelte';
	import CartQuantityControl from '$lib/components/CartQuantityControl.svelte';
	import { getDiscountedPrice } from '$lib/stores/coupon.svelte';
	import Price from '$lib/components/Price.svelte';
	import Authors from '$lib/components/Authors.svelte';

	const { data } = $props();
	const book = $derived(data.book);
	const discountedPrice = $derived(getDiscountedPrice(book, data.discounts ?? [], ''));
</script>

<div class="container mx-auto px-12 py-32 md:px-24">
	<div class="flex flex-col gap-12 md:flex-row">
		<div class="w-full shrink-0 md:w-1/4">
			{#if book.img_path}
				<img
					src={book.img_path}
					alt={m['accessibility.book_cover']()}
					class="aspect-2/3 w-full object-cover"
				/>
			{:else}
				<div
					class="flex aspect-2/3 w-full items-start justify-center bg-blue-950 font-bold text-black"
				>
					<p class="relative top-1/4 bg-white p-1 text-center dark:bg-foreground">{book.title}</p>
				</div>
			{/if}
		</div>
		<div class="flex flex-col gap-6">
			<div>
				<h1 class="text-3xl font-bold">{book.title}</h1>
				<Authors classes="mt-2" authors={book.authors} />
			</div>
			<div class="flex items-center gap-3">
				<Price textSize="2xl" price={book.price} {discountedPrice} />
			</div>
			<div class="flex flex-col gap-2 text-sm text-muted-foreground">
				<p class="flex items-center gap-2">
					<Calendar size={16} />
					{book.release_year}
				</p>
				<p class="flex items-center gap-2">
					<BookOpen size={16} />
					{m['pages']({ pages: book.pages })}
				</p>
				<p class="flex items-center gap-2">
					<Pen size={16} />
					{book.publisher.name}
				</p>
				<p class="flex items-center gap-2">
					<Tag size={16} />
					{book.genre.name}
				</p>
			</div>
			<div class="flex flex-col gap-2 md:flex-row">
				<CartQuantityControl {book} showLabel={true} />
				<Button variant="outline">
					<Bookmark />
					{m['actions.add_to_wishlist']()}
				</Button>
			</div>
		</div>
	</div>
</div>
