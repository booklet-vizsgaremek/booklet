<script lang="ts">
	import Button from '$lib/components/ui/button/button.svelte';
	import getGreeting from '$lib/utils/greeting';
	import * as m from '$lib/paraglide/messages.js';
	import { Form, Input, Spinner } from '$lib/components/ui';
	import { type SuperValidated, type Infer, superForm } from 'sveltekit-superforms';
	import { zod4Client } from 'sveltekit-superforms/adapters';
	import { passwordChangeSchema, type PasswordChangeSchema } from '$lib/schemas/passwordChange';
	import { enhance } from '$app/forms';
	import { toast } from 'svelte-sonner';

	let {
		data
	}: { data: { user: App.Locals['user']; form: SuperValidated<Infer<PasswordChangeSchema>> } } =
		$props();

	// svelte-ignore state_referenced_locally
	const form = superForm(data.form, {
		validators: zod4Client(passwordChangeSchema),
		onUpdate: ({ result }) => {
			if (result.status === 200) {
				toast.success(m['messages.successful_password_change']());
			} else if (result.data?.error) {
				toast.error(result.data.error);
			}
		}
	});

	const { form: formData, enhance: superFormEnhance, submitting } = form;

	let signingOut = $state(false);
</script>

<div class="flex flex-col gap-8 p-12 md:p-24">
	<h1 class="text-3xl!">{getGreeting(data.user?.first_name ?? null)}</h1>
	<h2 class="text-2xl!">{m['auth.account']()}</h2>
	<h3 class="text-xl!">{m['auth.general']()}</h3>
	<form
		method="POST"
		action="?/signout"
		use:enhance={() => {
			signingOut = true;
		}}
	>
		<Button type="submit" class="mb-6 cursor-pointer">
			{#if signingOut}
				<Spinner />
			{:else}
				{m['auth.sign_out']()}
			{/if}
		</Button>
	</form>
	<h3 class="text-xl!">{m['auth.change_password']()}</h3>
	<form
		class="flex w-full flex-col gap-4 md:w-1/3"
		method="POST"
		action="?/passwordChange"
		use:superFormEnhance
	>
		<Form.Field {form} name="current_password">
			<Form.Control>
				{#snippet children({ props })}
					<Form.Label>{m['auth.current_password']()}</Form.Label>
					<Input
						type="password"
						{...props}
						bind:value={$formData.current_password}
						disabled={$submitting}
					/>
				{/snippet}
			</Form.Control>
			<Form.FieldErrors />
		</Form.Field>
		<Form.Field {form} name="password">
			<Form.Control>
				{#snippet children({ props })}
					<Form.Label>{m['auth.password']()}</Form.Label>
					<Input
						type="password"
						{...props}
						bind:value={$formData.password}
						disabled={$submitting}
					/>
				{/snippet}
			</Form.Control>
			<Form.FieldErrors />
		</Form.Field>
		<Form.Field {form} name="password_confirmation">
			<Form.Control>
				{#snippet children({ props })}
					<Form.Label>{m['auth.password_confirmation']()}</Form.Label>
					<Input
						type="password"
						{...props}
						bind:value={$formData.password_confirmation}
						disabled={$submitting}
					/>
				{/snippet}
			</Form.Control>
			<Form.FieldErrors />
		</Form.Field>
		<Button type="submit" class="mt-4 cursor-pointer" disabled={$submitting}>
			{#if $submitting}
				<Spinner />
			{:else}
				{m['auth.change_password']()}
			{/if}
		</Button>
	</form>
</div>
