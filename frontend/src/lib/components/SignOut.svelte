<script lang="ts">
	import * as m from '$lib/paraglide/messages.js';
	import * as AlertDialog from '$lib/components/ui/alert-dialog/index.js';
	import { enhance } from '$app/forms';
	import Spinner from '$lib/components/ui/spinner/spinner.svelte';
	import Button from '$lib/components/ui/button/button.svelte';

	let open = $state(false);
	let signingOut = $state(false);

	const { class: className = '' } = $props();
</script>

<button class="cursor-pointer {className}" onclick={() => (open = true)}>
	{m['auth.sign_out']()}
</button>

<AlertDialog.Root bind:open>
	<AlertDialog.Content>
		<AlertDialog.Header>
			<AlertDialog.Title>{m['auth.sign_out']()}</AlertDialog.Title>
			<AlertDialog.Description>
				{m['auth.sign_out_dialog.description']()}
			</AlertDialog.Description>
		</AlertDialog.Header>
		<AlertDialog.Footer>
			{#if !signingOut}
				<AlertDialog.Cancel>
					{m['actions.cancel']()}
				</AlertDialog.Cancel>
			{/if}
			<form
				method="POST"
				action="/signout"
				use:enhance={() => {
					signingOut = true;
				}}
			>
				<Button class="cursor-pointer" type="submit" disabled={signingOut}>
					{#if signingOut}
						<Spinner />
					{:else}
						{m['auth.sign_out']()}
					{/if}
				</Button>
			</form>
		</AlertDialog.Footer>
	</AlertDialog.Content>
</AlertDialog.Root>
