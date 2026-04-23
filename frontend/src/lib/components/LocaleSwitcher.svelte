<script lang="ts">
	import { getLocale, locales, setLocale } from '$lib/paraglide/runtime';
	import * as Select from '$lib/components/ui/select';
	import { Globe } from '@lucide/svelte';

	let {
		disabled = false,
		classes = 'border-0 bg-transparent! disabled:pointer-events-none disabled:opacity-50'
	} = $props();

	let currentLocale = $state<string>(getLocale());

	function handleChange(value: string) {
		setLocale(value as (typeof locales)[number]);
		currentLocale = value;
	}
</script>

{#key currentLocale}
	<Select.Root type="single" value={currentLocale} onValueChange={handleChange} {disabled}>
		<Select.Trigger class={classes}>
			<div class="flex flex-row items-center gap-2">
				<Globe />
				{currentLocale.toUpperCase()}
			</div>
		</Select.Trigger>
		<Select.Content>
			{#each locales as locale}
				<Select.Item class="cursor-pointer" value={locale} disabled={locale === currentLocale}>
					{locale.toUpperCase()}
				</Select.Item>
			{/each}
		</Select.Content>
	</Select.Root>
{/key}
