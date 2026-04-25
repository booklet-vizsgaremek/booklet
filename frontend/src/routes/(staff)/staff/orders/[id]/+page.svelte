<script lang="ts">
	import type { Receipt } from '$lib/types';
	import OrderStatusBadge from '$lib/components/OrderStatusBadge.svelte';
	import * as Item from '$lib/components/ui/item/index.js';
	import * as m from '$lib/paraglide/messages.js';
	import * as Select from '$lib/components/ui/select/index.js';
	import { Spinner } from '$lib/components/ui/index.js';
	import { toast } from 'svelte-sonner';
	import { invalidateAll } from '$app/navigation';
	import { getLocale } from '$lib/paraglide/runtime';
	import { ChevronLeft } from '@lucide/svelte';
	import { formatCurrency } from '$lib/components/Price.svelte';

	let { data }: { data: { receipt: Receipt } } = $props();

	const ORDER_STATUSES = ['pending', 'ready', 'completed', 'cancelled'] as const;
	type OrderStatus = (typeof ORDER_STATUSES)[number];

	let currentStatus = $derived(data.receipt.pickup.status);

	const fullName = $derived<string>(
		(() => {
			let user = data.receipt.user;
			return getLocale() === 'hu'
				? `${user.last_name} ${user.first_name}`
				: `${user.first_name} ${user.last_name}`;
		})()
	);

	let isUpdating = $state(false);

	async function updateStatus(newStatus: OrderStatus) {
		if (isUpdating || newStatus === currentStatus) return;

		isUpdating = true;
		try {
			const response = await fetch(`/api/staff/pickups/${data.receipt.pickup.id}`, {
				method: 'PATCH',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({
					status: newStatus,
					completed_at: ['completed', 'cancelled'].includes(newStatus)
						? new Date().toISOString()
						: null
				})
			});

			if (!response.ok) {
				toast.error(m['messages.server_error']());
				return;
			}

			currentStatus = newStatus;
			toast.success(m['orders.status_updated']());
			await invalidateAll();
		} catch (e) {
			toast.error(m['messages.server_error']());
		} finally {
			isUpdating = false;
		}
	}

	let selectedStatus = $state<{ value: OrderStatus; label: string } | null>(null);

	$effect(() => {
		selectedStatus = {
			value: currentStatus as OrderStatus,
			label: statusLabels[currentStatus]()
		};
	});

	function handleStatusChange(value: OrderStatus) {
		updateStatus(value);
	}

	const statusLabels: Record<string, () => string> = {
		pending: m['orders.status.pending'],
		ready: m['orders.status.ready'],
		cancelled: m['orders.status.cancelled'],
		completed: m['orders.status.completed']
	};

	const total = $derived(
		(() =>
			data.receipt.books.reduce(
				(sum: number, book: Receipt['books'][number]) =>
					sum + book.price_at_purchase * book.quantity,
				0
			))()
	);
</script>

<div class="mx-auto w-full px-4 pt-16! pb-56 md:w-4/5 md:px-0">
	<div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:gap-2">
		<button class="cursor-pointer" onclick={() => history.back()}><ChevronLeft /></button>
		<h1 class="text-3xl">
			{m['title.order']({ id: data.receipt.id })}
		</h1>
		<OrderStatusBadge status={currentStatus} />
	</div>
	<p class="mb-2 text-muted-foreground">
		{m['orders.customer']()}:
		<span class="font-medium text-foreground">
			{#if data.receipt.user}
				{fullName} ({data.receipt.user.email})
			{:else}
				{m['orders.deleted_user']()}
			{/if}
		</span>
	</p>
	{#if data.receipt.pickup.completed_at}
		<p class="text-muted-foreground">
			{m['orders.completed_at']({
				date: new Date(data.receipt.pickup.completed_at).toLocaleDateString()
			})}
		</p>
	{/if}
	<div class="mt-12 flex flex-col gap-3">
		<h2 class="mb-2 text-2xl">
			{m['orders.update_status']()}
		</h2>
		<div class="flex items-center gap-3">
			{#if selectedStatus}
				<Select.Root
					value={selectedStatus.value}
					type="single"
					onValueChange={(selected) => {
						if (selected) handleStatusChange(selected as OrderStatus);
					}}
				>
					<Select.Trigger disabled={isUpdating}>
						{#if isUpdating}
							<Spinner class="h-4 w-4" />
						{:else}
							{selectedStatus.label}
						{/if}
					</Select.Trigger>
					<Select.Content>
						{#each ORDER_STATUSES as status}
							<Select.Item
								value={status}
								label={m[`orders.status.${status}`]()}
								disabled={status === selectedStatus.value}
							/>
						{/each}
					</Select.Content>
				</Select.Root>
			{/if}
		</div>
	</div>
	<div
		class="{data.receipt.pickup.completed_at
			? 'opacity-25 transition-opacity hover:opacity-100'
			: ''} mt-12"
	>
		<h2 class="mb-4 text-2xl">
			{m['orders.items']()}
		</h2>
		<Item.Group>
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
									<p
										class="relative top-1/4 bg-white p-px text-center text-[20%] dark:bg-foreground"
									>
										{book.title}
									</p>
								</div>
							{/if}
						</a>
					</Item.Media>
					<Item.Content class="gap-1">
						<Item.Title><a href="/books/{book.id}">{book.title}</a></Item.Title>
						<Item.Description>
							{m['orders.quantity']({ quantity: book.quantity })}
						</Item.Description>
					</Item.Content>
				</Item.Root>
				{#if i !== data.receipt.books.length - 1}
					<Item.Separator class="m-0" />
				{/if}
			{/each}
		</Item.Group>
		<h2 class="float-end mt-12 text-2xl">{m['cart.total']()}: {formatCurrency(total)}</h2>
	</div>
</div>
