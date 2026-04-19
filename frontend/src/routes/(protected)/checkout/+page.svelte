<script lang="ts">
	import * as m from '$lib/paraglide/messages.js';
	import { getLocale } from '$lib/paraglide/runtime';
	import { cart } from '$lib/stores/cart.svelte';
	import * as Accordion from '$lib/components/ui/accordion';
	import * as Empty from '$lib/components/ui/empty/index.js';
	import Price, { formatCurrency } from '$lib/components/Price.svelte';
	import { Button } from '$lib/components/ui/button';
	import { Input } from '$lib/components/ui/input';
	import { enhance } from '$app/forms';
	import { toast } from 'svelte-sonner';
	import Label from '$lib/components/ui/label/label.svelte';
	import * as RadioGroup from '$lib/components/ui/radio-group';
	import { ShoppingCart, ChevronLeft, CircleCheck } from '@lucide/svelte';
	import { goto } from '$app/navigation';
	import FormLogo from '$lib/components/FormLogo.svelte';
	import { Spinner } from '$lib/components/ui/index.js';
	import { Checkbox } from '$lib/components/ui/checkbox/index.js';
	import type { Coupon, CartItem } from '$lib/types';
	import { getAllCartCoupons } from '$lib/stores/coupon.svelte.js';
	import CartQuantityControl from '$lib/components/CartQuantityControl.svelte';

	const { data } = $props();

	let currentStep = $state<'details' | 'delivery' | 'payment' | 'summary'>('details');
	let deliveryType = $state<'shipping' | 'pickup' | null>(null);
	let paymentType = $state<'by_card' | 'at_location' | null>(null);

	let couponCode = $state('');
	let appliedCoupon = $state<{ id: string; discount: number; code: string } | null>(null);
	let couponError = $state<string | null>(null);
	let couponLoading = $state(false);
	let termsAccepted = $state(false);
	let success = $state(false);
	let orderId = $state<string | null>(null);
	let submitting = $state(false);

	function isApplicable(
		coupon: Coupon | { id: string; discount: number; code: string },
		item: CartItem,
		userId: string
	): boolean {
		if ('user_id' in coupon && coupon.user_id && coupon.user_id !== userId) return false;
		if ('book_id' in coupon && coupon.book_id) return coupon.book_id === item.id;
		if ('genre_id' in coupon && coupon.genre_id) return coupon.genre_id === item.genre_id;
		return true;
	}

	const total = $derived(
		cart.items.reduce((sum, item) => {
			let price = item.price;
			for (const coupon of data.discounts) {
				if (isApplicable(coupon, item, data.user?.id ?? '')) {
					price *= 1 - coupon.discount / 100;
				}
			}
			if (appliedCoupon && isApplicable(appliedCoupon, item, data.user?.id ?? '')) {
				price *= 1 - appliedCoupon.discount / 100;
			}
			return sum + Math.round(price) * item.quantity;
		}, 0)
	);

	const handleApplyCoupon = async () => {
		if (!couponCode.trim()) return;
		couponLoading = true;
		couponError = null;

		const response = await fetch(`/api/coupons/validate?code=${couponCode}`, {
			credentials: 'include'
		});

		couponLoading = false;

		if (response.status === 404) {
			couponError = m['checkout.coupon_invalid']();
			return;
		}

		if (response.status === 403) {
			couponError = m['checkout.coupon_not_owned']();
			return;
		}

		if (!response.ok) {
			couponError = m['messages.server_error']();
			return;
		}

		const { data: coupon } = await response.json();

		if (!cart.items.some((item) => isApplicable(coupon, item, data.user?.id ?? ''))) {
			couponError = m['checkout.coupon_no_applicable_items']();
			return;
		}

		appliedCoupon = coupon;
		toast.success(m['checkout.coupon_applied']());
	};
</script>

