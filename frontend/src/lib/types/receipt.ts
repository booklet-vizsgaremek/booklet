import type { Coupon, User, Pickup, Book } from '$lib/types';

export type Receipt = {
	id: string;
	date: Date;
	user: User;
	books: (Book & { price_at_purchase: number; quantity: number })[];
	coupons: Coupon[];
	pickup: Pickup;
};
