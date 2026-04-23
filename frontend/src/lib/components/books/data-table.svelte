<script lang="ts">
	import { browser } from '$app/environment';
	import ChevronDownIcon from '@lucide/svelte/icons/chevron-down';
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import {
		type ColumnDef,
		type ColumnFiltersState,
		type PaginationState,
		type SortingState,
		type VisibilityState,
		getCoreRowModel,
		getFilteredRowModel,
		getPaginationRowModel,
		getSortedRowModel
	} from '@tanstack/table-core';
	import { FlexRender, createSvelteTable } from '$lib/components/ui/data-table/index.js';
	import * as Table from '$lib/components/ui/table/index.js';
	import { Button } from '$lib/components/ui/button/index.js';
	import * as DropdownMenu from '$lib/components/ui/dropdown-menu/index.js';
	import * as Accordion from '$lib/components/ui/accordion/index.js';
	import * as Select from '$lib/components/ui/select/index.js';
	import * as Pagination from '$lib/components/ui/pagination/index.js';
	import * as Command from '$lib/components/ui/command/index.js';
	import * as Popover from '$lib/components/ui/popover/index.js';
	import * as Drawer from '$lib/components/ui/drawer/index.js';
	import * as m from '$lib/paraglide/messages.js';
	import type { Book, Author, Genre, Publisher } from '$lib/types';
	import BookItem from '../BookItem.svelte';
	import { Input, Separator } from '$lib/components/ui';
	import { getColumns } from './columns.ts';
	import { onMount } from 'svelte';
	import ChevronDown from '@lucide/svelte/icons/chevron-down';
	import { getLocale } from '$lib/paraglide/runtime.js';
	import { Search } from '@lucide/svelte';

	type DataTableProps = {
		data: {
			books: Book[];
			authors: Author[];
			genres: Genre[];
			publishers: Publisher[];
			meta: { per_page: number; total: number };
		};
	};

	let { data }: DataTableProps = $props();

	let openAuthor = $state(false);
	let openGenre = $state(false);
	let openPublisher = $state(false);
	let searchAuthor = $state('');
	let searchGenre = $state('');
	let searchPublisher = $state('');
	let isDesktop = $state(false);

	function checkScreenSize() {
		if (browser) {
			isDesktop = window.innerWidth >= 768;
		}
	}

	onMount(() => {
		if (browser) {
			checkScreenSize();
			window.addEventListener('resize', checkScreenSize);
			return () => window.removeEventListener('resize', checkScreenSize);
		}
	});

	const FILTER_KEYS = [
		'search',
		'author',
		'genre',
		'release_year',
		'publisher',
		'min_year',
		'max_year',
		'min_price',
		'max_price',
		'min_pages',
		'max_pages',
		'order_title',
		'order_year',
		'order_page',
		'order_price'
	] as const;
	type FilterKey = (typeof FILTER_KEYS)[number];

	let filters = $state<Record<FilterKey, string>>(
		Object.fromEntries(FILTER_KEYS.map((k) => [k, page.url.searchParams.get(k) ?? ''])) as Record<
			FilterKey,
			string
		>
	);
	let sort = $derived(
		['order_title', 'order_year', 'order_page', 'order_price']
			.map((k) => (filters[k as FilterKey] ? `${k}_${filters[k as FilterKey]}` : ''))
			.find(Boolean) ?? ''
	);
	let currentPage = $state(Number(page.url.searchParams.get('page') || 1));

	const sortLabels: Record<string, () => string> = {
		order_title_asc: m['book_lookup.order_by_t_asc'],
		order_title_desc: m['book_lookup.order_by_t_desc'],
		order_year_asc: m['book_lookup.order_by_y_asc'],
		order_year_desc: m['book_lookup.order_by_y_desc'],
		order_page_asc: m['book_lookup.order_by_pa_asc'],
		order_page_desc: m['book_lookup.order_by_pa_desc'],
		order_price_asc: m['book_lookup.order_by_pr_asc'],
		order_price_desc: m['book_lookup.order_by_pr_desc']
	};

	function applyFilters() {
		currentPage = 1;
		const params = new URLSearchParams({ page: currentPage.toString() });
		for (const key of FILTER_KEYS) {
			if (filters[key]) params.set(key, filters[key]);
		}
		goto(`?${params}`, { keepFocus: true });
	}

	function clearFilters() {
		currentPage = 1;
		for (const key of FILTER_KEYS) filters[key] = '';
		sort = '';
		goto('/books', { keepFocus: true });
	}

	let pagination = $state<PaginationState>({ pageIndex: 0, pageSize: 10 });
	let sorting = $state<SortingState>([]);
	let columnFilters = $state<ColumnFiltersState>([]);
	let columnVisibility = $state<VisibilityState>({});

	const table = createSvelteTable({
		get data() {
			return data.books;
		},
		// svelte-ignore state_referenced_locally
		columns: getColumns(page.data.user),
		state: {
			get pagination() {
				return pagination;
			},
			get sorting() {
				return sorting;
			},
			get columnFilters() {
				return columnFilters;
			},
			get columnVisibility() {
				return columnVisibility;
			}
		},
		getCoreRowModel: getCoreRowModel(),
		getPaginationRowModel: getPaginationRowModel(),
		getSortedRowModel: getSortedRowModel(),
		getFilteredRowModel: getFilteredRowModel(),
		onPaginationChange: (updater) => {
			pagination = typeof updater === 'function' ? updater(pagination) : updater;
		},
		onSortingChange: (updater) => {
			sorting = typeof updater === 'function' ? updater(sorting) : updater;
		},
		onColumnFiltersChange: (updater) => {
			columnFilters = typeof updater === 'function' ? updater(columnFilters) : updater;
		},
		onColumnVisibilityChange: (updater) => {
			columnVisibility = typeof updater === 'function' ? updater(columnVisibility) : updater;
		}
	});

	function getColumnLabel(columnId: string): string {
		const key = `book_lookup.${columnId}` as keyof typeof m;
		return typeof m[key] === 'function' ? (m[key] as () => string)() : columnId;
	}
