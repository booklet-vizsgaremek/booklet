<script lang="ts">
	import ChevronDownIcon from '@lucide/svelte/icons/chevron-down';
	import {
		type PaginationState,
		type SortingState,
		type ColumnFiltersState,
		type VisibilityState,
		getCoreRowModel,
		getFilteredRowModel,
		getPaginationRowModel,
		getSortedRowModel
	} from '@tanstack/table-core';
	import { FlexRender, createSvelteTable } from '$lib/components/ui/data-table/index.js';
	import * as Table from '$lib/components/ui/table/index.js';
	import * as Select from '$lib/components/ui/select/index.js';
	import * as Pagination from '$lib/components/ui/pagination/index.js';
	import * as DropdownMenu from '$lib/components/ui/dropdown-menu/index.js';
	import { Input } from '$lib/components/ui/input/index.js';
	import { Button } from '$lib/components/ui/button/index.js';
	import * as m from '$lib/paraglide/messages.js';
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { columns } from './columns.js';
	import type { User } from '$lib/types';
	import UserItem from '../UserItem.svelte';

	type DataTableProps = {
		data: {
			users: User[];
			meta: { per_page: number; total: number };
		};
	};

	let { data }: DataTableProps = $props();

	const FILTER_KEYS = [
		'search',
		'role',
		'order_name',
		'order_email',
		'order_role',
		'order_receipts'
	] as const;
	type FilterKey = (typeof FILTER_KEYS)[number];

	let filters = $state<Record<FilterKey, string>>(
		Object.fromEntries(FILTER_KEYS.map((k) => [k, page.url.searchParams.get(k) ?? ''])) as Record<
			FilterKey,
			string
		>
	);

	let currentPage = $state(Number(page.url.searchParams.get('page') ?? 1));

	function applyFilters() {
		currentPage = 1;
		const params = new URLSearchParams({ page: '1' });
		for (const key of FILTER_KEYS) {
			if (filters[key]) params.set(key, filters[key]);
		}
		goto(`?${params}`, { keepFocus: true });
	}

	function clearFilters() {
		currentPage = 1;
		for (const key of FILTER_KEYS) filters[key] = '';
		goto('?', { keepFocus: true });
	}

	function getColumnLabel(columnId: string): string {
		const key = `admin.user_table.${columnId}` as keyof typeof m;
		return typeof m[key] === 'function' ? (m[key] as () => string)() : columnId;
	}

	let pagination = $state<PaginationState>({ pageIndex: 0, pageSize: 10 });
	let sorting = $state<SortingState>([]);
	let columnFilters = $state<ColumnFiltersState>([]);
	let columnVisibility = $state<VisibilityState>({});

	const table = createSvelteTable({
		get data() {
			return data.users;
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

<div class="w-full">
	<h1 class="mb-6 text-2xl">{m['title.users']()}</h1>
	<div class="mb-6 flex flex-col gap-2">
		<div class="mb-2 flex flex-col gap-1">
			<span class="text-xs text-muted-foreground">{m['admin.user_table.filter_by_name']()}</span>
			<Input
				placeholder="{m['admin.user_table.filter_by_name']()}..."
				bind:value={filters.search}
				onkeydown={(e) => e.key === 'Enter' && applyFilters()}
				class="md:w-1/2"
			/>
		</div>
		<div class="flex flex-col gap-1">
			<span class="text-xs text-muted-foreground">
				{m['admin.user_table.filter_by_role']()}
			</span>
			<div class="flex gap-2 md:w-1/2">
				<Select.Root type="single" bind:value={filters.role}>
					<Select.Trigger class="w-full md:w-auto">
						{#if filters.role === 'admin'}
							{m['admin.user_table.admin']()}
						{:else if filters.role === 'manager'}
							{m['admin.user_table.manager']()}
						{:else if filters.role === 'staff'}
							{m['admin.user_table.staff']()}
						{:else if filters.role === 'customer'}
							{m['admin.user_table.customer']()}
						{:else}
							{m['admin.user_table.all_roles']()}
						{/if}
					</Select.Trigger>
					<Select.Content>
						<Select.Item value="">{m['admin.user_table.all_roles']()}</Select.Item>
						<Select.Item value="admin">{m['admin.user_table.admin']()}</Select.Item>
						<Select.Item value="manager">{m['admin.user_table.manager']()}</Select.Item>
						<Select.Item value="staff">{m['admin.user_table.staff']()}</Select.Item>
						<Select.Item value="customer">{m['admin.user_table.customer']()}</Select.Item>
					</Select.Content>
				</Select.Root>
			</div>
		</div>
	</div>
	<div class="mb-6 flex flex-row items-end justify-between">
		<div class="flex w-full flex-col gap-2 md:w-auto md:flex-row">
			<Button onclick={applyFilters}>{m['admin.user_table.apply_filters']()}</Button>
			<Button variant="ghost" onclick={clearFilters}>{m['admin.user_table.clear_filters']()}</Button
			>
		</div>
		<DropdownMenu.Root>
			<DropdownMenu.Trigger>
				{#snippet child({ props }: { props: Record<string, unknown> })}
					<Button {...props} variant="outline" class="hidden md:ms-auto md:flex">
						{m['admin.user_table.columns']()}
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
	<div class="flex flex-col gap-3 md:hidden">
		{#if data.users.length === 0}
			<p class="py-10 text-center text-muted-foreground">{m['admin.user_table.no_results']()}</p>
		{:else}
			{#each data.users as user (user.id)}
				<UserItem {user} />
			{/each}
		{/if}
	</div>
	<div class="hidden md:block">
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
								<FlexRender content={cell.column.columnDef.cell} context={cell.getContext()} />
							</Table.Cell>
						{/each}
					</Table.Row>
				{:else}
					<Table.Row>
						<Table.Cell colspan={columns.length} class="h-24 text-center text-muted-foreground">
							{m['admin.user_table.no_results']()}
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
								<Pagination.Link {page} isActive={currentPage === page.value}>
									{page.value}
								</Pagination.Link>
							</Pagination.Item>
						{/if}
					{/each}
					<Pagination.Item><Pagination.NextButton /></Pagination.Item>
				</Pagination.Content>
			{/snippet}
		</Pagination.Root>
	</div>
</div>
