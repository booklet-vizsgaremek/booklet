<script lang="ts">
	import { MediaQuery } from 'svelte/reactivity';
	import { getLocale } from '$lib/paraglide/runtime';
	import * as Popover from '$lib/components/ui/popover';
	import * as Drawer from '$lib/components/ui/drawer';
	import * as m from '$lib/paraglide/messages.js';
	import { Button } from '$lib/components/ui/button';
	import type { Author } from '$lib/types/author';
	import { goto } from '$app/navigation';

	let { authors, classes = 'text-sm' }: { authors: Author[]; classes?: string } = $props();
	const authorList = $derived(
		new Intl.ListFormat(getLocale(), { style: 'long', type: 'conjunction' }).format(
			authors.map((x) => `${x.first_name} ${x.last_name}`)
		)
	);

	let open = $state(false);
	const isDesktop = new MediaQuery('(min-width: 768px)');
</script>

{#if authors.length === 1}
	<Button
		variant="link"
		onclick={() => goto(`/authors/${authors[0].id}`)}
		class={`h-auto w-auto cursor-pointer p-0 text-start whitespace-normal text-muted-foreground hover:text-foreground hover:no-underline ${classes}`}
	>
		{authorList}
	</Button>
{:else if isDesktop.current}
	<Popover.Root>
		<Popover.Trigger>
			<Button
				variant="link"
				class={`h-auto w-auto cursor-pointer p-0 text-start whitespace-normal text-muted-foreground hover:text-foreground hover:no-underline ${classes}`}
			>
				{authorList}
			</Button>
		</Popover.Trigger>
		<Popover.Content class="w-48 p-2">
			<ul class="flex flex-col gap-1">
				{#each authors as author (author.id)}
					<li>
						<a
							href="/authors/{author.id}"
							class="block rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground"
						>
							{author.first_name}
							{author.last_name}
						</a>
					</li>
				{/each}
			</ul>
		</Popover.Content>
	</Popover.Root>
{:else}
	<Drawer.Root bind:open>
		<Drawer.Trigger>
			<Button
				variant="link"
				class={`h-auto w-auto cursor-pointer p-0 text-start whitespace-normal text-muted-foreground hover:text-foreground hover:no-underline ${classes}`}
			>
				{authorList}
			</Button>
		</Drawer.Trigger>
		<Drawer.Content>
			<Drawer.Header>
				<Drawer.Title>{m['book_lookup.authors']()}</Drawer.Title>
			</Drawer.Header>
			<ul class="flex flex-col gap-1 p-4">
				{#each authors as author (author.id)}
					<li>
						<a
							href="/authors/{author.id}"
							class="block rounded-sm px-3 py-2.5 text-sm hover:bg-accent hover:text-accent-foreground"
						>
							{author.first_name}
							{author.last_name}
						</a>
					</li>
				{/each}
			</ul>
		</Drawer.Content>
	</Drawer.Root>
{/if}