</script>

<div class="mx-auto w-full px-4 pt-16 md:w-4/5 md:px-0">
	<h1 class="mb-6 text-3xl">{m['title.book_lookup']()}</h1>
	<div class="mb-6">
		<div class="relative flex-1 md:w-1/2">
			<Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
			<Input
				placeholder={`${m['book_lookup.filter_by_title']()}...`}
				bind:value={filters.search}
				onkeydown={(e) => e.key === 'Enter' && applyFilters()}
				class="mb-4 pl-9"
			/>
		</div>
		<Accordion.Root type="multiple">
			<Accordion.Item>
				<Accordion.Trigger>{m['book_lookup.metadata']()}</Accordion.Trigger>
				<Accordion.Content class="flex flex-col gap-3 pt-2 md:flex-row">
					<div class="flex flex-col gap-2">
						<span class="text-xs text-muted-foreground">
							{m['book_lookup.author']()}
						</span>
						{#if isDesktop}
							<Popover.Root bind:open={openAuthor}>
								<Popover.Trigger>
									<Button variant="outline" class="w-full justify-start md:w-auto">
										{data.authors.find((x) => x.id === filters.author)?.name ??
											m['book_lookup.all_authors']()}
										<ChevronDown />
									</Button>
								</Popover.Trigger>
								<Popover.Content class="w-50 p-0" align="start">
									<Command.Root>
										<Command.Input
											bind:value={searchAuthor}
											placeholder={m['book_lookup.author']()}
										/>
										<Command.List>
											<Command.Empty>{m['no_results']()}</Command.Empty>
											<Command.Group>
												<Command.Item
													value=""
													onSelect={() => {
														filters.author = '';
														searchAuthor = '';
														openAuthor = false;
													}}
												>
													{m['book_lookup.all_authors']()}
												</Command.Item>
												{#each data.authors as author (author.id)}
													<Command.Item
														value={author.id}
														keywords={[author.name]}
														onSelect={() => {
															filters.author = author.id;
															searchAuthor = '';
															openAuthor = false;
														}}
													>
														{author.name}
													</Command.Item>
												{/each}
											</Command.Group>
										</Command.List>
									</Command.Root>
								</Popover.Content>
							</Popover.Root>
						{:else}
							<Drawer.Root bind:open={openAuthor}>
								<Drawer.Trigger>
									<Button variant="outline" class="w-full justify-start">
										{data.authors.find((x) => x.id === filters.author)?.name ??
											m['book_lookup.all_authors']()}
										<ChevronDown />
									</Button>
								</Drawer.Trigger>
								<Drawer.Content>
									<div class="mt-4 border-t">
										<Command.Root>
											<Command.Input
												bind:value={searchAuthor}
												placeholder={m['book_lookup.author']()}
											/>
											<Command.List>
												<Command.Empty>{m['no_results']()}</Command.Empty>
												<Command.Group>
													<Command.Item
														value=""
														onSelect={() => {
															filters.author = '';
															searchAuthor = '';
															openAuthor = false;
														}}
													>
														{m['book_lookup.all_authors']()}
													</Command.Item>
													{#each data.authors as author (author.id)}
														<Command.Item
															value={author.id}
															keywords={[author.name]}
															onSelect={() => {
																filters.author = author.id;
																searchAuthor = '';
																openAuthor = false;
															}}
														>
															{author.name}
														</Command.Item>
													{/each}
												</Command.Group>
											</Command.List>
										</Command.Root>
									</div>
								</Drawer.Content>
							</Drawer.Root>
						{/if}
					</div>
					<div class="flex flex-col gap-2">
						<span class="text-xs text-muted-foreground">
							{m['book_lookup.genre']()}
						</span>
						{#if isDesktop}
							<Popover.Root bind:open={openGenre}>
								<Popover.Trigger>
									<Button variant="outline" class="w-full justify-start md:w-auto">
										{data.genres.find((x) => x.id === filters.genre)?.[`name_${getLocale()}`] ??
											m['book_lookup.all_genres']()}
										<ChevronDown />
									</Button>
								</Popover.Trigger>
								<Popover.Content class="w-50 p-0" align="start">
									<Command.Root>
										<Command.Input
											bind:value={searchGenre}
											placeholder={m['book_lookup.genre']()}
										/>
										<Command.List>
											<Command.Empty>{m['no_results']()}</Command.Empty>
											<Command.Group>
												<Command.Item
													value=""
													onSelect={() => {
														filters.genre = '';
														searchGenre = '';
														openGenre = false;
													}}
												>
													{m['book_lookup.all_genres']()}
												</Command.Item>
												{#each data.genres as genre (genre.id)}
													<Command.Item
														value={genre.id}
														keywords={[genre[`name_${getLocale()}`]]}
														onSelect={() => {
															filters.genre = genre.id;
															searchGenre = '';
															openGenre = false;
														}}
													>
														{genre[`name_${getLocale()}`]}
													</Command.Item>
												{/each}
											</Command.Group>
										</Command.List>
									</Command.Root>
								</Popover.Content>
							</Popover.Root>
						{:else}
							<Drawer.Root bind:open={openGenre}>
								<Drawer.Trigger>
									<Button variant="outline" class="w-full justify-start">
										{data.genres.find((x) => x.id === filters.genre)?.[`name_${getLocale()}`] ??
											m['book_lookup.all_genres']()}
										<ChevronDown />
									</Button>
								</Drawer.Trigger>
								<Drawer.Content>
									<div class="mt-4 border-t">
										<Command.Root>
											<Command.Input
												bind:value={searchGenre}
												placeholder={m['book_lookup.genre']()}
											/>
											<Command.List>
												<Command.Empty>{m['no_results']()}</Command.Empty>
												<Command.Group>
													<Command.Item
														value=""
														onSelect={() => {
															filters.genre = '';
															searchGenre = '';
															openGenre = false;
														}}
													>
														{m['book_lookup.all_genres']()}
													</Command.Item>
													{#each data.genres as genre (genre.id)}
														<Command.Item
															value={genre.id}
															keywords={[genre[`name_${getLocale()}`]]}
															onSelect={() => {
																filters.genre = genre.id;
																searchGenre = '';
																openGenre = false;
															}}
														>
															{genre[`name_${getLocale()}`]}
														</Command.Item>
													{/each}
												</Command.Group>
											</Command.List>
										</Command.Root>
									</div>
								</Drawer.Content>
							</Drawer.Root>
						{/if}
					</div>
					<div class="flex flex-col gap-2">
						<span class="text-xs text-muted-foreground">
							{m['book_lookup.publisher']()}
						</span>
						{#if isDesktop}
							<Popover.Root bind:open={openPublisher}>
								<Popover.Trigger>
									<Button variant="outline" class="w-full justify-start md:w-auto">
										{data.publishers.find((x) => x.id === filters.publisher)?.name ??
											m['book_lookup.all_publishers']()}
										<ChevronDown />
									</Button>
								</Popover.Trigger>
								<Popover.Content class="w-50 p-0" align="start">
									<Command.Root>
										<Command.Input
											bind:value={searchPublisher}
											placeholder={m['book_lookup.publisher']()}
										/>
										<Command.List>
											<Command.Empty>{m['no_results']()}</Command.Empty>
											<Command.Group>
												<Command.Item
													value=""
													onSelect={() => {
														filters.publisher = '';
														searchPublisher = '';
														openPublisher = false;
													}}
												>
													{m['book_lookup.all_publishers']()}
												</Command.Item>
												{#each data.publishers as publisher (publisher.id)}
													<Command.Item
														value={publisher.id}
														keywords={[publisher.name]}
														onSelect={() => {
															filters.publisher = publisher.id;
															searchPublisher = '';
															openPublisher = false;
														}}
													>
														{publisher.name}
													</Command.Item>
												{/each}
											</Command.Group>
										</Command.List>
									</Command.Root>
								</Popover.Content>
							</Popover.Root>
						{:else}
							<Drawer.Root bind:open={openPublisher}>
								<Drawer.Trigger>
									<Button variant="outline" class="w-full justify-start">
										{data.publishers.find((x) => x.id === filters.publisher)?.name ??
											m['book_lookup.all_publishers']()}
										<ChevronDown />
									</Button>
								</Drawer.Trigger>
								<Drawer.Content>
									<div class="mt-4 border-t">
										<Command.Root>
											<Command.Input
												bind:value={searchPublisher}
												placeholder={m['book_lookup.publisher']()}
											/>
											<Command.List>
												<Command.Empty>{m['no_results']()}</Command.Empty>
												<Command.Group>
													<Command.Item
														value=""
														onSelect={() => {
															filters.publisher = '';
															searchPublisher = '';
															openPublisher = false;
														}}
													>
														{m['book_lookup.all_publishers']()}
													</Command.Item>
													{#each data.publishers as publisher (publisher.id)}
														<Command.Item
															value={publisher.id}
															keywords={[publisher.name]}
															onSelect={() => {
																filters.publisher = publisher.id;
																searchPublisher = '';
																openPublisher = false;
															}}
														>
															{publisher.name}
														</Command.Item>
													{/each}
												</Command.Group>
											</Command.List>
										</Command.Root>
									</div>
								</Drawer.Content>
							</Drawer.Root>
						{/if}
					</div>
				</Accordion.Content>
			</Accordion.Item>
			<Accordion.Item>
				<Accordion.Trigger>{m['book_lookup.ranges']()}</Accordion.Trigger>
				<Accordion.Content class="flex flex-col gap-4 pt-2">
					<div class="flex flex-col gap-2">
						<span class="text-xs text-muted-foreground">
							{m['book_lookup.release_year']()}
						</span>
						<div class="flex gap-2 md:w-1/2">
							<Input type="number" min={1} placeholder="Min" bind:value={filters.min_year} />
							<Input type="number" placeholder="Max" bind:value={filters.max_year} />
						</div>
					</div>
					<div class="flex flex-col gap-2">
						<span class="text-xs text-muted-foreground">
							{m['book_lookup.price_range']()}
						</span>
						<div class="flex gap-2 md:w-1/2">
							<Input type="number" min={1} placeholder="Min" bind:value={filters.min_price} />
							<Input type="number" placeholder="Max" bind:value={filters.max_price} />
						</div>
					</div>
					<div class="flex flex-col gap-2">
						<span class="text-xs text-muted-foreground">
							{m['book_lookup.page_range']()}
						</span>
						<div class="flex gap-2 md:w-1/2">
							<Input type="number" min={1} placeholder="Min" bind:value={filters.min_pages} />
							<Input type="number" placeholder="Max" bind:value={filters.max_pages} />
						</div>
					</div>
				</Accordion.Content>
			</Accordion.Item>
			<Accordion.Item class="md:hidden">
				<Accordion.Trigger>{m['book_lookup.order']()}</Accordion.Trigger>
				<Accordion.Content>
					<Select.Root
						type="single"
						value={sort}
						onValueChange={(x: string) => {
							for (const k of ['order_title', 'order_year', 'order_page', 'order_price'] as const) {
								filters[k] = '';
							}
							if (!x) return;
							const key = x.slice(0, x.lastIndexOf('_')) as FilterKey;
							const direction = x.slice(x.lastIndexOf('_') + 1);
							filters[key] = direction;
							applyFilters();
						}}
					>
						<Select.Trigger class="w-full md:w-auto">
							{sort ? sortLabels[sort]() : m['book_lookup.order']()}
						</Select.Trigger>
						<Select.Content>
							<Select.Item value="order_title_asc">{m['book_lookup.order_by_t_asc']()}</Select.Item>
							<Select.Item value="order_title_desc"
								>{m['book_lookup.order_by_t_desc']()}</Select.Item
							>
							<Select.Item value="order_year_asc">{m['book_lookup.order_by_y_asc']()}</Select.Item>
							<Select.Item value="order_year_desc">{m['book_lookup.order_by_y_desc']()}</Select.Item
							>
							<Select.Item value="order_page_asc">{m['book_lookup.order_by_pa_asc']()}</Select.Item>
							<Select.Item value="order_page_desc"
								>{m['book_lookup.order_by_pa_desc']()}</Select.Item
							>
							<Select.Item value="order_price_asc">{m['book_lookup.order_by_pr_asc']()}</Select.Item
							>
							<Select.Item value="order_price_desc"
								>{m['book_lookup.order_by_pr_desc']()}</Select.Item
							>
						</Select.Content>
					</Select.Root>
				</Accordion.Content>
			</Accordion.Item>
		</Accordion.Root>
		<Separator class="w-full md:hidden" orientation="horizontal" />
		<div class="flex flex-row items-end justify-between">
			<div class="mt-4 flex w-full flex-col gap-2 md:w-auto md:flex-row">
				<Button onclick={applyFilters}>
					{m['book_lookup.apply_filters']()}
				</Button>
				<Button variant="ghost" onclick={clearFilters}>
					{m['book_lookup.clear_filters']()}
				</Button>
			</div>
			<DropdownMenu.Root>
				<DropdownMenu.Trigger>
					{#snippet child({ props }: { props: Record<string, unknown> })}
						<Button {...props} variant="outline" class="hidden md:ms-auto md:flex">
							{m['book_lookup.columns']()}
							<ChevronDownIcon class="ms-2 size-4" />
						</Button>
					{/snippet}
				</DropdownMenu.Trigger>
				<DropdownMenu.Content align="end">
					{#each table.getAllColumns().filter((col) => col.getCanHide()) as column (column.id)}
						<DropdownMenu.CheckboxItem
							bind:checked={() => column.getIsVisible(), (v) => column.toggleVisibility(!!v)}
						>
							{getColumnLabel(column.id)}
						</DropdownMenu.CheckboxItem>
					{/each}
				</DropdownMenu.Content>
			</DropdownMenu.Root>
		</div>
	</div>
	<div class="flex flex-col gap-3 md:hidden">
		{#if data.books.length === 0}
			<p class="py-10 text-center text-muted-foreground">{m['no_results']()}.</p>
		{:else}
			{#each data.books as book (book.id)}
				<BookItem {book} discounts={page.data.discounts} />
			{/each}
		{/if}
	</div>
	<div class="hidden md:block">
		<Table.Root>
			<Table.Header>
				{#each table.getHeaderGroups() as headerGroup (headerGroup.id)}
					<Table.Row>
						{#each headerGroup.headers as header (header.id)}
							<Table.Head>
								{#if !header.isPlaceholder}
									<FlexRender
										content={header.column.columnDef.header}
										context={header.getContext()}
									/>
								{/if}
							</Table.Head>
						{/each}
					</Table.Row>
				{/each}
			</Table.Header>
			<Table.Body>
				{#each table.getRowModel().rows as row (row.id)}
					<Table.Row>
						{#each row.getVisibleCells() as cell (cell.id)}
							<Table.Cell>
								{#if ['cover', 'title'].includes(cell.column.id)}
									<a href={`/books/${data.books[Number(row.id)].id}`}>
										<FlexRender content={cell.column.columnDef.cell} context={cell.getContext()} />
									</a>
								{:else}
									<FlexRender content={cell.column.columnDef.cell} context={cell.getContext()} />
								{/if}
							</Table.Cell>
						{/each}
					</Table.Row>
				{:else}
					<Table.Row>
						<Table.Cell colspan={getColumns(page.data.user).length} class="h-24 text-center">
							{m['no_results']()}.
						</Table.Cell>
					</Table.Row>
				{/each}
			</Table.Body>
		</Table.Root>
	</div>
	<div class="flex items-center justify-center gap-2 pt-12 pb-24">
		<Pagination.Root
			count={data.meta.total}
			perPage={data.meta.per_page}
			page={currentPage}
			onPageChange={(p: number) => {
				currentPage = p;
				const params = new URLSearchParams(page.url.searchParams);
				params.set('page', String(p));
				goto(`?${params}`, { keepFocus: true });
			}}
		>
			{#snippet children({ pages, currentPage })}
				<Pagination.Content>
					<Pagination.Item><Pagination.PrevButton /></Pagination.Item>
					{#each pages as page (page.key)}
						{#if page.type === 'ellipsis'}
							<Pagination.Item><Pagination.Ellipsis /></Pagination.Item>
						{:else}
							<Pagination.Item>
								<Pagination.Link {page} isActive={currentPage === page.value}
									>{page.value}</Pagination.Link
								>
							</Pagination.Item>
						{/if}
					{/each}
					<Pagination.Item><Pagination.NextButton /></Pagination.Item>
				</Pagination.Content>
			{/snippet}
		</Pagination.Root>
	</div>
</div>
