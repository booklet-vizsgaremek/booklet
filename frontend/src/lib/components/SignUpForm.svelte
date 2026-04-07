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
	import clsx from 'clsx';
	import { twMerge } from 'tailwind-merge';
	import { toast } from 'svelte-sonner';
	import Button from './ui/button/button.svelte';
	import CircleCheck from '@lucide/svelte/icons/circle-check';

	let { data }: { data: { form: SuperValidated<Infer<SignUpSchema>> } } = $props();

	let success = $state(false);

	// svelte-ignore state_referenced_locally
	const form = superForm(data.form, {
		validators: zod4Client(signUpSchema),
		onUpdate: ({ result }) => {
			if (result.status === 200) {
				success = true;
			} else {
				toast.error(result.data.error);
			}
		}
	});

	const { form: formData, enhance, submitting } = form;
</script>

{#if !success}
	<form
		method="POST"
		use:enhance
		class="absolute top-1/2 left-1/2 flex h-full w-full -translate-1/2 flex-col items-center justify-center gap-4 bg-white p-12 *:w-full md:h-max md:w-1/2 md:justify-baseline xl:w-1/4 dark:bg-neutral-950"
	>
		<FormLogo />
		<div
			class={twMerge(clsx('flex gap-4', getLocale() === 'hu' ? 'flex-row-reverse' : 'flex-row'))}
		>
			<Form.Field {form} name="first_name" class="w-full">
				<Form.Control>
					{#snippet children({ props })}
						<Form.Label>{m['auth.first_name']()}</Form.Label>
						<Input {...props} bind:value={$formData.first_name} disabled={$submitting} />
					{/snippet}
				</Form.Control>
				<Form.FieldErrors />
			</Form.Field>
			<Form.Field {form} name="last_name" class="w-full">
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
						`/sign-in${page.url.searchParams.get('redirect') ? `?redirect=${page.url.searchParams.get('redirect')}&ref=signup` : '?ref=signup'}`
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
{:else}
	<div
		class="absolute top-1/2 left-1/2 flex h-full w-full -translate-1/2 flex-col items-center justify-center gap-4 bg-white p-12 *:w-full md:h-max md:w-1/2 md:justify-baseline xl:w-1/4 dark:bg-neutral-950"
	>
		<FormLogo />
		<CircleCheck size={48} />
		<h2 class="mb-6 text-center text-xl">{m['messages.successful_signup']()}</h2>
		<Button class="cursor-pointer" onclick={() => goto('/')}>
			{m['navigation.back_to_home']()}
		</Button>
	</div>
{/if}
