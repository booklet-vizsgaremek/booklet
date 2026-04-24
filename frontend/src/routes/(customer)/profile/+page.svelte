<script lang="ts">
	import Button, { buttonVariants } from '$lib/components/ui/button/button.svelte';
	import getGreeting from '$lib/utils/greeting';
	import * as m from '$lib/paraglide/messages.js';
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import SignOut from '$lib/components/SignOut.svelte';
	import PasswordChangeForm from '$lib/components/PasswordChangeForm.svelte';
	import UserDataChangeForm from '$lib/components/UserDataChangeForm.svelte';
	import * as AlertDialog from '$lib/components/ui/alert-dialog';
	import Spinner from '$lib/components/ui/spinner/spinner.svelte';
	import Label from '$lib/components/ui/label/label.svelte';
	import Input from '$lib/components/ui/input/input.svelte';
	import { enhance } from '$app/forms';

	let accountDeletionDialogOpen = $state(false);
	let isAccountDeletionLoading = $state(false);
	let email = $state('');

	const { data } = $props();
</script>

<AlertDialog.Root bind:open={accountDeletionDialogOpen}>
	<AlertDialog.Content>
		<AlertDialog.Header>
			<AlertDialog.Title>{m['auth.delete_account']()}</AlertDialog.Title>
			<AlertDialog.Description>
				{m['auth.delete_account_dialog.description']()}
			</AlertDialog.Description>
		</AlertDialog.Header>
		<div class="grid gap-3">
			<Label for="email">{m['auth.email']()}</Label>
			<Input id="email" name="email" bind:value={email} placeholder={page.data.user?.email} />
		</div>
		<AlertDialog.Footer>
			{#if !isAccountDeletionLoading}
				<AlertDialog.Cancel class="cursor-pointer">{m['actions.cancel']()}</AlertDialog.Cancel>
			{/if}
			<form
				action="?/deleteAccount"
				method="POST"
				use:enhance={async () => {
					isAccountDeletionLoading = true;
				}}
			>
				<AlertDialog.Action
					class="cursor-pointer"
					variant="destructive"
					disabled={isAccountDeletionLoading || email !== page.data.user?.email}
				>
					{#if isAccountDeletionLoading}
						<Spinner />
					{:else}
						{m['auth.delete_account']()}
					{/if}
				</AlertDialog.Action>
			</form>
		</AlertDialog.Footer>
	</AlertDialog.Content>
</AlertDialog.Root>

<div class="mx-auto flex w-full flex-col gap-8 px-4 pt-16! pb-12 md:w-4/5 md:px-0 md:pb-24">
	<h1 class="text-3xl">{getGreeting(page.data.user?.first_name ?? null)}</h1>
	<h2 class="text-2xl">{m['auth.account']()}</h2>
	<h3 class="text-xl">{m['auth.general']()}</h3>
	<div class="flex flex-row gap-4">
		<Button onclick={() => goto('/orders')} class="cursor-pointer">
			{m['title.orders']()}
		</Button>
		<Button onclick={() => goto('/wishlist')} class="cursor-pointer">
			{m['title.wishlist']()}
		</Button>
		<SignOut class={buttonVariants({ variant: 'default' })} />
	</div>
	<h3 class="mt-6 text-xl!">{m['auth.change_user_data']()}</h3>
	<UserDataChangeForm form={data.userDataForm} />
	<h3 class="mt-6 text-xl!">{m['auth.change_password']()}</h3>
	<PasswordChangeForm form={data.passwordForm} />
	<h3 class="mt-6 text-xl!">{m['auth.danger_zone']()}</h3>
	<Button
		onclick={() => (accountDeletionDialogOpen = true)}
		variant="destructive"
		class="cursor-pointer md:w-1/3"
	>
		{m['auth.delete_account']()}
	</Button>
</div>
