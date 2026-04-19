import type { ColumnDef } from '@tanstack/table-core';
import { createRawSnippet } from 'svelte';
import { renderComponent, renderSnippet } from '$lib/components/ui/data-table/index.js';
import DataTableButton from './data-table-button.svelte';
import Price from '$lib/components/Price.svelte';
import * as m from '$lib/paraglide/messages.js';
import { getLocale } from '$lib/paraglide/runtime';
import { page } from '$app/state';
import { goto } from '$app/navigation';
import { getDiscountedPrice } from '$lib/stores/coupon.svelte';
import type { CartItem, Book } from '$lib/types';

export const columns: ColumnDef<Book>[] = [
	{
		accessorKey: 'cover',
		header: m['book_lookup.cover'](),
		cell: ({ row }) => {
			const snippet = createRawSnippet<[{ book: Book }]>((getBook) => ({
				render: () => {
					const book = getBook().book;
					if (book.img_path) {
						return `
							<img
								src="${book.img_path}"
								alt="${m['accessibility.book_cover']()}"
								class="aspect-2/3 h-24 object-cover"
							/>
						`;
					}
					return `
						<div class="flex aspect-2/3 h-24 items-start justify-center bg-blue-950 font-bold text-black">
							<p class="relative top-1/4 bg-white p-[.5px] text-center text-[20%] dark:bg-foreground">
								${book.title}
							</p>
						</div>
					`;
				}
			}));

			return renderSnippet(snippet, { book: row.original });
		}
	},
	{
		accessorKey: 'title',
		header: () =>
			renderComponent(DataTableButton, {
				onclick: () => {
					const params = new URLSearchParams(page.url.searchParams);
					params.set('order_title', params.get('order_title') === 'desc' ? 'asc' : 'desc');
					params.delete('order_year');
					params.delete('order_page');
					params.delete('order_price');
					params.set('page', '1');
					goto(`?${params}`, { keepFocus: true });
				},
				title: m['book_lookup.title'](),
				isActive: !!page.url.searchParams.get('order_title'),
				order: page.url.searchParams.get('order_title'),
				textPos: 'start'
			}),
		cell: ({ row }) => {
			const snippet = createRawSnippet<[{ title: string }]>((getTitle) => ({
				render: () =>
					`<div class="text-start font-medium pl-2 text-ellipsis overflow-hidden w-36">${getTitle().title}</div>`
			}));
			return renderSnippet(snippet, { title: row.original.title });
		}
	},
	{
		accessorKey: 'authors',
		header: m['book_lookup.authors'](),
		cell: ({ row }) => {
			const snippet = createRawSnippet<[{ authors: { first_name: string; last_name: string }[] }]>(
				(getAuthors) => ({
					render: () =>
						`<div>${new Intl.ListFormat(getLocale(), { style: 'long', type: 'conjunction' }).format(
							getAuthors().authors.map(
								(x: { first_name: string; last_name: string }) => `${x.first_name} ${x.last_name}`
							)
						)}</div>`
				})
			);
			return renderSnippet(snippet, { authors: row.original.authors });
		}
	},
	{
		accessorKey: 'pages',
		header: () =>
			renderComponent(DataTableButton, {
				onclick: () => {
					const params = new URLSearchParams(page.url.searchParams);
					params.set('order_page', params.get('order_page') === 'asc' ? 'desc' : 'asc');
					params.delete('order_title');
					params.delete('order_year');
					params.delete('order_price');
					params.set('page', '1');
					goto(`?${params}`, { keepFocus: true });
				},
				title: m['book_lookup.pages'](),
				isActive: !!page.url.searchParams.get('order_page'),
				order: page.url.searchParams.get('order_page'),
				textPos: 'end'
			}),
		cell: ({ row }) => {
			const snippet = createRawSnippet<[{ pages: number }]>((getPages) => ({
				render: () => `<div class="text-end font-medium pr-8">${getPages().pages}</div>`
			}));
			return renderSnippet(snippet, { pages: row.original.pages });
		}
	},
	{
		accessorKey: 'release_year',
		header: () =>
			renderComponent(DataTableButton, {
				onclick: () => {
					const params = new URLSearchParams(page.url.searchParams);
					params.set('order_year', params.get('order_year') === 'asc' ? 'desc' : 'asc');
					params.delete('order_title');
					params.delete('order_page');
					params.delete('order_price');
					params.set('page', '1');
					goto(`?${params}`, { keepFocus: true });
				},
				title: m['book_lookup.release_year'](),
				isActive: !!page.url.searchParams.get('order_year'),
				order: page.url.searchParams.get('order_year'),
				textPos: 'end'
			}),
		cell: ({ row }) => {
			const snippet = createRawSnippet<[{ release_year: number }]>((getYear) => ({
				render: () => `<div class="text-end font-medium pr-8">${getYear().release_year}</div>`
			}));
			return renderSnippet(snippet, { release_year: row.original.release_year });
		}
	},
	{
		accessorKey: 'publisher',
		header: m['book_lookup.publisher'](),
		cell: ({ row }) => row.original.publisher.name
	},
	{
		accessorKey: 'genre',
		header: m['book_lookup.genre'](),
		cell: ({ row }) => row.original.genre.name
	},
	{
		accessorKey: 'price',
		header: () =>
			renderComponent(DataTableButton, {
				onclick: () => {
					const params = new URLSearchParams(page.url.searchParams);
					params.set('order_price', params.get('order_price') === 'asc' ? 'desc' : 'asc');
					params.delete('order_title');
					params.delete('order_year');
					params.delete('order_page');
					params.set('page', '1');
					goto(`?${params}`, { keepFocus: true });
				},
				title: m['book_lookup.price'](),
				isActive: !!page.url.searchParams.get('order_price'),
				order: page.url.searchParams.get('order_price'),
				textPos: 'end'
			}),
		cell: ({ row }) =>
			renderComponent(Price, {
				price: row.original.price,
				discountedPrice: getDiscountedPrice(
					row.original,
					page.data.discounts,
					page.data.user?.id as string
				)
			})
	}
];
