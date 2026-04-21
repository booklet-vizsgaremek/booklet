export type User = {
	id: string;
	first_name: string;
	last_name: string;
	email: string;
	role: 'admin' | 'manager' | 'staff' | 'customer' | null;
	receipts: [];
};
