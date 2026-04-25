import type { ColumnDef } from '@tanstack/table-core';
import { createRawSnippet } from 'svelte';
import { renderComponent, renderSnippet } from '$lib/components/ui/data-table/index.js';
import { Badge } from '$lib/components/ui/badge/index.js';
import DataTableButton from './data-table-button.svelte';
import * as m from '$lib/paraglide/messages.js';
import { page } from '$app/state';
import { goto } from '$app/navigation';
import type { User } from '$lib/types';
import { getLocale } from '$lib/paraglide/runtime';
import DataTableActions from './data-table-actions.svelte';
import { toast } from 'svelte-sonner';

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
	if (role === 'admin') return m['admin.user_table.admin']();
	if (role === 'manager') return m['admin.user_table.manager']();
	if (role === 'staff') return m['admin.user_table.staff']();
	return m['admin.user_table.customer']();
};

export const columns: ColumnDef<User>[] = [
	{
		accessorKey: 'name',
		header: () =>
			renderComponent(DataTableButton, {
				onclick: () => {
					const params = new URLSearchParams(page.url.searchParams);
					params.set('order_name', params.get('order_name') === 'asc' ? 'desc' : 'asc');
					params.delete('order_email');
					params.delete('order_role');
					params.delete('order_receipts');
					params.set('page', '1');
					goto(`?${params}`, { keepFocus: true });
				},
				title: m['admin.user_table.name'](),
				isActive: !!page.url.searchParams.get('order_name'),
				order: page.url.searchParams.get('order_name'),
				textPos: 'start'
			}),
		cell: ({ row }) =>
			getLocale() === 'hu'
				? `${row.original.last_name} ${row.original.first_name}`
				: `${row.original.first_name} ${row.original.last_name}`
	},
	{
		accessorKey: 'email',
		header: () =>
			renderComponent(DataTableButton, {
				onclick: () => {
					const params = new URLSearchParams(page.url.searchParams);
					params.set('order_email', params.get('order_email') === 'asc' ? 'desc' : 'asc');
					params.delete('order_name');
					params.delete('order_role');
					params.delete('order_receipts');
					params.set('page', '1');
					goto(`?${params}`, { keepFocus: true });
				},
				title: m['admin.user_table.email'](),
				isActive: !!page.url.searchParams.get('order_email'),
				order: page.url.searchParams.get('order_email'),
				textPos: 'start'
			}),
		cell: ({ row }) => {
			const snippet = createRawSnippet<[{ email: string }]>((getEmail) => ({
				render: () => `<div class="text-muted-foreground">${getEmail().email}</div>`
			}));

			return renderSnippet(snippet, { email: row.original.email });
		}
	},
	{
		accessorKey: 'role',
		header: () =>
			renderComponent(DataTableButton, {
				onclick: () => {
					const params = new URLSearchParams(page.url.searchParams);
					params.set('order_role', params.get('order_role') === 'asc' ? 'desc' : 'asc');
					params.delete('order_name');
					params.delete('order_email');
					params.delete('order_receipts');
					params.set('page', '1');
					goto(`?${params}`, { keepFocus: true });
				},
				title: m['admin.user_table.role'](),
				isActive: !!page.url.searchParams.get('order_role'),
				order: page.url.searchParams.get('order_role'),
				textPos: 'start'
			}),
		cell: ({ row }) => {
			const label = roleLabel(row.original.role!);
			const variant = roleBadgeVariant[row.original.role!];
			const badgeSnippet = createRawSnippet(() => ({
				render: () => `<span>${label}</span>`
			}));

			return renderComponent(Badge, {
				variant,
				children: badgeSnippet
			});
		}
	},
	{
		accessorKey: 'receipts',
		header: () =>
			renderComponent(DataTableButton, {
				onclick: () => {
					const params = new URLSearchParams(page.url.searchParams);
					params.set('order_receipts', params.get('order_receipts') === 'asc' ? 'desc' : 'asc');
					params.delete('order_name');
					params.delete('order_email');
					params.delete('order_role');
					params.set('page', '1');
					goto(`?${params}`, { keepFocus: true });
				},
				title: m['admin.user_table.orders'](),
				isActive: !!page.url.searchParams.get('order_receipts'),
				order: page.url.searchParams.get('order_receipts'),
				textPos: 'end'
			}),
		cell: ({ row }) => {
			const snippet = createRawSnippet<[{ count: number; isCustomer: boolean }]>((getData) => ({
				render: () => {
					const { count, isCustomer } = getData();
					return `<div class="text-muted-foreground">${isCustomer ? count : '-'}</div>`;
				}
			}));

			return renderSnippet(snippet, {
				count: row.original.receipts.length,
				isCustomer: row.original.role === 'customer'
			});
		}
	},
	{
		id: 'actions',
		cell: ({ row }) => {
			if (row.original.id === page.data.user?.id) {
				const snippet = createRawSnippet(() => ({
					render: () => '<div class="size-8"/>'
				}));
				return renderSnippet(snippet);
			} else {
				return renderComponent(DataTableActions, {
					user: row.original,
					onDelete: async (id) => {
						try {
							await fetch(`/api/admin/users/${id}`, { method: 'DELETE' });
							toast.success(m['admin.user_table.action.delete_user_success']());
							goto(page.url.pathname + page.url.search, { invalidateAll: true });
						} catch {
							toast.error(m['messages.server_error']());
						}
					},
					onRoleChange: async (id, role) => {
						try {
							await fetch(`/api/admin/users/${id}`, {
								method: 'PATCH',
								headers: { 'Content-Type': 'application/json' },
								body: JSON.stringify({ role })
							});
							toast.success(m['admin.user_table.action.role_change_success']());
							goto(page.url.pathname + page.url.search, { invalidateAll: true });
						} catch {
							toast.error(m['messages.server_error']());
						}
					}
				});
			}
		}
	}
];
