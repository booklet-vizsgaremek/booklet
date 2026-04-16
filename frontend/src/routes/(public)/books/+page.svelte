<script lang="ts">
	import DataTable from '$lib/components/books/data-table.svelte';
	import {
		columns,
		type Book,
		type Author,
		type Genre,
		type Publisher
	} from '$lib/components/books/columns';

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