{#if !success}
	<div class="container mx-auto px-12 py-16 md:px-24 md:py-32">
		<div class="mb-6 flex flex-row items-center gap-2">
			<button class="cursor-pointer" onclick={() => history.back()}><ChevronLeft /></button>
			<h1 class="text-3xl">{m['title.checkout']()}</h1>
		</div>
		{#if cart.itemCount === 0}
			<Empty.Root class="w-full border">
				<Empty.Media variant="icon">
					<ShoppingCart />
				</Empty.Media>
				<Empty.Header>
					<Empty.Title class="text-xl">{m['cart.empty']()}</Empty.Title>
				</Empty.Header>
				<Empty.Content>
					<Button
						variant="outline"
						class="cursor-pointer"
						size="sm"
						onclick={() => {
							goto('/');
						}}
					>
						{m['navigation.back_to_home']()}
					</Button>
				</Empty.Content>
			</Empty.Root>
		{:else}
			<div class="flex flex-col gap-12 lg:flex-row">
				<div class="w-full lg:w-1/2">
					<Accordion.Root type="single" bind:value={currentStep}>
						<Accordion.Item value="details">
							<Accordion.Trigger class={currentStep !== 'details' ? 'opacity-50' : ''}>
								{m['checkout.details']()}
							</Accordion.Trigger>
							<Accordion.Content class="flex flex-col items-end gap-2">
								<p class="w-full text-xl">
									{m['checkout.details_description']()}
								</p>
								<div class="flex w-full flex-col text-sm">
									<p class="mb-1!">
										{m['auth.full_name']()}: {getLocale() == 'hu'
											? `${data.user?.last_name} ${data.user?.first_name}`
											: `${data.user?.first_name} ${data.user?.last_name}`}
									</p>
									<p>{m['auth.email']()}: {data.user?.email}</p>
								</div>
								<Button class="mt-6 cursor-pointer" onclick={() => (currentStep = 'delivery')}>
									{m['checkout.continue']()}
								</Button>
							</Accordion.Content>
						</Accordion.Item>
						<Accordion.Item value="delivery">
							<Accordion.Trigger class={currentStep !== 'delivery' ? 'opacity-50' : ''}>
								{m['checkout.delivery']()}
							</Accordion.Trigger>
							<Accordion.Content class="flex flex-col items-end gap-3">
								<RadioGroup.Root bind:value={deliveryType}>
									<Label
										class="pointer-events-none flex items-start gap-3 rounded-lg border p-3 opacity-50 hover:bg-accent/50 has-aria-checked:border-blue-600 has-aria-checked:bg-blue-50 dark:has-aria-checked:border-blue-900 dark:has-aria-checked:bg-blue-950"
									>
										<RadioGroup.Item
											id="shipping"
											value="shipping"
											disabled
											class="data-[state=checked]:border-blue-600 data-[state=checked]:bg-blue-600 data-[state=checked]:text-white dark:data-[state=checked]:border-blue-700 dark:data-[state=checked]:bg-blue-700"
										/>
										<div class="grid font-normal">
											<p class="mb-2! text-sm leading-none font-medium">
												{m['checkout.shipping']()}
											</p>
											<p class="text-sm text-muted-foreground">
												{m['coming_soon']()}
											</p>
										</div>
									</Label>
									<Label
										class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 hover:bg-accent/50 has-aria-checked:border-blue-600 has-aria-checked:bg-blue-50 dark:has-aria-checked:border-blue-900 dark:has-aria-checked:bg-blue-950"
									>
										<RadioGroup.Item
											id="pickup"
											value="pickup"
											checked
											class="data-[state=checked]:border-blue-600 data-[state=checked]:bg-blue-600 data-[state=checked]:text-white dark:data-[state=checked]:border-blue-700 dark:data-[state=checked]:bg-blue-700"
										/>
										<div class="grid font-normal">
											<p class="mb-2! text-sm leading-none font-medium">{m['checkout.pickup']()}</p>
											<p class="text-sm text-muted-foreground">
												{m['checkout.pickup_description']()}
											</p>
										</div>
									</Label>
								</RadioGroup.Root>
								<Button
									class="cursor-pointer"
									onclick={() => {
										if (deliveryType != null) currentStep = 'payment';
										else toast.error(m['checkout.step_incomplete']());
									}}
									disabled={deliveryType == null}
								>
									{m['checkout.continue']()}
								</Button>
							</Accordion.Content>
						</Accordion.Item>
						<Accordion.Item value="payment">
							<Accordion.Trigger class={currentStep !== 'payment' ? 'opacity-50' : ''}>
								{m['checkout.payment']()}
							</Accordion.Trigger>
							<Accordion.Content class="flex flex-col items-end gap-3">
								<RadioGroup.Root bind:value={paymentType}>
									<Label
										class="pointer-events-none flex items-start gap-3 rounded-lg border p-3 opacity-50 hover:bg-accent/50 has-aria-checked:border-blue-600 has-aria-checked:bg-blue-50 dark:has-aria-checked:border-blue-900 dark:has-aria-checked:bg-blue-950"
									>
										<RadioGroup.Item
											id="by_card"
											value="by_card"
											disabled
											class="data-[state=checked]:border-blue-600 data-[state=checked]:bg-blue-600 data-[state=checked]:text-white dark:data-[state=checked]:border-blue-700 dark:data-[state=checked]:bg-blue-700"
										/>
										<div class="grid font-normal">
											<p class="mb-2! text-sm leading-none font-medium">
												{m['checkout.pay_by_card']()}
											</p>
											<p class="text-sm text-muted-foreground">
												{m['coming_soon']()}
											</p>
										</div>
									</Label>
									<Label
										class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 hover:bg-accent/50 has-aria-checked:border-blue-600 has-aria-checked:bg-blue-50 dark:has-aria-checked:border-blue-900 dark:has-aria-checked:bg-blue-950"
									>
										<RadioGroup.Item
											id="at_location"
											value="at_location"
											checked
											class="data-[state=checked]:border-blue-600 data-[state=checked]:bg-blue-600 data-[state=checked]:text-white dark:data-[state=checked]:border-blue-700 dark:data-[state=checked]:bg-blue-700"
										/>
										<div class="grid font-normal">
											<p class="mb-2! text-sm leading-none font-medium">
												{m['checkout.pay_at_location']()}
											</p>
											<p class="text-sm text-muted-foreground">
												{m['checkout.pay_at_location_description']()}
											</p>
										</div>
									</Label>
								</RadioGroup.Root>
								<Button
									class="cursor-pointer"
									onclick={() => {
										if (paymentType != null) currentStep = 'summary';
										else toast.error(m['checkout.step_incomplete']());
									}}
									disabled={paymentType == null}
								>
									{m['checkout.continue']()}
								</Button>
							</Accordion.Content>
						</Accordion.Item>
						<Accordion.Item
							value="summary"
							disabled={deliveryType == null}
							onclick={() => {
								if (deliveryType == null) toast.error(m['checkout.step_incomplete']());
							}}
						>
							<Accordion.Trigger class={currentStep !== 'summary' ? 'opacity-50' : ''}>
								{m['checkout.summary']()}
							</Accordion.Trigger>
							<Accordion.Content>
								<div class="mb-4 flex gap-2">
									<Input
										bind:value={couponCode}
										placeholder={m['checkout.coupon']()}
										disabled={!!appliedCoupon || couponLoading}
									/>
									<Button
										onclick={handleApplyCoupon}
										disabled={!!appliedCoupon || couponLoading || !couponCode.trim()}
										class="cursor-pointer"
									>
										{#if couponLoading}
											<Spinner />
										{:else}
											{m['checkout.coupon_apply']()}
										{/if}
									</Button>
								</div>
								{#if couponError}
									<p class="mb-4 text-sm text-destructive">{couponError}</p>
								{/if}
								<form
									method="POST"
									action="?/checkout"
									use:enhance={({ formData, cancel }) => {
										if (!termsAccepted) {
											toast.error(m['messages.terms_not_accepted']());
											cancel();
											return;
										}

										submitting = true;

										formData.append(
											'books',
											JSON.stringify(cart.items.map((i) => ({ id: i.id, quantity: i.quantity })))
										);

										formData.append(
											'coupons[]',
											JSON.stringify(
												getAllCartCoupons(
													cart.items,
													data.user?.id as string,
													appliedCoupon
														? [...data.discounts, $state.snapshot(appliedCoupon)]
														: data.discounts
												)
											)
										);

										return async ({ result }) => {
											submitting = false;

											if (result.type === 'success') {
												cart.clearCart();
												success = true;
												orderId = (result.data as { orderId: string }).orderId;
											} else {
												toast.error(m['messages.server_error']());
											}
										};
									}}
								>
									<div class="mb-6 flex items-center gap-2 *:cursor-pointer">
										<Checkbox
											id="terms"
											class="rounded-none"
											required
											bind:checked={termsAccepted}
										/>
										<Label for="terms">{m['checkout.accept_terms']()}</Label>
									</div>
									<p class="mb-4 text-lg font-semibold">
										{m['cart.total']()}: {formatCurrency(total)}
									</p>
									<Button
										type="submit"
										class="w-full cursor-pointer"
										disabled={submitting || !termsAccepted}
									>
										{#if submitting}
											<Spinner />
										{:else}
											{m['checkout.place_order']()}
										{/if}
									</Button>
								</form>
							</Accordion.Content>
						</Accordion.Item>
					</Accordion.Root>
				</div>
				<div class="w-full lg:w-1/2">
					<div class="flex flex-col gap-6">
						{#each cart.items as item}
							{@const discountedPrice = (() => {
								let price = item.price;
								for (const coupon of data.discounts) {
									if (isApplicable(coupon, item, data.user?.id ?? '')) {
										price *= 1 - coupon.discount / 100;
									}
								}
								if (appliedCoupon && isApplicable(appliedCoupon, item, data.user?.id ?? '')) {
									price *= 1 - appliedCoupon.discount / 100;
								}
								return Math.round(price);
							})()}
							<div class="flex gap-4">
								{#if item.img_path}
									<img
										src={item.img_path}
										alt={m['accessibility.book_cover']()}
										class="aspect-2/3 w-16 object-cover"
									/>
								{:else}
									<div
										class="flex aspect-2/3 w-16 items-start justify-center bg-blue-950 font-bold text-black"
									>
										<p
											class="relative top-1/4 bg-white p-px text-center text-[20%] dark:bg-foreground"
										>
											{item.title}
										</p>
									</div>
								{/if}
								<div class="flex flex-1 flex-col gap-2">
									<p class="font-semibold">{item.title}</p>
									<Price price={item.price} {discountedPrice} />
									<CartQuantityControl book={item} />
								</div>
								<p class="text-sm font-semibold">
									<Price price={item.price} {discountedPrice} quantity={item.quantity} />
								</p>
							</div>
						{/each}
					</div>
				</div>
			</div>
		{/if}
	</div>
{:else}
	<div
		class="absolute top-1/2 left-1/2 flex h-full w-full -translate-1/2 flex-col items-center justify-center gap-4 bg-white p-12 *:w-full md:h-max md:w-1/2 md:justify-baseline xl:w-1/4 dark:bg-neutral-950"
	>
		<FormLogo />
		<CircleCheck size={48} />
		<h2 class="mb-6 text-center text-xl">{m['messages.successful_order']()}</h2>
		<Button class="cursor-pointer" onclick={() => goto(`/orders/${orderId}`)}>
			{m['checkout.view_order']()}
		</Button>
		<Button class="cursor-pointer" onclick={() => goto('/')}>
			{m['navigation.back_to_home']()}
		</Button>
	</div>
{/if}
