<script lang="ts">
	import DataTable from '$lib/components/books/data-table.svelte';
	import type { Book, Author, Genre, Publisher } from '$lib/types';
	import { columns } from '$lib/components/books/columns';

	let {
		data
	}: {
		data: {
			books: Book[];
			authors: Author[];
			genres: Genre[];
			publishers: Publisher[];
			meta: { per_page: number; total: number };
		};
	} = $props();
</script>

<DataTable {data} {columns} />
