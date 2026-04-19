<script lang="ts">
	import { MediaQuery } from 'svelte/reactivity';
	import { Button } from '$lib/components/ui/button';
	import * as Tooltip from '$lib/components/ui/tooltip/index.js';
	import Minus from '@lucide/svelte/icons/minus';
	import Plus from '@lucide/svelte/icons/plus';
	import Trash from '@lucide/svelte/icons/trash';
	import ShoppingCart from '@lucide/svelte/icons/shopping-cart';
	import { cart, MAX_QUANTITY_PER_ITEM } from '$lib/stores/cart.svelte';
	import { toast } from 'svelte-sonner';
	import * as m from '$lib/paraglide/messages.js';
	import type { Book, CartItem } from '$lib/types';
	import Input from '$lib/components/ui/input/input.svelte';

	const {
		book,
		class: className = '',
		showLabel = false
	}: {
		book: Book | CartItem;
		class?: string;
		showLabel?: boolean;
	} = $props();

	const cartItem = $derived(cart.items.find((i) => i.id === book.id));
	const atLimit = $derived(
		cartItem ? cartItem.quantity >= Math.min(MAX_QUANTITY_PER_ITEM, book.stock) : false
	);
	const isDesktop = new MediaQuery('(min-width: 768px)');

	const handleModification = (quantity: number) => {
		if (!cartItem) return;

		const parsed = Math.floor(Number(quantity));

		if (isNaN(parsed) || parsed < 1) {
			cart.updateQuantity(book.id, 1);
			return;
		}

		const capped = Math.min(parsed, MAX_QUANTITY_PER_ITEM, book.stock);

		if (parsed > capped) {
			toast.error(m['cart.max_quantity']({ max: Math.min(MAX_QUANTITY_PER_ITEM, book.stock) }));
		}

		cart.updateQuantity(book.id, capped);
	};

	const handleIncrement = () => {
		if (!cartItem) return;
		if (atLimit) {
			toast.error(m['cart.max_quantity']({ max: MAX_QUANTITY_PER_ITEM }));
			return;
		}
		cart.updateQuantity(book.id, cartItem.quantity + 1);
	};

	const handleDecrement = () => {
		if (!cartItem) return;
		if (cartItem.quantity === 1) handleRemove();
		else cart.updateQuantity(book.id, cartItem.quantity - 1);
	};

	const handleAdd = () => {
		if (book.stock < 1) {
			toast.error(m['out_of_stock']());
			return;
		}

		cart.addToCart(
			'genre' in book ? { ...book, quantity: 1, genre_id: book.genre.id } : { ...book, quantity: 1 }
		);
		toast.success(m['actions.added_to_cart']());
	};

	const handleRemove = () => {
		cart.removeFromCart(book.id);
		toast.success(m['actions.removed_from_cart']());
	};
</script>

{#if cartItem}
	<div class="mt-4 flex w-full flex-row items-center gap-2 md:m-0 md:w-auto {className}">
		<Button
			variant="outline"
			size="icon"
			class="w-1/4 cursor-pointer md:w-9"
			onclick={handleDecrement}
		>
			<Minus size={12} />
		</Button>
		<Input
			class="w-1/4 text-center text-base md:w-12"
			type="text"
			inputmode="numeric"
			pattern="[0-9]*"
			onchange={(e) => handleModification(Number((e.target as HTMLInputElement).value))}
			value={cartItem.quantity}
		/>
		<Button
			variant="outline"
			size="icon"
			class="w-1/4 cursor-pointer md:w-9"
			disabled={atLimit}
			onclick={handleIncrement}
		>
			<Plus size={12} />
		</Button>
		<Button
			variant="ghost"
			size="icon"
			class="w-1/4 cursor-pointer text-destructive md:w-9"
			onclick={handleRemove}
		>
			<Trash size={12} />
		</Button>
	</div>
{:else}
	<Tooltip.Root>
		<Tooltip.Trigger>
			{#snippet child({ props }: { props: Record<string, unknown> })}
				<Button
					{...props}
					variant="default"
					class="mt-4 w-full cursor-pointer md:m-0 md:w-max"
					onclick={handleAdd}
					disabled={book.stock < 1}
				>
					<ShoppingCart />
					{#if book.stock < 1}
						{m['out_of_stock']()}
					{:else if showLabel || !isDesktop.current}
						{m['actions.add_to_cart']()}
					{/if}
				</Button>
			{/snippet}
		</Tooltip.Trigger>
		<Tooltip.Content>
			<p>{m['actions.add_to_cart']()}</p>
		</Tooltip.Content>
	</Tooltip.Root>
{/if}
