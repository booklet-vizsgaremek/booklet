<script lang="ts">
	import Trash2Icon from '@lucide/svelte/icons/trash-2';
	import { Button } from '$lib/components/ui/button/index.js';
	import * as AlertDialog from '$lib/components/ui/alert-dialog/index.js';
	import * as m from '$lib/paraglide/messages.js';
	import type { Book } from '$lib/types';
	import Spinner from '$lib/components/ui/spinner/spinner.svelte';
	import { beforeNavigate, goto } from '$app/navigation';
	import { Pen } from '@lucide/svelte';
	import { MediaQuery } from 'svelte/reactivity';

	let {
		book,
		onDelete
	}: {
		book: Book;
		onDelete?: (id: string) => void;
	} = $props();

	let deleteDialogOpen = $state(false);
	let deleteLoading = $state(false);

	beforeNavigate(() => {
		deleteDialogOpen = false;
		deleteLoading = false;
	});

	const isDesktop = new MediaQuery('(min-width: 768px)').current;
</script>

<div class="flex w-full items-center gap-2 md:w-auto">
	<AlertDialog.Root bind:open={deleteDialogOpen}>
		<AlertDialog.Trigger>
			{#snippet child({ props }: { props: Record<string, unknown> })}
				<Button
					{...props}
					variant={isDesktop ? 'ghost' : 'outline'}
					size="icon"
					class="w-1/2 cursor-pointer text-destructive hover:text-destructive md:size-8 md:text-muted-foreground"
					aria-label={m['book_lookup.action.delete']()}
				>
					<Trash2Icon class="size-4" />
				</Button>
			{/snippet}
		</AlertDialog.Trigger>
		<AlertDialog.Content>
			<AlertDialog.Header>
				<AlertDialog.Title>{m['book_lookup.action.delete']()}</AlertDialog.Title>
				<AlertDialog.Description>
					{m['book_lookup.action.delete_description']({ title: book.title })}
				</AlertDialog.Description>
			</AlertDialog.Header>
			<AlertDialog.Footer>
				{#if !deleteLoading}
					<AlertDialog.Cancel class="cursor-pointer"
						>{m['book_lookup.action.cancel']()}</AlertDialog.Cancel
					>
				{/if}
				<AlertDialog.Action
					class="text-destructive-foreground cursor-pointer bg-destructive hover:bg-destructive/90 disabled:pointer-events-none disabled:opacity-50"
					onclick={() => {
						deleteLoading = true;
						onDelete?.(book.id);
					}}
					disabled={deleteLoading}
				>
					{#if deleteLoading}
						<Spinner />
					{:else}
						{m['book_lookup.action.confirm']()}
					{/if}
				</AlertDialog.Action>
			</AlertDialog.Footer>
		</AlertDialog.Content>
	</AlertDialog.Root>
	<Button
		variant={isDesktop ? 'ghost' : 'outline'}
		size="icon"
		class="w-1/2 cursor-pointer text-muted-foreground md:size-8"
		aria-label={m['book_lookup.action.edit']()}
		onclick={() => goto(`/books/${book.id}/edit`)}
	>
		<Pen class="size-4" />
	</Button>
</div>
