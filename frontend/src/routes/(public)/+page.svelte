<script>
	import * as m from '$lib/paraglide/messages.js';
	import BookCarousel from '$lib/components/BookCarousel.svelte';
	const { data } = $props();
</script>

<div class="mx-auto flex w-full flex-col gap-24 px-4 pt-16! pb-12 md:w-4/5 md:px-0 md:pb-24">
	<section class="flex h-max w-full flex-col items-center gap-12 md:items-start">
		<h1 class="text-3xl">{m['home.top_10']()}</h1>
		<BookCarousel books={data.topPurchased} topList={true} discounts={data.discounts} />
	</section>
	<section class="flex h-max w-full flex-col items-center gap-12 md:items-start">
		<h1 class="text-3xl">{m['home.random_category']()}</h1>
		<BookCarousel books={data.randomCategory} discounts={data.discounts} />
	</section>
	<section class="flex h-max w-full flex-col items-center gap-12 md:items-start">
		<h1 class="text-3xl">{m['home.discounted']()}</h1>
		<BookCarousel books={data.discounted} discounts={data.discounts} />
	</section>
</div>
