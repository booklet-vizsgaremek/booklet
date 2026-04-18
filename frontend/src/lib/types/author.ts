import type { Book } from './book';

export type Author = {
	id: string;
	first_name: string;
	last_name: string;
	biography: string;
	books: Book[];
};
