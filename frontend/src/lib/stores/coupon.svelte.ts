import type { Book, CartItem, Coupon } from '$lib/types';

export const getDiscountedPrice = (
	item: CartItem | Book,
	coupons: Coupon[],
	userId: string
): number => {
	const applicable = coupons.filter((coupon) => {
		if (coupon.user_id && coupon.user_id !== userId) return false;
		if (coupon.book_id) return coupon.book_id === item.id;
		if (coupon.genre_id)
			return 'genre' in item
				? coupon.genre_id === item.genre?.id
				: coupon.genre_id === item.genre_id;
		return true;
	});

	if (applicable.length === 0) return item.price;

	return Math.round(
		item.price * Math.max(0, 1 - applicable.reduce((sum, coupon) => sum + coupon.discount, 0) / 100)
	);
};

export const getCartTotal = (items: CartItem[], coupons: Coupon[], userId: string): number => {
	return items.reduce((sum, item) => {
		return sum + getDiscountedPrice(item, coupons, userId) * item.quantity;
	}, 0);
};

export const getAllCartCoupons = (items: CartItem[], userId: string, coupons: Coupon[]): Coupon[] =>
	coupons.filter(
		(x) =>
			items.find((y) => y.id == x.book_id) ||
			items.find((y) => y.genre_id == x.genre_id) ||
			userId == x.user_id
	);
