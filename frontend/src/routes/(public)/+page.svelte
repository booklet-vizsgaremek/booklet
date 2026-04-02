<script>
	import * as m from '$lib/paraglide/messages.js';
	import BookCarousel from '$lib/components/BookCarousel.svelte';
	const { data } = $props();
</script>

<section class="flex h-max w-full flex-col items-center gap-6 px-12 pt-20 pb-48 md:items-start">
	<h1 class="text-2xl">{m['title.home']()}</h1>
	<BookCarousel books={data.books} topList={true} />
</section>
