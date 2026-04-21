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
		{(() => {
			switch (page.status) {
				case 404:
					return m['messages.page_not_found']();
					break;
				case 403:
					return m['messages.forbidden']();
					break;
				default:
					return m['messages.server_error']();
					break;
			}
		})()}
	</h3>
	<Button
		class="mt-4 cursor-pointer"
		onclick={() => {
			goto(!page.data.user || page.data.user.role === 'customer' ? '/' : `/${page.data.user.role}`);
		}}
	>
		{page.status === 403 ? m['navigation.back']() : m['navigation.back_to_home']()}
	</Button>
</div>
