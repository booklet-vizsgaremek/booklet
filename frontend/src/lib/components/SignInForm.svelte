<script lang="ts">
	import { goto } from '$app/navigation';
	import { page } from '$app/state';
	import * as Form from '$lib/components/ui/form/index.js';
	import { Input } from '$lib/components/ui/input/index.js';
	import { Separator } from '$lib/components/ui/separator/index.js';
	import * as m from '$lib/paraglide/messages.js';
	import { getLocale, locales, setLocale } from '$lib/paraglide/runtime';
	import { signInSchema, type SignInSchema } from '$lib/schemas/signIn';
	import { fade } from 'svelte/transition';
	import { type SuperValidated, type Infer, superForm } from 'sveltekit-superforms';
	import { zod4Client } from 'sveltekit-superforms/adapters';

	let darkMode = $state(false);

	$effect(() => {
		const mq = window.matchMedia('(prefers-color-scheme: dark)');
		darkMode = mq.matches;
		const handler = (e: MediaQueryListEvent) => (darkMode = e.matches);
		mq.addEventListener('change', handler);
		return () => mq.removeEventListener('change', handler);
	});

	let logoPromise = $derived(
		import(`$lib/assets/logos/logo_${getLocale()}_${darkMode ? 'white' : 'black'}.svg?url`)
	);

	let { data }: { data: { form: SuperValidated<Infer<SignInSchema>> } } = $props();

	// svelte-ignore state_referenced_locally
	const form = superForm(data.form, {
		validators: zod4Client(signInSchema)
	});

	const { form: formData, enhance } = form;
</script>

<form
	method="POST"
	use:enhance
	class="absolute top-1/2 left-1/2 flex h-full w-full -translate-1/2 flex-col items-center justify-center gap-4 bg-white p-12 *:w-full md:h-max md:w-1/4 md:justify-baseline dark:bg-neutral-950"
>
	{#await logoPromise}
		<div class="mb-6 h-12 w-32! animate-pulse bg-background"></div>
	{:then logo}
		<img
			transition:fade={{ delay: 100 }}
			id="logo"
			class="mb-6 h-12"
			src={logo.default}
			alt="Logo"
		/>
	{/await}
	<Form.Field {form} name="email">
		<Form.Control>
			{#snippet children({ props })}
				<Form.Label>{m['auth.email']()}</Form.Label>
				<Input {...props} bind:value={$formData.email} />
			{/snippet}
		</Form.Control>
		<Form.FieldErrors />
	</Form.Field>
	<Form.Field {form} name="password">
		<Form.Control>
			{#snippet children({ props })}
				<Form.Label>{m['auth.password']()}</Form.Label>
				<Input type="password" {...props} bind:value={$formData.password} />
			{/snippet}
		</Form.Control>
		<Form.FieldErrors />
	</Form.Field>
	<Form.Button class="mt-4 cursor-pointer">{m['auth.sign_in']()}</Form.Button>
	<div class="mt-4 flex h-max items-center justify-center space-x-4 text-sm">
		<Form.Button
			onclick={(e) => {
				e.preventDefault();
				history.back();
			}}
			class="cursor-pointer bg-transparent p-0 text-foreground hover:bg-transparent hover:underline"
			>{m['navigation.go_back']()}</Form.Button
		>
		<Separator class="h-6" orientation="vertical" />
		<Form.Button
			onclick={(e) => {
				e.preventDefault();
				goto(
					`/sign-up${page.url.searchParams.get('redirect') ? `?redirect=${page.url.searchParams.get('redirect')}` : ''}`
				);
			}}
			class="cursor-pointer bg-transparent p-0 text-foreground hover:bg-transparent hover:underline"
			>{m['auth.sign_up']()}</Form.Button
		>
		<Separator class="h-6" orientation="vertical" />
		<select
			onchange={(e) => setLocale(e.currentTarget.value as (typeof locales)[number])}
			class="border-0 bg-transparent"
		>
			{#each locales as locale}
				<option selected={locale === getLocale()}>{locale.toUpperCase()}</option>
			{/each}
		</select>
	</div>
</form>
