<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiptRequest;
use App\Http\Requests\UpdateReceiptRequest;
use App\Http\Resources\ReceiptResource;
use App\Models\Book;
use App\Models\Receipt;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $user = Auth::user();
        $query = Receipt::with(['user', 'books', 'coupons', 'pickup']);
        if ($user->role === 'customer') $query->where('user_id', $user->id);
        return ReceiptResource::collection($query->paginate($request->integer('per_page', 10)));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReceiptRequest $request): JsonResource
    {
        $receipt = Receipt::create($request->validated());

        $books = collect($request->books)->mapWithKeys(fn($book) => [
            $book['id'] => [
                'quantity' => $book['quantity'],
                'price_at_purchase' => Book::find($book['id'])->price
            ]
        ]);
        $receipt->books()->attach($books);
        if ($request->has('coupons')) $receipt->coupons()->attach($request->coupons);
        $receipt->pickup()->create(['status' => 'pending']);

        return new ReceiptResource($receipt->load(['user', 'books', 'coupons', 'pickup']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Receipt $receipt): JsonResource
    {
        return new ReceiptResource($receipt->load(['user', 'books', 'coupons', 'pickup']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReceiptRequest $request, Receipt $receipt): JsonResource
    {
        $receipt->update($request->validated());
        return new ReceiptResource($receipt->load(['user', 'books', 'coupons', 'pickup']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receipt $receipt): Response
    {
        return $receipt->delete() ? response()->noContent() : abort(500);
    }
}
