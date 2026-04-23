import type { Book } from './book';

export type Genre = {
	id: string;
	name_en: string;
	name_hu: string;
	books: Book[];
};
