<script lang="ts">
	import * as m from '$lib/paraglide/messages.js';
	import { Button } from '$lib/components/ui/button';
	import { Input } from '$lib/components/ui/input';
	import { BookOpen, Tag, Calendar, ChevronLeft, Building } from '@lucide/svelte';
	import { fileProxy, superForm } from 'sveltekit-superforms';
	import * as Form from '$lib/components/ui/form';
	import type { Author, Genre, Publisher, Book } from '$lib/types';
	import { getLocale } from '$lib/paraglide/runtime';
	import ComboSelect from '$lib/components/ComboSelect.svelte';
	import { PUBLIC_STORAGE_URL } from '$env/static/public';
	import { toast } from 'svelte-sonner';
	import Spinner from './ui/spinner/spinner.svelte';
	import { goto, invalidateAll } from '$app/navigation';
	import { page } from '$app/state';
	import { bookSchema } from '$lib/schemas/book';

	const {
		data
	}: {
		data: {
			publishers: Publisher[];
			genres: Genre[];
			authors: Author[];
			book?: Book;
			form: any;
		};
	} = $props();

	// svelte-ignore state_referenced_locally
	const form = superForm(data.form, {
		resetForm: false,
		onSubmit({ formData }) {
			formData.set(
				'stock',
				(stockAddPositive ? formData.get('stock') : `-${formData.get('stock')}`) as string
			);
		},
		onUpdated: async ({ form }) => {
			if (form.valid) {
				if (page.url.pathname.startsWith('/books/new')) {
					toast.success(m['messages.successful_book_save']());
					goto(`/books/${form.message?.id}/edit`);
				} else {
					toast.success(m['messages.successful_book_save']());
					$formData.stock = 0;
					await invalidateAll().then(() => {
						if (data.book?.stock === 0) stockAddPositive = true;
						$formData.stock = 0;
					});
				}
			} else {
				toast.error(form.message?.error ?? m['messages.server_error']());
			}
		}
	});
	const { form: formData, enhance, submitting } = form;
	const cover = fileProxy(form, 'cover');

	let previewUrl = $derived<string | null>(
		data.book?.img_path ? `${PUBLIC_STORAGE_URL}/${data.book?.img_path}` : null
	);

	$effect(() => {
		const file = $cover?.[0];
		if (!file) return;
		const url = URL.createObjectURL(file);
		previewUrl = url;
		return () => URL.revokeObjectURL(url);
	});

	const publishers = $derived(data.publishers.map((p) => ({ id: p.id, label: p.name })));
	const genres = $derived(
		data.genres.map((g) => ({ id: g.id, label: getLocale() === 'hu' ? g.name_hu : g.name_en }))
	);
	const authors = $derived(data.authors.map((a) => ({ id: a.id, label: a.name })));

	let stockAddPositive = $state(true);
</script>

