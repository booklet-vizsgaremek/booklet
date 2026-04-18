import type { Book } from './book';

export type Publisher = {
	id: string;
	name: string;
	books: Book[];
};
