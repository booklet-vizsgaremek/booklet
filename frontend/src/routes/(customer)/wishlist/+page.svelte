<script lang="ts">
	import BookItem from '$lib/components/BookItem.svelte';
	import * as Empty from '$lib/components/ui/empty/index.js';
	import { Button } from '$lib/components/ui/button';
	import { Bookmark, ChevronLeft } from '@lucide/svelte';
	import { goto } from '$app/navigation';
	import * as m from '$lib/paraglide/messages.js';
	import { wishlist } from '$lib/stores/wishlist.svelte';
	import { page } from '$app/state';
</script>

<div class="mx-auto w-full px-4 pt-16! pb-12 md:w-4/5 md:px-0 md:pb-24">
	<div class="mb-6 flex flex-row items-center gap-2">
		<button class="cursor-pointer" onclick={() => history.back()}><ChevronLeft /></button>
		<h1 class="text-3xl">{m['title.wishlist']()}</h1>
	</div>
	{#if wishlist.items.length === 0}
		<Empty.Root class="w-full border">
			<Empty.Media variant="icon">
				<Bookmark />
			</Empty.Media>
			<Empty.Header>
				<Empty.Title class="text-xl">{m['wishlist.empty']()}</Empty.Title>
			</Empty.Header>
			<Empty.Content>
				<Button
					variant="outline"
					class="cursor-pointer"
					size="sm"
					onclick={() => {
						goto('/profile');
					}}
				>
					{m['navigation.back_to_profile']()}
				</Button>
			</Empty.Content>
		</Empty.Root>
	{:else}
		<div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
			{#each wishlist.items as book (book.id)}
				<BookItem {book} discounts={page.data.discounts} />
			{/each}
		</div>
	{/if}
</div>
