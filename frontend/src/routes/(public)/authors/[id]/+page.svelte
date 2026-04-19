<script lang="ts">
	import type { Author } from '$lib/types';
	import BookCarousel from '$lib/components/BookCarousel.svelte';
	import { page } from '$app/state';
	import { getLocale } from '$lib/paraglide/runtime';
	import * as m from '$lib/paraglide/messages.js';

	export let data: { author: Author };
</script>

<div class="mx-auto w-full px-4 pt-16! pb-12 md:w-4/5 md:px-0 md:pb-24">
	<h1 class="text-3xl">{data.author.name}</h1>
	<p class="mt-4">{data.author[`biography_${getLocale()}`]}</p>
	<div class="mt-8">
		<p class="mb-4 text-3xl">{m['book_count']({ count: data.author.books.length })}</p>
		<BookCarousel books={data.author.books} discounts={page.data.discounts} />
	</div>
</div>
