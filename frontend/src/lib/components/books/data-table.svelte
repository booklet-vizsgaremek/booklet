<script lang="ts">
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
	import * as Pagination from '$lib/components/ui/pagination/index.js';
	import * as m from '$lib/paraglide/messages.js';
	import type { Book, Author, Genre, Publisher } from './columns';
	import BookItem from '../BookItem.svelte';

	type DataTableProps = {
		columns: ColumnDef<Book>[];
		data: {
			books: Book[];
			authors: Author[];
			genres: Genre[];
			publishers: Publisher[];
			meta: { per_page: number; total: number };
		};
	};

	let { data, columns }: DataTableProps = $props();

	let currentPage = $state(Number(page.url.searchParams.get('page') || 1));

	let pagination = $state<PaginationState>({ pageIndex: 0, pageSize: 10 });
	let sorting = $state<SortingState>([]);
	let columnFilters = $state<ColumnFiltersState>([]);
	let columnVisibility = $state<VisibilityState>({});

	const table = createSvelteTable({
		get data() {
			return data.books;
		},
		// svelte-ignore state_referenced_locally
		columns,
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
</script>

<div class="mx-auto w-full px-4 pt-16 md:w-4/5 md:px-0">
	<h1 class="mb-6 text-3xl">{m['title.book_lookup']()}</h1>
	<div class="flex flex-col gap-3 md:hidden">
		{#if data.books.length === 0}
			<p class="py-10 text-center text-muted-foreground">{m['no_results']()}.</p>
		{:else}
			{#each data.books as book (book.id)}
				<BookItem {book} />
			{/each}
		{/if}
	</div>
	<div class="hidden rounded-md border md:block">
		<Table.Root>
			<Table.Header>
				{#each table.getHeaderGroups() as headerGroup (headerGroup.id)}
					<Table.Row class="sticky top-0">
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
						<Table.Cell colspan={columns.length} class="h-24 text-center">
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
			{#snippet children({
				pages,
				currentPage
			}: {
				pages: Array<{ key: string | number; type: 'page' | 'ellipsis'; value?: number }>;
				currentPage: number;
			})}
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
