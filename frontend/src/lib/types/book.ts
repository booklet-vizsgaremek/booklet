import type { Author, Genre, Publisher } from '$lib/types';

export type Book = {
	id: string;
	title: string;
	img_path: string | null;
	stock: number;
	authors: Author[];
	price: number;
	pages: number;
	release_year: number;
	publisher: Publisher;
	genre: Genre;
};
