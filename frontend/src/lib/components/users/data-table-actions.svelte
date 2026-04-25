<script lang="ts">
	import PencilIcon from '@lucide/svelte/icons/pencil';
	import Trash2Icon from '@lucide/svelte/icons/trash-2';
	import { Button } from '$lib/components/ui/button/index.js';
	import * as AlertDialog from '$lib/components/ui/alert-dialog/index.js';
	import * as Select from '$lib/components/ui/select/index.js';
	import * as m from '$lib/paraglide/messages.js';
	import type { User } from '$lib/types';
	import { getLocale } from '$lib/paraglide/runtime';
	import Spinner from '$lib/components/ui/spinner/spinner.svelte';
	import { beforeNavigate } from '$app/navigation';

	let {
		user,
		onDelete,
		onRoleChange
	}: {
		user: User;
		onDelete?: (id: string) => void;
		onRoleChange?: (id: string, role: NonNullable<User['role']>) => void;
	} = $props();

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

	let selectedRole = $derived(user.role as NonNullable<User['role']>);

	const fullName = $derived(
		getLocale() === 'hu'
			? `${user.last_name} ${user.first_name}`
			: `${user.first_name} ${user.last_name}`
	);

	const roleLabel = (role: NonNullable<User['role']>): string => {
		if (role === 'admin') return m['admin.user_table.admin']();
		if (role === 'manager') return m['admin.user_table.manager']();
		if (role === 'staff') return m['admin.user_table.staff']();
		return m['admin.user_table.customer']();
	};
</script>

<div class="flex items-center gap-2">
	<AlertDialog.Root bind:open={roleDialogOpen}>
		<AlertDialog.Trigger>
			{#snippet child({ props }: { props: Record<string, unknown> })}
				<Button
					{...props}
					variant="ghost"
					size="icon"
					class="size-8 cursor-pointer text-muted-foreground hover:text-foreground"
					aria-label={m['admin.user_table.action.change_role']()}
				>
					<PencilIcon class="size-4" />
				</Button>
			{/snippet}
		</AlertDialog.Trigger>
		<AlertDialog.Content>
			<AlertDialog.Header>
				<AlertDialog.Title>{m['admin.user_table.action.change_role']()}</AlertDialog.Title>
				<AlertDialog.Description>
					{m['admin.user_table.action.change_role_description']({ name: fullName })}
				</AlertDialog.Description>
			</AlertDialog.Header>
			<div class="flex flex-col gap-2 py-2">
				<span class="text-xs text-muted-foreground">{m['admin.user_table.action.new_role']()}</span>
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
						>{m['admin.user_table.action.cancel']()}</AlertDialog.Cancel
					>
				{/if}
				<AlertDialog.Action
					class="cursor-pointer disabled:pointer-events-none disabled:opacity-50"
					onclick={() => {
						roleLoading = true;
						onRoleChange?.(user.id, selectedRole);
					}}
					disabled={roleLoading}
				>
					{#if roleLoading}
						<Spinner />
					{:else}
						{m['admin.user_table.action.confirm']()}
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
					class="size-8 cursor-pointer text-muted-foreground hover:text-destructive"
					aria-label={m['admin.user_table.action.delete']()}
				>
					<Trash2Icon class="size-4" />
				</Button>
			{/snippet}
		</AlertDialog.Trigger>
		<AlertDialog.Content>
			<AlertDialog.Header>
				<AlertDialog.Title>{m['admin.user_table.action.delete']()}</AlertDialog.Title>
				<AlertDialog.Description>
					{m['admin.user_table.action.delete_description']({ name: fullName })}
				</AlertDialog.Description>
			</AlertDialog.Header>
			<AlertDialog.Footer>
				{#if !deleteLoading}
					<AlertDialog.Cancel class="cursor-pointer"
						>{m['admin.user_table.action.cancel']()}</AlertDialog.Cancel
					>
				{/if}
				<AlertDialog.Action
					class="text-destructive-foreground cursor-pointer bg-destructive hover:bg-destructive/90 disabled:pointer-events-none disabled:opacity-50"
					onclick={() => {
						deleteLoading = true;
						onDelete?.(user.id);
					}}
					disabled={deleteLoading}
				>
					{#if deleteLoading}
						<Spinner />
					{:else}
						{m['admin.user_table.action.confirm']()}
					{/if}
				</AlertDialog.Action>
			</AlertDialog.Footer>
		</AlertDialog.Content>
	</AlertDialog.Root>
</div>
