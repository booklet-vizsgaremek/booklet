import type { CartItem } from '$lib/types/cart';
import type { Coupon } from '$lib/types/coupon';

export const getDiscountedPrice = (item: CartItem, coupons: Coupon[], userId: string): number => {
	const applicable = coupons.filter((coupon) => {
		if (coupon.user_id && coupon.user_id !== userId) return false;
		if (coupon.book_id) return coupon.book_id === item.id;
		if (coupon.genre_id) return coupon.genre_id === item.genre_id;
		return true;
	});

	if (applicable.length === 0) return item.price;

	const totalDiscount = applicable.reduce((sum, coupon) => sum + coupon.discount, 0);
	const discountMultiplier = Math.max(0, 1 - totalDiscount / 100);

	return Math.round(item.price * discountMultiplier);
};

export const getCartTotal = (items: CartItem[], coupons: Coupon[], userId: string): number => {
	return items.reduce((sum, item) => {
		return sum + getDiscountedPrice(item, coupons, userId) * item.quantity;
	}, 0);
};
