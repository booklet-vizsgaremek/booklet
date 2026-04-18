import type { Book } from './book';

export type Genre = {
	id: string;
	name: string;
	books: Book[];
};
