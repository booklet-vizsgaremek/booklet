<script lang="ts">
	import { buttonVariants } from '$lib/components/ui/button';
	import Bookmark from '@lucide/svelte/icons/bookmark';
	import * as m from '$lib/paraglide/messages.js';
	import * as Tooltip from '$lib/components/ui/tooltip/index.js';
	import * as AlertDialog from '$lib/components/ui/alert-dialog/index.js';
	import { page } from '$app/state';
	import { toast } from 'svelte-sonner';
	import { wishlist } from '$lib/stores/wishlist.svelte';
	import useDarkMode from '$lib/stores/darkMode.svelte';
	import type { Book } from '$lib/types';
	import Spinner from './ui/spinner/spinner.svelte';

	const { book, showLabel = false }: { book: Book; showLabel?: boolean } = $props();

	const dark = useDarkMode();
	const isWishlisted = $derived(wishlist.isWishlisted(book.id));

	const handleWishlistToggle = async () => {
		isLoading = true;
		if (!page.data.user) {
			toast.error(m['messages.signin_for_action']());
			isLoading = false;
			return;
		}
		try {
			const result = await wishlist.toggle(book);
			toast.success(
				result === 'added' ? m['actions.added_to_wishlist']() : m['actions.removed_from_wishlist']()
			);
		} catch {
			toast.error(m['messages.server_error']());
		}
		toggleDialogOpen = false;
		isLoading = false;
	};

	let toggleDialogOpen = $state(false);
	let isLoading = $state(false);
</script>

<AlertDialog.Root bind:open={toggleDialogOpen}>
	<AlertDialog.Content>
		<AlertDialog.Header>
			<AlertDialog.Title>{m['actions.remove_from_wishlist_dialog.title']()}</AlertDialog.Title>
			<AlertDialog.Description>
				{m['actions.remove_from_wishlist_dialog.description']({ title: book.title })}
			</AlertDialog.Description>
		</AlertDialog.Header>
		<AlertDialog.Footer>
			{#if !isLoading}
				<AlertDialog.Cancel class="cursor-pointer">{m['actions.cancel']()}</AlertDialog.Cancel>
			{/if}
			<AlertDialog.Action
				class="cursor-pointer"
				onclick={handleWishlistToggle}
				disabled={isLoading}
			>
				{#if isLoading}
					<Spinner />
				{:else}
					{m['actions.remove']()}
				{/if}
			</AlertDialog.Action>
		</AlertDialog.Footer>
	</AlertDialog.Content>
</AlertDialog.Root>

<Tooltip.Root>
	<Tooltip.Trigger
		class={'w-full cursor-pointer md:w-max ' + buttonVariants({ variant: 'outline' })}
		onclick={() => (isWishlisted ? (toggleDialogOpen = true) : handleWishlistToggle())}
		disabled={!page.data.user}
	>
		<Bookmark fill={isWishlisted ? (dark.darkMode ? '#fff' : '#000') : 'transparent'} />
		{#if showLabel}
			{isWishlisted ? m['actions.remove_from_wishlist']() : m['actions.add_to_wishlist']()}
		{/if}
	</Tooltip.Trigger>
	{#if !showLabel}
		<Tooltip.Content>
			<p>
				{isWishlisted ? m['actions.remove_from_wishlist']() : m['actions.add_to_wishlist']()}
			</p>
		</Tooltip.Content>
	{/if}
</Tooltip.Root>
