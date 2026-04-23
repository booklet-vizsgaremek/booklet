<script lang="ts">
	import * as Item from '$lib/components/ui/item/index.js';
	import * as Empty from '$lib/components/ui/empty/index.js';
	import { Button } from '$lib/components/ui/button/index.js';
	import { Input } from '$lib/components/ui/input/index.js';
	import { ChevronRight, ReceiptText, Search } from '@lucide/svelte';
	import * as m from '$lib/paraglide/messages.js';
	import { goto } from '$app/navigation';
	import OrderStatusBadge from '$lib/components/OrderStatusBadge.svelte';
	import { Spinner } from '$lib/components/ui/index.js';
	import { toast } from 'svelte-sonner';
	import { getLocale } from '$lib/paraglide/runtime.js';
	import { type Receipt } from '$lib/types';
	import * as Select from '$lib/components/ui/select';

	let { data } = $props();

	let receipts = $state<Receipt[]>([]);
	let meta = $state<{ current_page: number; last_page: number }>({
		current_page: 1,
		last_page: 1
	});
	let isLoading = $state(false);
	let status = $state('');
	let search = $state('');
	let searchTimeout = $state<ReturnType<typeof setTimeout> | null>(null);

	$effect(() => {
		receipts = [...data.receipts];
		meta = data.meta;
	});

	async function fetchReceipts(page: number, append = false) {
		isLoading = true;
		try {
			const params = new URLSearchParams({ page: String(page) });
			if (search.trim()) params.set('search', search.trim());
			if (status) params.set('status', status);
			const response = await fetch(`/api/staff/receipts?${params}`);
			if (!response.ok) {
				toast.error(m['messages.server_error']());
				return;
			}
			const result = await response.json();
			receipts = append ? [...receipts, ...result.data] : result.data;
			meta = result.meta;
		} catch {
			toast.error(m['messages.server_error']());
		} finally {
			isLoading = false;
		}
	}

	function loadMore() {
		if (isLoading || meta.current_page >= meta.last_page) return;
		fetchReceipts(meta.current_page + 1, true);
	}

	function onSearchInput() {
		if (searchTimeout) clearTimeout(searchTimeout);
		searchTimeout = setTimeout(() => fetchReceipts(1), 300);
	}

	function displayName(user: { first_name: string; last_name: string; email: string } | null) {
		return !user
			? m['orders.deleted_user']()
			: getLocale() === 'hu'
				? `${user.last_name} ${user.first_name}`
				: `${user.first_name} ${user.last_name}`;
	}

	const statusLabels: Record<string, () => string> = {
		pending: m['orders.status.pending'],
		ready: m['orders.status.ready'],
		cancelled: m['orders.status.cancelled'],
		completed: m['orders.status.completed']
	};
</script>

<div class="mx-auto w-full px-4 pt-16! pb-12 md:w-4/5 md:px-0 md:pb-24">
	<div class="mb-6 flex flex-col gap-3">
		<h1 class="text-3xl">{m['title.orders']()}</h1>
		<div class="mt-2 flex flex-col gap-2 md:flex-row md:items-stretch">
			<div class="relative flex-1">
				<Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
				<Input
					type="text"
					placeholder={`${m['orders.search_placeholder']()}...`}
					class="w-full pl-9 md:h-full! md:self-stretch"
					bind:value={search}
					oninput={onSearchInput}
				/>
			</div>
			<Select.Root type="single" bind:value={status} onValueChange={() => fetchReceipts(1)}>
				<Select.Trigger class="h-full! w-auto shrink-0 md:self-stretch" disabled={isLoading}>
					{#if isLoading}
						<Spinner class="size-5" />
					{:else}
						{status ? statusLabels[status]() : m['orders.filter_all']()}
					{/if}
				</Select.Trigger>
				<Select.Content>
					<Select.Item class="cursor-pointer" value="" disabled={status === ''}
						>{m['orders.filter_all']()}</Select.Item
					>
					<Select.Item class="cursor-pointer" value="pending" disabled={status === 'pending'}
						>{m['orders.status.pending']()}</Select.Item
					>
					<Select.Item class="cursor-pointer" value="ready" disabled={status === 'ready'}
						>{m['orders.status.ready']()}</Select.Item
					>
					<Select.Item class="cursor-pointer" value="completed" disabled={status === 'completed'}
						>{m['orders.status.completed']()}</Select.Item
					>
					<Select.Item class="cursor-pointer" value="cancelled" disabled={status === 'cancelled'}
						>{m['orders.status.cancelled']()}</Select.Item
					>
				</Select.Content>
			</Select.Root>
		</div>
	</div>
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
					onclick={async () => {
						status = '';
						search = '';
						await fetchReceipts(1);
					}}
				>
					{m['orders.clear_filters']()}
				</Button>
			</Empty.Content>
		</Empty.Root>
	{:else}
		<Item.Group class="gap-0">
			{#each receipts as receipt, i}
				<Item.Root>
					{#snippet child({ props })}
						{@const receiptStatus = receipt.pickup.status}
						<a href={`/staff/orders/${receipt.id}`} {...props}>
							<Item.Content class="gap-1 overflow-hidden">
								<Item.Title class="flex flex-wrap items-baseline gap-x-1">
									<span class="shrink-0">{m['title.order']({ id: receipt.id })}</span>
								</Item.Title>
								<Item.Description
									class="flex flex-col flex-wrap justify-center gap-2 md:flex-row-reverse md:items-center md:justify-end"
								>
									<span class="text-xs text-muted-foreground">
										{new Date(receipt.pickup.completed_at ?? receipt.date).toLocaleDateString()}
										·
										{displayName(receipt.user)} ({receipt.user.email})
									</span>
									<OrderStatusBadge status={receiptStatus} />
								</Item.Description>
							</Item.Content>
							<Item.Actions>
								<Button variant="ghost" size="icon" class="shrink-0 rounded-full">
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
