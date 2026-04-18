<script lang="ts">
	import { page } from '$app/state';
	import Button from '$lib/components/ui/button/button.svelte';
	import * as m from '$lib/paraglide/messages.js';
	import { goto } from '$app/navigation';
</script>

<div
	class="absolute top-1/2 left-1/2 flex -translate-1/2 flex-col items-center justify-center gap-4"
>
	<h1 class="text-8xl">{page.status}</h1>
	<h3 class="text-center text-2xl">
		{page.status === 404 ? m['messages.page_not_found']() : m['messages.server_error']()}
	</h3>
	<Button class="mt-4 cursor-pointer" onclick={() => goto('/')}>
		{m['navigation.back_to_home']()}
	</Button>
</div>
