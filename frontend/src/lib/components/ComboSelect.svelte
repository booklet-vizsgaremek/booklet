<script lang="ts" generics="T extends { id: string; label: string }">
	import * as Popover from '$lib/components/ui/popover';
	import * as Drawer from '$lib/components/ui/drawer';
	import * as Command from '$lib/components/ui/command';
	import { Button } from '$lib/components/ui/button';
	import { Check, ChevronDown } from '@lucide/svelte';
	import * as m from '$lib/paraglide/messages.js';
	import { MediaQuery } from 'svelte/reactivity';

	interface Props {
		items: T[];
		value: string | string[];
		placeholder: string;
		searchPlaceholder?: string;
		multiple?: boolean;
		onchange?: (value: string | string[]) => void;
	}

	let {
		items,
		value = $bindable(),
		placeholder,
		searchPlaceholder,
		multiple = false,
		onchange
	}: Props = $props();

	const isDesktop = new MediaQuery('(min-width: 768px)');
	let open = $state(false);
	let search = $state('');

	function getLabel(): string {
		if (multiple) {
			const selected = value as string[];
			if (selected.length === 0) return placeholder;
			if (selected.length === 1)
				return items.find((i) => i.id === selected[0])?.label ?? placeholder;
			return m['admin.books.selected']({ amount: selected.length });
		}
		return items.find((i) => i.id === value)?.label ?? placeholder;
	}

	function isSelected(id: string): boolean {
		return multiple ? (value as string[]).includes(id) : value === id;
	}

	function select(id: string) {
		if (multiple) {
			const current = value as string[];
			value = current.includes(id) ? current.filter((v) => v !== id) : [...current, id];
		} else {
			value = id;
			open = false;
		}
		search = '';
		onchange?.(value);
	}
</script>

{#snippet content()}
	<Command.Root>
		<Command.Input bind:value={search} placeholder={searchPlaceholder ?? placeholder} />
		<Command.List>
			<Command.Empty>{m['no_results']()}</Command.Empty>
			<Command.Group>
				{#each items as item (item.id)}
					<Command.Item value={item.id} keywords={[item.label]} onSelect={() => select(item.id)}>
						<Check size={14} class={isSelected(item.id) ? 'opacity-100' : 'opacity-0'} />
						{item.label}
					</Command.Item>
				{/each}
			</Command.Group>
		</Command.List>
	</Command.Root>
{/snippet}

{#if isDesktop.current}
	<Popover.Root bind:open>
		<Popover.Trigger>
			<Button variant="outline" class="w-full justify-start">
				{getLabel()}
				<ChevronDown class="ml-auto" size={16} />
			</Button>
		</Popover.Trigger>
		<Popover.Content class="w-64 p-0" align="start">
			{@render content()}
		</Popover.Content>
	</Popover.Root>
{:else}
	<Drawer.Root bind:open>
		<Drawer.Trigger>
			<Button variant="outline" class="w-full justify-start">
				{getLabel()}
				<ChevronDown class="ml-auto" size={16} />
			</Button>
		</Drawer.Trigger>
		<Drawer.Content>
			<div class="mt-4 border-t">
				{@render content()}
			</div>
		</Drawer.Content>
	</Drawer.Root>
{/if}
