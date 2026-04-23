<script lang="ts">
	import Button from '$lib/components/ui/button/button.svelte';
	import getGreeting from '$lib/utils/greeting';
	import * as m from '$lib/paraglide/messages.js';
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
</script>

<div class="mx-auto flex w-full flex-col gap-8 px-4 pt-16! pb-12 md:w-4/5 md:px-0 md:pb-24">
	<h1 class="text-3xl">{getGreeting(page.data.user?.first_name ?? null)}</h1>
	<h2 class="text-2xl">{m['management']()}</h2>
	<div class="flex flex-col gap-4 md:flex-row">
		<Button onclick={() => goto('/admin/users')} class="cursor-pointer">
			{m['title.users']()}
		</Button>
		<Button onclick={() => goto('/staff/orders')} class="cursor-pointer">
			{m['title.orders']()}
		</Button>
		<Button onclick={() => goto('/books')} class="cursor-pointer">
			{m['title.book_lookup']()}
		</Button>
	</div>
</div>
