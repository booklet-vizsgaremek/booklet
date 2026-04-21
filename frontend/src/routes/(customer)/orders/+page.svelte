<script lang="ts">
	import * as Item from '$lib/components/ui/item/index.js';
	import * as Empty from '$lib/components/ui/empty/index.js';
	import { Button } from '$lib/components/ui/button/index.js';
	import { ChevronRight, ReceiptText } from '@lucide/svelte';
	import * as m from '$lib/paraglide/messages.js';
	import { goto } from '$app/navigation';
	import OrderStatusBadge from '$lib/components/OrderStatusBadge.svelte';
	import { Spinner } from '$lib/components/ui/index.js';
	import { toast } from 'svelte-sonner';

	let { data } = $props();

	let receipts = $state<{ id: string; pickup: { status: string } }[]>([]);
	let meta = $state<{ current_page: number; last_page: number }>({
		current_page: 1,
		last_page: 1
	});
	let isLoading = $state(false);

	$effect(() => {
		receipts = [...data.receipts];
		meta = data.meta;
	});

	async function loadMore() {
		if (isLoading || meta.current_page >= meta.last_page) return;

		isLoading = true;

		try {
			const response = await fetch(`/api/receipts?page=${meta.current_page + 1}`);
			if (!response.ok) toast.error(m['messages.server_error']());

			const result = await response.json();
			receipts = [...receipts, ...result.data];
			console.log(receipts);
			meta = result.meta;
		} catch (e) {
			toast.error(m['messages.server_error']());
		} finally {
			isLoading = false;
		}
	}
</script>

<div class="mx-auto w-full px-4 pt-16! pb-12 md:w-4/5 md:px-0 md:pb-24">
	<h1 class="mb-6 text-3xl">{m['title.orders']()}</h1>
	{#if !receipts.length}
		<Empty.Root class="w-full border">
			<Empty.Media variant="icon">
				<ReceiptText />
			</Empty.Media>
			<Empty.Header>
				<Empty.Title class="text-xl">{m['orders.empty']()}</Empty.Title>
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
					{m['navigation.back']()}
				</Button>
			</Empty.Content>
		</Empty.Root>
	{:else}
		<Item.Group class="gap-0">
			{#each receipts as receipt, i}
				<Item.Root>
					{#snippet child({ props })}
						{@const status = receipt.pickup.status}
						<a href={`/orders/${receipt.id}`} {...props}>
							<Item.Content class="gap-1">
								<Item.Title>{m['title.order']({ id: receipt.id })}</Item.Title>
								<Item.Description>
									<OrderStatusBadge {status} />
								</Item.Description>
							</Item.Content>
							<Item.Actions>
								<Button variant="ghost" size="icon" class="rounded-full">
									<ChevronRight />
								</Button>
							</Item.Actions>
						</a>
					{/snippet}
				</Item.Root>
				{#if i !== receipts.length - 1}
					<Item.Separator class="m-0" />
				{/if}
			{/each}
		</Item.Group>
	{/if}
	{#if meta.current_page < meta.last_page}
		<div class="mt-6 flex w-full justify-center">
			<Button class="cursor-pointer" onclick={loadMore} disabled={isLoading}>
				{#if isLoading}
					<Spinner />
				{:else}
					{m['show_more']()}
				{/if}
			</Button>
		</div>
	{/if}
</div>
