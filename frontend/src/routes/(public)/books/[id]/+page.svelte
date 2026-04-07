<script lang="ts">
	import * as m from '$lib/paraglide/messages.js';
	import { getLocale } from '$lib/paraglide/runtime';
	import { cart, MAX_QUANTITY_PER_ITEM } from '$lib/stores/cart.svelte';
	import { toast } from 'svelte-sonner';
	import { Button } from '$lib/components/ui/button';
	import { ShoppingCart, Bookmark, BookOpen, Pen, Tag } from '@lucide/svelte';

	const { data } = $props();
	const book = $derived(data.book);

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
				<p class="mt-2 text-muted-foreground">
					{new Intl.ListFormat(getLocale(), { style: 'long', type: 'conjunction' }).format(
						book.authors.map(
							(x: { first_name: string; last_name: string }) => `${x.first_name} ${x.last_name}`
						)
					)}
				</p>
			</div>

			<p class="text-2xl font-semibold">
				{new Intl.NumberFormat(getLocale(), {
					style: 'currency',
					currency: 'HUF',
					maximumFractionDigits: 0
				}).format(book.price)}
			</p>

			<div class="flex flex-col gap-2 text-sm text-muted-foreground">
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

			<div class="flex gap-4">
				<Button
					class="cursor-pointer"
					onclick={handleAddToCart}
					disabled={atLimit || book.stock === 0}
				>
					<ShoppingCart />
					{book.stock === 0 ? m['out_of_stock']() : m['actions.add_to_cart']()}
				</Button>
				<Button variant="outline">
					<Bookmark />
					{m['actions.add_to_wishlist']()}
				</Button>
			</div>
		</div>
	</div>
</div>
