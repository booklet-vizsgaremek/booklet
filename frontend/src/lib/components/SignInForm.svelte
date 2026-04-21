<script lang="ts">
	import { goto } from '$app/navigation';
	import { navigating, page } from '$app/state';
	import { Form, Input, Separator, Spinner } from '$lib/components/ui';
	import * as m from '$lib/paraglide/messages.js';
	import { signInSchema, type SignInSchema } from '$lib/schemas/signIn';
	import { type SuperValidated, type Infer, superForm } from 'sveltekit-superforms';
	import { zod4Client } from 'sveltekit-superforms/adapters';
	import LocaleSwitcher from './LocaleSwitcher.svelte';
	import FormLogo from './FormLogo.svelte';
	import { toast } from 'svelte-sonner';
	import { onMount } from 'svelte';

	let { data }: { data: { form: SuperValidated<Infer<SignInSchema>> } } = $props();

	onMount(() => {
		if (
			!!navigating.from?.url.pathname &&
			!['signin', 'signup'].includes(page.url.searchParams.get('ref') ?? '')
		) {
			toast.error(m['messages.signin_to_continue']());
		}
	});

	// svelte-ignore state_referenced_locally
	const form = superForm(data.form, {
		validators: zod4Client(signInSchema),
		onUpdate: ({ form }) => {
			if (form.message) {
				if (form.valid) {
					toast.success(form.message);
				} else {
					toast.error(form.message);
				}
			}
		}
	});

	const { form: formData, enhance, submitting } = form;
</script>

<form
	method="POST"
	use:enhance
	class="absolute top-1/2 left-1/2 flex h-full w-full -translate-1/2 flex-col items-center justify-center gap-4 bg-white p-12 *:w-full md:h-max md:w-1/2 md:justify-baseline xl:w-1/4 dark:bg-neutral-950"
>
	<FormLogo />
	<Form.Field {form} name="email">
		<Form.Control>
			{#snippet children({ props })}
				<Form.Label>{m['auth.email']()}</Form.Label>
				<Input {...props} bind:value={$formData.email} disabled={$submitting} />
			{/snippet}
		</Form.Control>
		<Form.FieldErrors />
	</Form.Field>
	<Form.Field {form} name="password">
		<Form.Control>
			{#snippet children({ props })}
				<Form.Label>{m['auth.password']()}</Form.Label>
				<Input type="password" {...props} bind:value={$formData.password} disabled={$submitting} />
			{/snippet}
		</Form.Control>
		<Form.FieldErrors />
	</Form.Field>
	<Form.Button class="mt-4 cursor-pointer" disabled={$submitting}>
		{#if $submitting}
			<Spinner />
		{:else}
			{m['auth.sign_in']()}
		{/if}
	</Form.Button>
	<div class="mt-4 flex h-max items-center justify-center space-x-4 text-sm">
		<Form.Button
			onclick={(e) => {
				e.preventDefault();
				goto('/');
			}}
			class="cursor-pointer bg-transparent p-0 text-foreground shadow-none hover:bg-transparent hover:underline"
			disabled={$submitting}
		>
			{m['navigation.back_to_home']()}
		</Form.Button>
		<Separator class="h-6" orientation="vertical" />
		<Form.Button
			onclick={(e) => {
				e.preventDefault();
				goto(
					`/sign-up${page.url.searchParams.get('redirect') ? `?redirect=${page.url.searchParams.get('redirect')}` : ''}`
				);
			}}
			class="cursor-pointer bg-transparent p-0 text-foreground shadow-none hover:bg-transparent hover:underline"
			disabled={$submitting}
		>
			{m['auth.sign_up']()}
		</Form.Button>
		<Separator class="h-6" orientation="vertical" />
		<LocaleSwitcher disabled={$submitting} />
	</div>
</form>
