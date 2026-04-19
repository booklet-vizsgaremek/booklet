<script>
	import * as m from '$lib/paraglide/messages.js';
	import BookCarousel from '$lib/components/BookCarousel.svelte';
	const { data } = $props();
</script>

<section class="flex h-max w-full flex-col items-center gap-6 px-12 py-20 md:items-start">
	<h1 class="text-2xl">{m['home.top_10']()}</h1>
	<BookCarousel books={data.topPurchased} topList={true} discounts={data.discounts} />
</section>
<section class="flex h-max w-full flex-col items-center gap-6 px-12 py-20 md:items-start">
	<h1 class="text-2xl">{m['home.random_category']()}</h1>
	<BookCarousel books={data.randomCategory} discounts={data.discounts} />
</section>
<section class="flex h-max w-full flex-col items-center gap-6 px-12 pt-20 pb-48 md:items-start">
	<h1 class="text-2xl">{m['home.discounted']()}</h1>
	<BookCarousel books={data.discounted} discounts={data.discounts} />
</section>