<div class="container mx-auto px-12 pt-18 pb-32 md:px-24">
	<Button
		variant="link"
		class="mb-8 cursor-pointer hover:no-underline md:-ml-12"
		onclick={() => history.back()}
	>
		<ChevronLeft />
		{m['navigation.back']()}
	</Button>
	<form method="POST" use:enhance enctype="multipart/form-data">
		<div class="flex flex-col gap-12 md:flex-row">
			<div class="w-full shrink-0 md:w-1/4">
				{#if previewUrl}
					<img
						src={previewUrl}
						alt={m['accessibility.book_cover']()}
						class="aspect-2/3 w-full object-cover"
					/>
				{:else}
					<div
						class="flex aspect-2/3 w-full items-start justify-center bg-blue-950 font-bold text-black"
					>
						{#if $formData.title}
							<p
								class="relative top-1/4 max-w-full bg-white p-1 text-center wrap-break-word dark:bg-foreground"
							>
								{$formData.title}
							</p>
						{/if}
					</div>
				{/if}
				<Form.Field {form} name="cover" class="mt-3">
					<Form.Control>
						{#snippet children({ props })}
							<Form.Label>{m['book_lookup.cover']()}</Form.Label>
							<input
								{...props}
								type="file"
								accept="image/jpeg,image/png,image/webp"
								bind:files={$cover}
								class="grid h-9 w-full cursor-pointer content-center border border-input bg-input/30 px-2 text-sm"
							/>
						{/snippet}
					</Form.Control>
					<Form.FieldErrors />
				</Form.Field>
			</div>
			<div class="flex flex-col gap-6 md:w-1/2">
				<div>
					<Form.Field {form} name="title">
						<Form.Control>
							{#snippet children({ props })}
								<Form.Label>{m['book_lookup.title']()}</Form.Label>
								<Input {...props} bind:value={$formData.title} />
							{/snippet}
						</Form.Control>
						<Form.FieldErrors />
					</Form.Field>
				</div>
				<Form.Field {form} name="price">
					<Form.Control>
						{#snippet children({ props })}
							<Form.Label>{m['book_lookup.price']()}</Form.Label>
							<Input {...props} type="number" step="0.01" bind:value={$formData.price} />
						{/snippet}
					</Form.Control>
					<Form.FieldErrors />
				</Form.Field>
				<div class="flex flex-col gap-4 text-sm">
					<Form.Field {form} name="release_year">
						<Form.Control>
							{#snippet children({ props })}
								<Form.Label class="flex items-center gap-2">
									<Calendar size={16} />
									{m['book_lookup.release_year']()}
								</Form.Label>
								<Input {...props} type="number" bind:value={$formData.release_year} />
							{/snippet}
						</Form.Control>
						<Form.FieldErrors />
					</Form.Field>
					<Form.Field {form} name="pages">
						<Form.Control>
							{#snippet children({ props })}
								<Form.Label class="flex items-center gap-2">
									<BookOpen size={16} />
									{m['book_lookup.pages']()}
								</Form.Label>
								<Input {...props} type="number" bind:value={$formData.pages} />
							{/snippet}
						</Form.Control>
						<Form.FieldErrors />
					</Form.Field>
					<Form.Field {form} name="publisher_id">
						<Form.Control>
							{#snippet children({ props })}
								<Form.Label class="flex items-center gap-2">
									<Building size={16} />
									{m['book_lookup.publisher']()}
								</Form.Label>
								<input type="hidden" name={props.name} value={$formData.publisher_id} />
								<ComboSelect
									items={publishers}
									bind:value={$formData.publisher_id}
									placeholder={m['admin.books.select_publisher']()}
								/>
							{/snippet}
						</Form.Control>
						<Form.FieldErrors />
					</Form.Field>
					<Form.Field {form} name="genre_id">
						<Form.Control>
							{#snippet children({ props })}
								<Form.Label class="flex items-center gap-2">
									<Tag size={16} />
									{m['book_lookup.genre']()}
								</Form.Label>
								<input type="hidden" name={props.name} value={$formData.genre_id} />
								<ComboSelect
									items={genres}
									bind:value={$formData.genre_id}
									placeholder={m['admin.books.select_genre']()}
								/>
							{/snippet}
						</Form.Control>
						<Form.FieldErrors />
					</Form.Field>
					<Form.Field {form} name="author_ids">
						<Form.Control>
							{#snippet children({ props })}
								<Form.Label>{m['book_lookup.authors']()}</Form.Label>
								{#each $formData.author_ids as id}
									<input type="hidden" name={props.name} value={id} />
								{/each}
								<ComboSelect
									items={authors}
									bind:value={$formData.author_ids}
									placeholder={m['admin.books.select_authors']()}
									multiple
								/>
							{/snippet}
						</Form.Control>
						<Form.FieldErrors />
					</Form.Field>
					{#if page.url.pathname.endsWith('/edit') && data.book?.stock}
						<div class="flex w-full flex-col items-start gap-2">
							<span>{m['book_lookup.stock']()}</span>
							<div class="flex w-full flex-row items-center gap-2">
								<span class="whitespace-nowrap">{data.book?.stock}</span>
								<Button
									onclick={() => {
										stockAddPositive = !stockAddPositive;
									}}
									variant="ghost"
									class="cursor-pointer"
								>
									{stockAddPositive ? '+' : '-'}
								</Button>
								<div class="flex-1">
									<Form.Field {form} name="stock">
										<Form.Control>
											{#snippet children({ props })}
												<Input
													{...props}
													type="number"
													class="m-0 w-full"
													min={0}
													onchange={() => {
														if ($formData.stock < 0) $formData.stock = 0;
														else if (
															Number(data.book?.stock) - $formData.stock < 0 &&
															stockAddPositive == false
														)
															$formData.stock = data.book?.stock;
													}}
													bind:value={$formData.stock}
												/>
											{/snippet}
										</Form.Control>
										<Form.FieldErrors />
									</Form.Field>
								</div>
								<span class="whitespace-nowrap"
									>({stockAddPositive
										? data.book?.stock + $formData.stock
										: data.book?.stock - $formData.stock})</span
								>
							</div>
						</div>
					{:else}
						<Form.Field {form} name="stock">
							<Form.Control>
								{#snippet children({ props })}
									<Form.Label>{m['book_lookup.stock']()}</Form.Label>
									<Input {...props} type="number" bind:value={$formData.stock} />
								{/snippet}
							</Form.Control>
							<Form.FieldErrors />
						</Form.Field>
					{/if}
				</div>
				<div class="flex gap-3 pt-2">
					<Button
						type="submit"
						class="flex cursor-pointer items-center gap-2"
						disabled={$submitting}
					>
						{#if $submitting}
							<Spinner />
						{:else}
							{m['actions.save']()}
						{/if}
					</Button>
				</div>
			</div>
		</div>
	</form>
</div>
