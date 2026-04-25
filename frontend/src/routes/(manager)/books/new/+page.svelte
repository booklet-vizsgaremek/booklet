<script lang="ts">
	import BookForm from '$lib/components/BookForm.svelte';

	const { data } = $props();
</script>

<BookForm {data} />
