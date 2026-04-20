import type { User, Book } from '$lib/typess';

declare global {
	namespace App {
		// interface Error {}
		interface Locals {
			user: User | null;
		}
		interface PageData {
			wishlist?: Book[];
		}
		// interface PageState {}
		// interface Platform {}
	}
}

export {};
