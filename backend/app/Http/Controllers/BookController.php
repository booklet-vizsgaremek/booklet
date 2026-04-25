<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $query = Book::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where('title', 'like', "%{$search}%");
            })
            ->when(
                $request->filled('genre'),
                fn($q) => $q->where('genre_id', $request->string('genre'))
            )
            ->when(
                $request->filled('publisher'),
                fn($q) => $q->where('publisher_id', $request->string('publisher'))
            )
            ->when(
                $request->filled('author'),
                fn($q) => $q->whereHas(
                    'authors',
                    fn($q) => $q->where('authors.id', $request->string('author'))
                )
            )
            ->when(
                $request->filled('min_year'),
                fn($q) => $q->where('release_year', '>=', $request->integer('min_year'))
            )
            ->when(
                $request->filled('max_year'),
                fn($q) => $q->where('release_year', '<=', $request->integer('max_year'))
            )
            ->when(
                $request->filled('min_price'),
                fn($q) => $q->where('price', '>=', $request->integer('min_price'))
            )
            ->when(
                $request->filled('max_price'),
                fn($q) => $q->where('price', '<=', $request->integer('max_price'))
            )
            ->when(
                $request->filled('min_pages'),
                fn($q) => $q->where('pages', '>=', $request->integer('min_pages'))
            )
            ->when(
                $request->filled('max_pages'),
                fn($q) => $q->where('pages', '<=', $request->integer('max_pages'))
            );

        $orderColumns = [
            'order_title' => 'title',
            'order_year'  => 'release_year',
            'order_page'  => 'pages',
            'order_price' => 'price',
        ];

        $hasOrder = false;
        foreach ($orderColumns as $param => $column) {
            $direction = $request->query($param);
            if (in_array($direction, ['asc', 'desc'])) {
                $query->orderBy($column, $direction);
                $hasOrder = true;
            }
        }

        if (!$hasOrder) {
            $query->orderBy('title', 'asc')
                ->orderBy('release_year', 'desc')
                ->orderBy('pages', 'desc')
                ->orderBy('price', 'asc');
        }

        return BookResource::collection(
            $query->with(['authors', 'publisher', 'genre'])
                ->paginate($request->integer('per_page', 10))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): JsonResource
    {
        $this->authorize('manager');
        $validated = $request->validated();
        $book = Book::create(collect($validated)->except('author_ids')->toArray());

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('books', 'public');
            $book->img_path = $path;
        }

        $book->authors()->sync($validated['author_ids']);
        return new BookResource($book->load(['authors', 'publisher', 'genre']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): JsonResource
    {
        return new BookResource($book->load(['authors', 'publisher', 'genre']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book): JsonResource
    {
        $this->authorize('manager');
        $validated = $request->validated();
        $book->update(collect($validated)->except('author_ids')->toArray());
        if (isset($validated['author_ids'])) $book->authors()->sync($validated['author_ids']);

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('books', 'public');
            $book->img_path = $path;
        }

        $book->update($validated);
        return new BookResource($book->load(['authors', 'publisher', 'genre']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): Response
    {
        $this->authorize('manager');
        return $book->delete() ? response()->noContent() : abort(500);
    }

    public function topPurchased(): JsonResource
    {
        $books = Book::with(['authors', 'publisher', 'genre'])
            ->withCount(['receipts as total_purchased' => function ($query) {
                $query->select(DB::raw('sum(books_receipts.quantity)'));
            }])
            ->orderByDesc('total_purchased')
            ->limit(10)
            ->get();

        return BookResource::collection($books);
    }

    public function randomCategory(): JsonResource
    {
        $genre = Genre::inRandomOrder()->first();
        $books = Book::with(['authors', 'publisher', 'genre'])
            ->where('genre_id', $genre->id)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return BookResource::collection($books);
    }

    public function discounted(): JsonResource
    {
        $userId = auth('sanctum')->id();
        $books = Book::with(['authors', 'publisher', 'genre'])
            ->where(function ($query) use ($userId) {
                $query->whereHas('coupons', function ($q) use ($userId) {
                    $q->whereNull('code')
                        ->where(function ($q) use ($userId) {
                            $q->whereNull('user_id');
                            if ($userId) {
                                $q->orWhere('user_id', $userId);
                            }
                        })
                        ->where('starts_at', '<=', now())
                        ->where('ends_at', '>=', now());
                })->orWhereHas('genre.coupons', function ($q) use ($userId) {
                    $q->whereNull('code')
                        ->where(function ($q) use ($userId) {
                            $q->whereNull('user_id');
                            if ($userId) {
                                $q->orWhere('user_id', $userId);
                            }
                        })
                        ->where('starts_at', '<=', now())
                        ->where('ends_at', '>=', now());
                });
            })
            ->limit(10)
            ->get();

        return BookResource::collection($books);
    }
}