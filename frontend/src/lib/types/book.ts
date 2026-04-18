import type { Genre } from './genre';

export type Book = {
	id: string;
	title: string;
	img_path: string | null;
	authors: { first_name: string; last_name: string }[];
	price: number;
	pages: number;
	release_year: number;
	publisher: { name: string };
	genre: Genre;
};
