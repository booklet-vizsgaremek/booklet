<script lang="ts">
	import { MediaQuery } from 'svelte/reactivity';
	import * as Item from '$lib/components/ui/item/index.js';
	import { buttonVariants } from '$lib/components/ui/button';
	import Bookmark from '@lucide/svelte/icons/bookmark';
	import * as m from '$lib/paraglide/messages.js';
	import * as Tooltip from '$lib/components/ui/tooltip/index.js';
	import * as AlertDialog from '$lib/components/ui/alert-dialog/index.js';
	import { goto } from '$app/navigation';
	import { getDiscountedPrice } from '$lib/stores/coupon.svelte';
	import Price from './Price.svelte';
	import Authors from './Authors.svelte';
	import CartQuantityControl from '$lib/components/CartQuantityControl.svelte';
	import { page } from '$app/state';
	import { toast } from 'svelte-sonner';
	import { wishlist } from '$lib/stores/wishlist.svelte';
	import useDarkMode from '$lib/stores/darkMode.svelte';

	const { book, discounts = [] } = $props();
	const dark = useDarkMode();

	const isWishlisted = $derived(wishlist.isWishlisted(book.id));

	const handleWishlistToggle = async () => {
		if (!page.data.user) {
			toast.error(m['messages.signin_to_continue']());
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
	};

	let toggleDialogOpen = $state(false);
	const discountedPrice = $derived(
		getDiscountedPrice(book, discounts, page.data.user?.id as string)
	);
	const isDesktop = new MediaQuery('(min-width: 768px)');
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
			<AlertDialog.Cancel class="cursor-pointer">{m['actions.cancel']()}</AlertDialog.Cancel>
			<AlertDialog.Action class="cursor-pointer" onclick={handleWishlistToggle}>
				{m['actions.remove']()}
			</AlertDialog.Action>
		</AlertDialog.Footer>
	</AlertDialog.Content>
</AlertDialog.Root>

<Item.Root>
	<Item.Header class="hidden cursor-pointer md:block" onclick={() => goto(`/books/${book.id}`)}>
		{#if book.img_path}
			<img
				src={book.img_path}
				alt={m['accessibility.book_cover']()}
				class="aspect-2/3 w-full object-cover"
			/>
		{:else}
			<div
				class="flex aspect-2/3 w-full items-start justify-center bg-blue-950 object-cover font-bold text-black"
			>
				<p class="relative top-1/4 bg-white p-1 text-center dark:bg-foreground">{book.title}</p>
			</div>
		{/if}
	</Item.Header>
	<Item.Media
		class="size-16! h-24! md:hidden"
		variant="image"
		onclick={() => goto(`/books/${book.id}`)}
	>
		{#if book.img_path}
			<img
				src={book.img_path}
				alt={m['accessibility.book_cover']()}
				class="aspect-2/3 w-full object-cover"
			/>
		{:else}
			<div
				class="flex aspect-2/3 w-full items-start justify-center bg-blue-950 object-cover font-bold text-black"
			>
				<p
					class="relative top-1/4 bg-white p-1 text-center text-[25%] md:text-base dark:bg-foreground"
				>
					{book.title}
				</p>
			</div>
		{/if}
	</Item.Media>
	<Item.Content>
		<a href={`/books/${book.id}`}><Item.Title>{book.title}</Item.Title></a>
		<Item.Description class="text-xs">
			<p>
				<a
					class="transition-colors hover:text-accent-foreground"
					href="/books/?genre={book.genre.id}">{book.genre.name}</a
				>
				·
				<a
					class="transition-colors hover:text-accent-foreground"
					href="/books/?publisher={book.publisher.id}">{book.publisher.name}</a
				>
				· {book.release_year}
			</p>
		</Item.Description>
		<Item.Description class="text-xs">{m['pages']({ pages: book.pages })}</Item.Description>
		<Item.Description class="text-xs"
			><Authors classes="text-xs" authors={book.authors} /></Item.Description
		>
		<Item.Description class="mt-2">
			<Price price={book.price} {discountedPrice} />
		</Item.Description>
	</Item.Content>
	<Item.Actions
		class="flex w-full flex-col justify-center gap-2 md:flex md:flex-row md:flex-wrap md:justify-start"
	>
		<CartQuantityControl {book} />
		<Tooltip.Root>
			<Tooltip.Trigger
				class={'w-full cursor-pointer md:w-max ' + buttonVariants({ variant: 'outline' })}
				onclick={() => (isWishlisted ? (toggleDialogOpen = true) : handleWishlistToggle())}
			>
				<Bookmark fill={isWishlisted ? (dark.darkMode ? '#fff' : '#000') : 'transparent'} />
				{#if !isDesktop.current}
					{isWishlisted ? m['actions.remove_from_wishlist']() : m['actions.add_to_wishlist']()}
				{/if}
			</Tooltip.Trigger>
			<Tooltip.Content>
				<p>
					{isWishlisted ? m['actions.remove_from_wishlist']() : m['actions.add_to_wishlist']()}
				</p>
			</Tooltip.Content>
		</Tooltip.Root>
	</Item.Actions>
</Item.Root>
