<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import { Form, Input, Separator, Spinner } from '$lib/components/ui';
	import * as m from '$lib/paraglide/messages.js';
	import { getLocale } from '$lib/paraglide/runtime';
	import { signUpSchema, type SignUpSchema } from '$lib/schemas/signUp';
	import { type SuperValidated, type Infer, superForm } from 'sveltekit-superforms';
	import { zod4Client } from 'sveltekit-superforms/adapters';
	import LocaleSwitcher from './LocaleSwitcher.svelte';
	import FormLogo from './FormLogo.svelte';

	let { data }: { data: { form: SuperValidated<Infer<SignUpSchema>> } } = $props();

	// svelte-ignore state_referenced_locally
	const form = superForm(data.form, {
		validators: zod4Client(signUpSchema)
	});

	const { form: formData, enhance, submitting } = form;
</script>

<form
	method="POST"
	use:enhance
	class="absolute top-1/2 left-1/2 flex h-full w-full -translate-1/2 flex-col items-center justify-center gap-4 bg-white p-12 *:w-full md:h-max md:w-1/4 md:justify-baseline dark:bg-neutral-950"
>
	<FormLogo />
	<div class={`flex ${getLocale() == 'hu' ? 'flex-row-reverse' : 'flex-row'} gap-4`}>
		<Form.Field {form} name="first_name">
			<Form.Control>
				{#snippet children({ props })}
					<Form.Label>{m['auth.first_name']()}</Form.Label>
					<Input {...props} bind:value={$formData.first_name} disabled={$submitting} />
				{/snippet}
			</Form.Control>
			<Form.FieldErrors />
		</Form.Field>
		<Form.Field {form} name="last_name">
			<Form.Control>
				{#snippet children({ props })}
					<Form.Label>{m['auth.last_name']()}</Form.Label>
					<Input {...props} bind:value={$formData.last_name} disabled={$submitting} />
				{/snippet}
			</Form.Control>
			<Form.FieldErrors />
		</Form.Field>
	</div>
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
	<Form.Field {form} name="password_confirm">
		<Form.Control>
			{#snippet children({ props })}
				<Form.Label>{m['auth.password_confirm']()}</Form.Label>
				<Input
					type="password"
					{...props}
					bind:value={$formData.password_confirm}
					disabled={$submitting}
				/>
			{/snippet}
		</Form.Control>
		<Form.FieldErrors />
	</Form.Field>
	<Form.Button class="mt-4 cursor-pointer" disabled={$submitting}>
		{#if $submitting}
			<Spinner />
		{:else}
			{m['auth.sign_up']()}
		{/if}
	</Form.Button>
	<div class="mt-4 flex h-max items-center justify-center space-x-4 text-sm">
		<Form.Button
			onclick={(e) => {
				e.preventDefault();
				history.back();
			}}
			class="cursor-pointer bg-transparent p-0 text-foreground shadow-none hover:bg-transparent hover:underline"
			disabled={$submitting}
		>
			{m['navigation.go_back']()}
		</Form.Button>
		<Separator class="h-6" orientation="vertical" />
		<Form.Button
			onclick={(e) => {
				e.preventDefault();
				goto(
					`/sign-in${page.url.searchParams.get('redirect') ? `?redirect=${page.url.searchParams.get('redirect')}` : ''}`
				);
			}}
			class="cursor-pointer bg-transparent p-0 text-foreground shadow-none hover:bg-transparent hover:underline"
			disabled={$submitting}
		>
			{m['auth.sign_in']()}
		</Form.Button>
		<Separator class="h-6" orientation="vertical" />
		<LocaleSwitcher disabled={$submitting} />
	</div>
</form>
