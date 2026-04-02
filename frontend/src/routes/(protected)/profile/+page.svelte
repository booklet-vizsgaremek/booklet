<script>
	import Button from '$lib/components/ui/button/button.svelte';
	import getGreeting from '$lib/utils/greeting';
	import * as m from '$lib/paraglide/messages.js';
	import { enhance } from '$app/forms';
	import { Spinner } from '$lib/components/ui/index';

	const { data } = $props();
	let signingOut = $state(false);
</script>

<div class="container p-24">
	<h1 class="text-3xl!">{getGreeting(data.user?.first_name ?? null)}</h1>
	<form
		method="POST"
		action="?/signout"
		use:enhance={() => {
			signingOut = true;
		}}
	>
		<Button type="submit" class="mt-12 cursor-pointer">
			{#if signingOut}
				<Spinner />
			{:else}
				{m['auth.sign_out']()}
			{/if}
		</Button>
	</form>
</div>
