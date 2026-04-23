<script lang="ts">
	import * as m from '$lib/paraglide/messages.js';
	import { Button } from '$lib/components/ui/button';
	import { BookOpen, Tag, Calendar, ChevronLeft, Building } from '@lucide/svelte';
	import CartQuantityControl from '$lib/components/CartQuantityControl.svelte';
	import { getDiscountedPrice } from '$lib/stores/coupon.svelte';
	import Price from '$lib/components/Price.svelte';
	import Authors from '$lib/components/Authors.svelte';
	import { page } from '$app/state';
	import { getLocale } from '$lib/paraglide/runtime.js';
	import BookCarousel from '$lib/components/BookCarousel.svelte';
	import WishlistToggle from '$lib/components/WishlistToggle.svelte';
	import { goto } from '$app/navigation';

	const { data } = $props();
	const book = $derived(data.book);
	const discountedPrice = $derived(getDiscountedPrice(book, data.discounts ?? [], ''));
</script>

<div class="container mx-auto px-12 pt-18 pb-32 md:px-24">
	<Button
		variant="link"
		class="mb-8 cursor-pointer hover:no-underline md:-ml-12"
		onclick={() => history.back()}
	>
		<ChevronLeft />
		{m['navigation.back']()}
	</Button>
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
				<Button
					onclick={() => goto(`/books?publisher=${book.publisher.id}`)}
					variant="link"
					class="flex h-max w-max cursor-pointer items-center gap-2 p-0! text-muted-foreground hover:text-foreground hover:no-underline"
				>
					<Building size={16} />
					{book.publisher.name}
				</Button>
				<Button
					onclick={() => goto(`/books?genre=${book.genre.id}`)}
					variant="link"
					class="flex h-max w-max cursor-pointer items-center gap-2 p-0! text-muted-foreground hover:text-foreground hover:no-underline"
				>
					<Tag size={16} />
					{book.genre[`name_${getLocale()}`]}
				</Button>
			</div>
			<div class="flex flex-col gap-2 md:flex-row">
				{#if !page.data.user || page.data.user?.role === 'customer'}
					<CartQuantityControl {book} showLabel={true} />
					<WishlistToggle {book} showLabel={true} />
				{:else}
					<p>{m['in_stock']({ count: book.stock })}</p>
				{/if}
			</div>
		</div>
	</div>
	<div class="mt-24 flex h-max w-full flex-col items-center gap-6 md:items-start">
		<h2 class="text-2xl">
			{m['more_books_in_genre']({
				genre_en: data.book.genre.name_en,
				genre_hu: data.book.genre.name_hu
			})}
		</h2>
		<BookCarousel books={data.booksFromGenre} discounts={data.discounts} />
	</div>
</div>
