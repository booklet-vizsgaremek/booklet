<script lang="ts">
	import { Badge } from '$lib/components/ui/badge/index.js';
	import * as AlertDialog from '$lib/components/ui/alert-dialog/index.js';
	import * as Select from '$lib/components/ui/select/index.js';
	import { Button } from '$lib/components/ui/button/index.js';
	import PencilIcon from '@lucide/svelte/icons/pencil';
	import Trash2Icon from '@lucide/svelte/icons/trash-2';
	import * as m from '$lib/paraglide/messages.js';
	import { beforeNavigate, goto } from '$app/navigation';
	import { page } from '$app/state';
	import type { User } from '$lib/types';
	import { getLocale } from '$lib/paraglide/runtime';
	import Spinner from './ui/spinner/spinner.svelte';

	let { user }: { user: User } = $props();

	let selectedRole = $derived<NonNullable<User['role']>>(user.role!);

	const fullName = $derived(
		getLocale() === 'hu'
			? `${user.last_name} ${user.first_name}`
			: `${user.first_name} ${user.last_name}`
	);

	let roleDialogOpen = $state(false);
	let deleteDialogOpen = $state(false);
	let roleLoading = $state(false);
	let deleteLoading = $state(false);

	beforeNavigate(() => {
		roleDialogOpen = false;
		deleteDialogOpen = false;
		roleLoading = false;
		deleteLoading = false;
	});

	const roleBadgeVariant: Record<
		NonNullable<User['role']>,
		'default' | 'secondary' | 'outline' | 'destructive'
	> = {
		admin: 'default',
		manager: 'secondary',
		staff: 'outline',
		customer: 'outline'
	};

	const roleLabel = (role: NonNullable<User['role']>): string => {
		switch (role) {
			case 'admin':
				return m['admin.user_table.admin']();
			case 'manager':
				return m['admin.user_table.manager']();
			case 'staff':
				return m['admin.user_table.staff']();
			default:
				return m['admin.user_table.customer']();
		}
	};

	async function handleDelete() {
		await fetch(`/api/admin/users/${user.id}`, { method: 'DELETE' });
		goto(`${page.url.pathname}${page.url.search}`, { invalidateAll: true });
	}

	async function handleRoleChange() {
		await fetch(`/api/admin/users/${user.id}`, {
			method: 'PATCH',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ role: selectedRole })
		});
		goto(`${page.url.pathname}${page.url.search}`, { invalidateAll: true });
	}
</script>

<div class="flex items-center justify-between rounded-lg border p-3">
	<div class="flex items-center gap-3">
		<div class="flex flex-col gap-1">
			<span class="font-medium">{fullName}</span>
			<span class="text-xs text-muted-foreground">{user.email}</span>
			<div class="mt-1 flex items-center gap-2">
				<Badge variant={roleBadgeVariant[user.role!]}>
					{roleLabel(user.role!)}
				</Badge>
				{#if user.role === 'customer'}
					<span class="text-xs text-muted-foreground">
						{#if user.receipts.length === 0}
							-
						{:else}
							{m['admin.user_table.orders_count']({ count: user.receipts.length })}
						{/if}
					</span>
				{/if}
			</div>
		</div>
	</div>

	<div class="flex items-center gap-1">
		<AlertDialog.Root bind:open={roleDialogOpen}>
			<AlertDialog.Trigger>
				{#snippet child({ props }: { props: Record<string, unknown> })}
					<Button
						{...props}
						variant="ghost"
						size="icon"
						class="size-8 text-muted-foreground hover:text-foreground"
						aria-label={m['admin.user_table.actions.change_role']()}
					>
						<PencilIcon class="size-4" />
					</Button>
				{/snippet}
			</AlertDialog.Trigger>
			<AlertDialog.Content>
				<AlertDialog.Header>
					<AlertDialog.Title>{m['admin.user_table.actions.change_role']()}</AlertDialog.Title>
					<AlertDialog.Description>
						{m['admin.user_table.actions.change_role_description']({ name: fullName })}
					</AlertDialog.Description>
				</AlertDialog.Header>
				<div class="flex flex-col gap-2 py-2">
					<span class="text-xs text-muted-foreground"
						>{m['admin.user_table.actions.new_role']()}</span
					>
					<Select.Root type="single" bind:value={selectedRole}>
						<Select.Trigger class="w-full">
							{roleLabel(selectedRole)}
						</Select.Trigger>
						<Select.Content>
							<Select.Item value="admin" disabled={user.role === 'admin'}
								>{m['admin.user_table.admin']()}</Select.Item
							>
							<Select.Item value="manager" disabled={user.role === 'manager'}
								>{m['admin.user_table.manager']()}</Select.Item
							>
							<Select.Item value="staff" disabled={user.role === 'staff'}
								>{m['admin.user_table.staff']()}</Select.Item
							>
							<Select.Item value="customer" disabled={user.role === 'customer'}
								>{m['admin.user_table.customer']()}</Select.Item
							>
						</Select.Content>
					</Select.Root>
				</div>
				<AlertDialog.Footer>
					{#if !roleLoading}
						<AlertDialog.Cancel class="cursor-pointer"
							>{m['admin.user_table.actions.cancel']()}</AlertDialog.Cancel
						>
					{/if}
					<AlertDialog.Action
						class="cursor-pointer disabled:pointer-events-none disabled:opacity-50"
						onclick={() => {
							roleLoading = true;
							handleRoleChange();
						}}
						disabled={roleLoading}
					>
						{#if roleLoading}
							<Spinner />
						{:else}
							{m['admin.user_table.actions.confirm']()}
						{/if}
					</AlertDialog.Action>
				</AlertDialog.Footer>
			</AlertDialog.Content>
		</AlertDialog.Root>

		<AlertDialog.Root bind:open={deleteDialogOpen}>
			<AlertDialog.Trigger>
				{#snippet child({ props }: { props: Record<string, unknown> })}
					<Button
						{...props}
						variant="ghost"
						size="icon"
						class="size-8 text-muted-foreground hover:text-destructive"
						aria-label={m['admin.user_table.actions.delete']()}
					>
						<Trash2Icon class="size-4" />
					</Button>
				{/snippet}
			</AlertDialog.Trigger>
			<AlertDialog.Content>
				<AlertDialog.Header>
					<AlertDialog.Title>{m['admin.user_table.actions.delete']()}</AlertDialog.Title>
					<AlertDialog.Description>
						{m['admin.user_table.actions.delete_description']({ name: fullName })}
					</AlertDialog.Description>
				</AlertDialog.Header>
				<AlertDialog.Footer>
					{#if !deleteLoading}
						<AlertDialog.Cancel class="cursor-pointer"
							>{m['admin.user_table.actions.cancel']()}</AlertDialog.Cancel
						>
					{/if}
					<AlertDialog.Action
						class="text-destructive-foreground bg-destructive hover:bg-destructive/90 disabled:pointer-events-none disabled:opacity-50"
						onclick={() => {
							deleteLoading = true;
							handleDelete();
						}}
						disabled={deleteLoading}
					>
						{#if deleteLoading}
							<Spinner />
						{:else}
							{m['admin.user_table.actions.confirm']()}
						{/if}
					</AlertDialog.Action>
				</AlertDialog.Footer>
			</AlertDialog.Content>
		</AlertDialog.Root>
	</div>
</div>
