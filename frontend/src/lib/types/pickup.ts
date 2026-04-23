import type { Book, Receipt } from '$lib/types';

export type Pickup = {
	id: string;
	receipt: Receipt;
	status: 'pending' | 'ready' | 'completed' | 'cancelled';
	completed_at: Date | null;
};
