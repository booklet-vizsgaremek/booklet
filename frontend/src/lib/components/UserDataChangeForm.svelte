<script lang="ts">
	import Button from '$lib/components/ui/button/button.svelte';
	import { Form, Input, Spinner } from '$lib/components/ui';
	import { type SuperValidated, type Infer, superForm } from 'sveltekit-superforms';
	import { zod4Client } from 'sveltekit-superforms/adapters';
	import { userDataChangeSchema, type UserDataChangeSchema } from '$lib/schemas/userDataChange';
	import { toast } from 'svelte-sonner';
	import * as m from '$lib/paraglide/messages.js';
	import { page } from '$app/state';

	let { form }: { form: SuperValidated<Infer<UserDataChangeSchema>> } = $props();

	// svelte-ignore state_referenced_locally
	const userDataChangeForm = superForm(form, {
		validators: zod4Client(userDataChangeSchema),
		resetForm: false,
		onUpdate: ({ result }) => {
			if (result.status === 200) {
				toast.success(m['messages.successful_user_data_change']());
			} else if (result.data?.error) {
				toast.error(result.data.error);
			}
		}
	});

	const { form: formData, enhance: superFormEnhance, submitting } = userDataChangeForm;
</script>

<form
	class="flex w-full flex-col gap-4 md:w-1/3"
	method="POST"
	action="?/userDataChange"
	use:superFormEnhance
>
	<Form.Field form={userDataChangeForm} name="email">
		<Form.Control>
			{#snippet children({ props })}
				<Form.Label>{m['auth.email']()}</Form.Label>
				<Input type="email" {...props} bind:value={$formData.email} disabled={$submitting} />
			{/snippet}
		</Form.Control>
		<Form.FieldErrors />
	</Form.Field>
	<Form.Field form={userDataChangeForm} name="first_name">
		<Form.Control>
			{#snippet children({ props })}
				<Form.Label>{m['auth.first_name']()}</Form.Label>
				<Input type="text" {...props} bind:value={$formData.first_name} disabled={$submitting} />
			{/snippet}
		</Form.Control>
		<Form.FieldErrors />
	</Form.Field>
	<Form.Field form={userDataChangeForm} name="last_name">
		<Form.Control>
			{#snippet children({ props })}
				<Form.Label>{m['auth.last_name']()}</Form.Label>
				<Input type="text" {...props} bind:value={$formData.last_name} disabled={$submitting} />
			{/snippet}
		</Form.Control>
		<Form.FieldErrors />
	</Form.Field>
	<Button
		type="submit"
		class="mt-4 cursor-pointer"
		disabled={$submitting ||
			($formData.email === page.data.user?.email &&
				$formData.first_name === page.data.user?.first_name &&
				$formData.last_name === page.data.user?.last_name)}
	>
		{#if $submitting}
			<Spinner />
		{:else}
			{m['auth.change_user_data']()}
		{/if}
	</Button>
</form>
