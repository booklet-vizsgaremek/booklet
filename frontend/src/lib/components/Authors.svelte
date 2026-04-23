<script lang="ts">
	import { MediaQuery } from 'svelte/reactivity';
	import { getLocale } from '$lib/paraglide/runtime';
	import * as Popover from '$lib/components/ui/popover';
	import * as Drawer from '$lib/components/ui/drawer';
	import * as m from '$lib/paraglide/messages.js';
	import { Button } from '$lib/components/ui/button';
	import type { Author } from '$lib/types/author';
	import { goto } from '$app/navigation';
	import { page } from '$app/state';

	let {
		authors,
		classes = 'text-sm',
		truncate = false
	}: { authors: Author[]; classes?: string; truncate?: boolean } = $props();

	const authorList = $derived(
		(() => {
			if (truncate && authors.length > 1) {
				return `${authors[0].name} +${authors.length - 1}`;
			} else {
				return new Intl.ListFormat(getLocale(), { style: 'long', type: 'conjunction' }).format(
					authors.map((x) => x.name)
				);
			}
		})()
	);

	let openPopover = $state(false);
	let openDrawer = $state(false);
	const isDesktop = new MediaQuery('(min-width: 768px)');
</script>

{#if authors.length === 1}
	<Button
		variant="link"
		onclick={() => goto(`/authors/${authors[0].id}`)}
		class={`h-auto w-auto cursor-pointer p-0 text-start whitespace-normal text-muted-foreground hover:no-underline ${classes} ${page.url.pathname == `/authors/${authors[0].id}` ? 'pointer-events-none' : 'hover:text-foreground'}`}
	>
		{authorList}
	</Button>
{:else if isDesktop.current}
	<Popover.Root bind:open={openPopover}>
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
							onclick={() => (openPopover = false)}
							class="block rounded-sm px-2 py-1.5 text-sm {page.url.pathname ==
							`/authors/${author.id}`
								? 'pointer-events-none text-muted'
								: 'hover:bg-accent hover:text-accent-foreground'}"
						>
							{author.name}
						</a>
					</li>
				{/each}
			</ul>
		</Popover.Content>
	</Popover.Root>
{:else}
	<Drawer.Root bind:open={openDrawer}>
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
							onclick={() => (openDrawer = false)}
							class="block rounded-sm px-3 py-2.5 text-sm {page.url.pathname ==
							`/authors/${author.id}`
								? 'pointer-events-none text-muted'
								: 'hover:bg-accent hover:text-accent-foreground'}"
						>
							{author.name}
						</a>
					</li>
				{/each}
			</ul>
		</Drawer.Content>
	</Drawer.Root>
{/if}
