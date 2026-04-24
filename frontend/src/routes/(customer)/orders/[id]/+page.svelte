<script lang="ts">
	import type { Book } from '$lib/types';
	import OrderStatusBadge from '$lib/components/OrderStatusBadge.svelte';
	import * as Item from '$lib/components/ui/item/index.js';
	import * as m from '$lib/paraglide/messages.js';
	import { getDiscountedPrice, getCartTotal } from '$lib/stores/coupon.svelte';
	import Price, { formatCurrency } from '$lib/components/Price.svelte';
	import { ChevronLeft } from '@lucide/svelte';
	import { navigating } from '$app/state';
	import { goto, invalidateAll } from '$app/navigation';
	import Button from '$lib/components/ui/button/button.svelte';
	import * as AlertDialog from '$lib/components/ui/alert-dialog';
	import { enhance } from '$app/forms';
	import Spinner from '$lib/components/ui/spinner/spinner.svelte';
	import { toast } from 'svelte-sonner';

	let { data } = $props();

	let orderCancelDialogOpen = $state(false);
	let isOrderCancelLoading = $state(false);

	let fromCheckout = navigating.from?.url.pathname.startsWith('/checkout');
</script>

<AlertDialog.Root bind:open={orderCancelDialogOpen}>
	<AlertDialog.Content>
		<AlertDialog.Header>
			<AlertDialog.Title>{m['orders.cancel_order_dialog.title']()}</AlertDialog.Title>
			<AlertDialog.Description>
				{m['orders.cancel_order_dialog.description']()}
			</AlertDialog.Description>
		</AlertDialog.Header>
		<AlertDialog.Footer>
			{#if !isOrderCancelLoading}
				<AlertDialog.Cancel class="cursor-pointer">{m['actions.cancel']()}</AlertDialog.Cancel>
			{/if}
			<form
				method="POST"
				use:enhance={async ({ formData }) => {
					isOrderCancelLoading = true;
					formData.append('pickupId', data.receipt.pickup.id);
					return async ({ result }) => {
						if (result.type === 'success') {
							invalidateAll();
							toast.success(m['messages.successful_order_cancel']());
						} else if (result.type === 'failure' && result.data?.error) {
							toast.error(result.data.error as string);
						}
						isOrderCancelLoading = false;
						orderCancelDialogOpen = false;
					};
				}}
			>
				<AlertDialog.Action
					class="w-full cursor-pointer"
					variant="destructive"
					disabled={isOrderCancelLoading || data.receipt.pickup.status !== 'pending'}
				>
					{#if isOrderCancelLoading}
						<Spinner />
					{:else}
						{m['orders.cancel']()}
					{/if}
				</AlertDialog.Action>
			</form>
		</AlertDialog.Footer>
	</AlertDialog.Content>
</AlertDialog.Root>

<div class="mx-auto w-full px-4 pt-16! pb-56 md:w-4/5 md:px-0">
	<div class="mb-12 flex flex-col md:mb-0 md:flex-row md:justify-between">
		<div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:gap-2">
			<button
				class="cursor-pointer"
				onclick={() => {
					if (fromCheckout) goto('/orders');
					else history.back();
				}}><ChevronLeft /></button
			>
			<h1 class="text-3xl">
				{m['title.order']({ id: data.receipt.id })}
			</h1>
			<OrderStatusBadge status={data.receipt.pickup.status} />
		</div>
		<Button
			onclick={() => (orderCancelDialogOpen = true)}
			type="submit"
			class="w-full cursor-pointer md:w-max"
			disabled={data.receipt.pickup.status !== 'pending'}
		>
			{m['orders.cancel']()}
		</Button>
	</div>
	<Item.Group class="gap-0">
		{#each data.receipt.books as book, i}
			<Item.Root class="flex-col *:w-full md:flex-row md:*:w-auto">
				<Item.Media class="w-auto!">
					<a href="/books/{book.id}">
						{#if book.img_path}
							<img
								src={book.img_path}
								alt={m['accessibility.book_cover']()}
								class="aspect-2/3 w-16 object-cover"
							/>
						{:else}
							<div
								class="flex aspect-2/3 w-16 items-start justify-center bg-blue-950 font-bold text-black"
							>
								<p class="relative top-1/4 bg-white p-px text-center text-[20%] dark:bg-foreground">
									{book.title}
								</p>
							</div>
						{/if}
					</a>
				</Item.Media>
				<Item.Content class="gap-1">
					<Item.Title><a href="/books/{book.id}">{book.title}</a></Item.Title>
					<Item.Description>{m['orders.quantity']({ quantity: book.quantity })}</Item.Description>
				</Item.Content>
				<Item.Actions>
					{@const discountedPrice = getDiscountedPrice(
						{
							...book,
							price: (book as Book & { price_at_purchase: number }).price_at_purchase
						},
						data.receipt.coupons,
						data.user?.id as string
					)}
					<Price price={book.price_at_purchase} {discountedPrice} quantity={book.quantity} />
				</Item.Actions>
			</Item.Root>
			{#if i !== data.receipt.books.length - 1}
				<Item.Separator class="m-0" />
			{/if}
		{/each}
	</Item.Group>
	<h1 class="float-right mt-12 text-2xl">
		{m['cart.total']()}:
		{formatCurrency(
			getCartTotal(data.receipt.books, data.receipt.coupons, data.user?.id as string)
		)}
	</h1>
</div>
