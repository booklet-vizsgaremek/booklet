import type { Book } from '$lib/types';

function createWishlist() {
	let items = $state<Book[]>([]);
	return {
		get items() {
			return items;
		},
		init(books: Book[]) {
			items = books;
		},
		isWishlisted(bookId: string): boolean {
			return items.some((b) => b.id === bookId);
		},
		async toggle(book: Book): Promise<'added' | 'removed'> {
			if (this.isWishlisted(book.id)) {
				await fetch(`/api/wishlists/${book.id}`, { method: 'DELETE' });
				items = items.filter((b) => b.id !== book.id);
				return 'removed';
			} else {
				await fetch('/api/wishlists', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ book_id: book.id })
				});
				items = [...items, book];
				return 'added';
			}
		},
		clear() {
			items = [];
		}
	};
}

export const wishlist = createWishlist();
