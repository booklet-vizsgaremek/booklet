<script lang="ts">
	import * as Carousel from '$lib/components/ui/carousel/index.js';
	import BookItem from './BookItem.svelte';
	import Button from './ui/button/button.svelte';
	import * as m from '$lib/paraglide/messages.js';

	const { books, discounts = [], topList = false } = $props();
	let showAll = $state(false);
</script>

<div class="grid grid-cols-1 gap-12 md:hidden">
	{#each books as book, i}
		{#if showAll || (i < 3 && !showAll)}
			<div class="flex flex-row gap-1">
				{#if topList}
					<h1 class="mt-2 text-xl">#{i + 1}</h1>
				{/if}
				<BookItem {book} {discounts} />
			</div>
		{/if}
	{/each}
</div>

{#if !showAll}
	<li class="flex justify-center pt-2 md:hidden">
		<Button onclick={() => (showAll = true)}>{m['show_all']()}</Button>
	</li>
{/if}

<Carousel.Root
	class={`hidden w-full max-w-[calc(100%-(24*0.25rem))] md:block${books.length < 5 ? ' lg:m-0' : books.length < 3 ? ' md:m-0' : ' md:ml-12'}`}
>
	<Carousel.Content class="-ms-1 items-stretch">
		{#each books as book, i}
			<Carousel.Item class="h-full p-1 md:basis-1/3 lg:basis-1/5">
				{#if topList}
					<h1 class="ml-4 text-xl">#{i + 1}</h1>
				{/if}
				<BookItem {book} {discounts} />
			</Carousel.Item>
		{/each}
	</Carousel.Content>
	<Carousel.Previous
		class={`cursor-pointer rounded-none${books.length < 3 ? ' md:hidden' : ''}${books.length < 5 ? ' lg:hidden' : ''}`}
	/>
	<Carousel.Next
		class={`cursor-pointer rounded-none${books.length < 3 ? ' md:hidden' : ''}${books.length < 5 ? ' lg:hidden' : ''}`}
	/>
</Carousel.Root>
