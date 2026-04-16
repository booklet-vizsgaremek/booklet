<script lang="ts">
	import type { ComponentProps } from 'svelte';
	import { ArrowDownIcon, ArrowUpIcon, ArrowUpDownIcon } from '@lucide/svelte/icons';
	import { Button } from '$lib/components/ui/button/index.js';

	let {
		variant = 'ghost',
		title,
		order,
		textPos,
		isActive,
		...restProps
	}: ComponentProps<typeof Button> & {
		title: string;
		order: string | null;
		textPos: string | null;
		isActive: boolean;
	} = $props();
</script>

<Button
	{variant}
	{...restProps}
	class={`${textPos ? `text-${textPos} ` : ''}${isActive ? 'bg-accent/50' : ''}`}
>
	{title}
	{#if order == 'asc'}
		<ArrowDownIcon class="ms-2" />
	{:else if order == 'desc'}
		<ArrowUpIcon class="ms-2" />
	{:else}
		<ArrowUpDownIcon class="ms-2" />
	{/if}
</Button>
