<script lang="ts">
	import Button from '$lib/components/ui/button/button.svelte';
	import { Form, Input, Spinner } from '$lib/components/ui';
	import { type SuperValidated, type Infer, superForm } from 'sveltekit-superforms';
	import { zod4Client } from 'sveltekit-superforms/adapters';
	import { passwordChangeSchema, type PasswordChangeSchema } from '$lib/schemas/passwordChange';
	import { toast } from 'svelte-sonner';
	import * as m from '$lib/paraglide/messages.js';

	let { form }: { form: SuperValidated<Infer<PasswordChangeSchema>> } = $props();

	// svelte-ignore state_referenced_locally
	const passwordChangeForm = superForm(form, {
		validators: zod4Client(passwordChangeSchema),
		onUpdate: ({ result }) => {
			if (result.status === 200) {
				toast.success(m['messages.successful_password_change']());
			} else if (result.data?.error) {
				toast.error(result.data.error);
			}
		}
	});

	const { form: formData, enhance: superFormEnhance, submitting } = passwordChangeForm;
</script>

<form
	class="flex w-full flex-col gap-4 md:w-1/3"
	method="POST"
	action="?/passwordChange"
	use:superFormEnhance
>
	<Form.Field form={passwordChangeForm} name="current_password">
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
	<Form.Field form={passwordChangeForm} name="password">
		<Form.Control>
			{#snippet children({ props })}
				<Form.Label>{m['auth.password']()}</Form.Label>
				<Input type="password" {...props} bind:value={$formData.password} disabled={$submitting} />
			{/snippet}
		</Form.Control>
		<Form.FieldErrors />
	</Form.Field>
	<Form.Field form={passwordChangeForm} name="password_confirmation">
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
	<Button
		type="submit"
		class="mt-4 cursor-pointer"
		disabled={$submitting ||
			$formData.password === '' ||
			$formData.current_password === '' ||
			$formData.password_confirmation === ''}
	>
		{#if $submitting}
			<Spinner />
		{:else}
			{m['auth.change_password']()}
		{/if}
	</Button>
</form>
