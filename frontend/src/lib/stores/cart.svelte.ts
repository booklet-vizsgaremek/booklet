import { browser } from '$app/environment';
import type { CartItem } from '$lib/types/cart';

export const MAX_QUANTITY_PER_ITEM = 5;

function createCartStore() {
	let items = $state<CartItem[]>(browser ? JSON.parse(localStorage.getItem('cart') ?? '[]') : []);

	$effect.root(() => {
		$effect(() => {
			localStorage.setItem('cart', JSON.stringify(items));
		});
	});

	const total = $derived(items.reduce((sum, item) => sum + item.price * item.quantity, 0));
	const itemCount = $derived(items.reduce((sum, item) => sum + item.quantity, 0));

	return {
		get items() {
			return items;
		},
		get total() {
			return total;
		},
		get itemCount() {
			return itemCount;
		},

		addToCart(book: CartItem) {
			const existing = items.find((i) => i.id === book.id);
			if (existing) {
				const newQuantity = existing.quantity + 1;
				if (newQuantity > Math.min(MAX_QUANTITY_PER_ITEM, existing.stock)) return;
				items = items.map((i) => (i.id === book.id ? { ...i, quantity: newQuantity } : i));
			} else {
				if (book.stock === 0) return;
				items = [...items, { ...book, quantity: 1 }];
			}
		},

		removeFromCart(id: string) {
			items = items.filter((i) => i.id !== id);
		},

		updateQuantity(id: string, quantity: number) {
			const item = items.find((i) => i.id === id);
			if (!item) return;
			const capped = Math.min(quantity, Math.min(MAX_QUANTITY_PER_ITEM, item.stock));
			if (capped < 1) {
				items = items.filter((i) => i.id !== id);
				return;
			}
			items = items.map((i) => (i.id === id ? { ...i, quantity: capped } : i));
		},

		clearCart() {
			items = [];
		}
	};
}

export const cart = createCartStore();
