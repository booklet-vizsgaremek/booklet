<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceiptRequest;
use App\Http\Requests\UpdateReceiptRequest;
use App\Http\Resources\ReceiptResource;
use App\Models\Receipt;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $receipts = Receipt::with(['user', 'books', 'coupons', 'pickup'])->get();
        return ReceiptResource::collection($receipts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReceiptRequest $request): JsonResource
    {
        $receipt = Receipt::create($request->validated())->load(['user', 'books', 'coupons', 'pickup']);
        return new ReceiptResource($receipt);
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
