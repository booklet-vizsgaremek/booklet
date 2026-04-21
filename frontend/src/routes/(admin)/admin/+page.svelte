<script lang="ts">
	import getGreeting from '$lib/utils/greeting';
	import { page } from '$app/state';
	import DataTable from '$lib/components/users/data-table.svelte';
	import type { User } from '$lib/types';

	let {
		data
	}: {
		data: {
			users: User[];
			meta: { per_page: number; total: number };
		};
	} = $props();
</script>

<div class="mx-auto flex w-full flex-col gap-8 px-4 pt-16! pb-12 md:w-4/5 md:px-0 md:pb-24">
	<h1 class="text-3xl">{getGreeting(page.data.user?.first_name ?? null)}</h1>
	<DataTable {data} />
</div>
