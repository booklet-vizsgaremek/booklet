<script lang="ts" module>
	import { getLocale } from '$lib/paraglide/runtime';

	export const formatCurrency = (amount: number) =>
		new Intl.NumberFormat(getLocale(), {
			style: 'currency',
			currency: 'HUF',
			maximumFractionDigits: 0
		}).format(amount);
</script>

<script lang="ts">
	import { Badge } from '$lib/components/ui/badge/index.js';

	let { price, discountedPrice = price, quantity = 0, textSize = 'sm' } = $props();
</script>

<div class="flex flex-wrap items-center gap-2">
	{#if discountedPrice !== price}
		{#if quantity > 1}
			<span class={`text-${textSize} font-semibold text-foreground`}>
				{quantity} x
			</span>
		{/if}
		<span class={`text-${textSize} text-muted-foreground line-through`}>
			{formatCurrency(price)}
		</span>
		<span class={`text-${textSize} font-semibold text-foreground`}>
			{formatCurrency(discountedPrice)}
		</span>
		<Badge variant="warning">-{Math.round(((price - discountedPrice) / price) * 100)}%</Badge>
		{#if quantity > 1}
			<span class={`text-${textSize} font-semibold text-foreground`}>
				({formatCurrency(quantity * discountedPrice)})
			</span>
		{/if}
	{:else}
		<span class={`text-${textSize} text-foreground`}>
			{quantity > 0 ? `${quantity} x ` : ''}{formatCurrency(price)}
		</span>
		{#if quantity > 1}
			<span class={`text-${textSize} text-foreground`}>
				({formatCurrency(quantity * price)})
			</span>
		{/if}
	{/if}
</div>
