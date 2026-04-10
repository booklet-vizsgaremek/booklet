export type Coupon = {
	id: string;
	book_id: string | null;
	genre_id: string | null;
	user_id: string | null;
	discount: number;
	starts_at: string;
	ends_at: string;
	code: string | null;
	book: { id: string; genre_id: string } | null;
	genre: { id: string } | null;
};
