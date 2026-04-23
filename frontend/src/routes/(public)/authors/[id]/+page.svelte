<script lang="ts">
	import type { Author } from '$lib/types';
	import { page } from '$app/state';
	import { getLocale } from '$lib/paraglide/runtime';
	import * as m from '$lib/paraglide/messages.js';
	import * as Empty from '$lib/components/ui/empty';
	import { ChevronRight, Pen } from '@lucide/svelte';
	import Button from '$lib/components/ui/button/button.svelte';
	import BookItem from '$lib/components/BookItem.svelte';
	import { goto } from '$app/navigation';

	export let data: { author: Author };
</script>

<div class="mx-auto w-full px-4 pt-16! pb-12 md:w-4/5 md:px-0 md:pb-24">
	<h1 class="mb-4 text-3xl">{data.author.name}</h1>
	<p>{data.author[`biography_${getLocale()}`]}</p>
	<div class="mt-8">
		{#if data.author.books.length === 0}
			<Empty.Root class="w-full border">
				<Empty.Media variant="icon">
					<Pen />
				</Empty.Media>
				<Empty.Header>
					<Empty.Title class="text-xl">{m['no_books']()}</Empty.Title>
				</Empty.Header>
				<Empty.Content>
					<Button
						variant="outline"
						class="cursor-pointer"
						size="sm"
						onclick={() => {
							goto('/');
						}}
					>
						{m['navigation.back_to_home']()}
					</Button>
				</Empty.Content>
			</Empty.Root>
		{:else}
			<h2 class="mb-4 text-2xl">{m['book_count']({ count: data.author.books.length })}</h2>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
				{#each data.author.books as book (book.id)}
					<BookItem {book} discounts={page.data.discounts} />
				{/each}
				<div class="mt-12 flex w-full items-center justify-center px-6 md:m-0 md:h-full">
					<Button
						class="w-full cursor-pointer"
						onclick={() => goto(`/books?author=${data.author.id}`)}
					>
						{m['show_all']()}
						<ChevronRight />
					</Button>
				</div>
			</div>
		{/if}
	</div>
</div>
