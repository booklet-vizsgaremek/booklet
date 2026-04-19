import type { Book } from './book';

export type Author = {
	id: string;
	name: string;
	biography_en: string;
	biography_hu: string;
	books: Book[];
};
