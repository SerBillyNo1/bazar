<?php

namespace Cone\Bazar\Http\Controllers;

use Cone\Bazar\Http\Requests\TransactionStoreRequest as StoreRequest;
use Cone\Bazar\Http\Requests\TransactionUpdateRequest as UpdateRequest;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Transaction;
use Cone\Bazar\Support\Facades\Gateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class TransactionsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = Transaction::getProxiedClass())) {
            $this->authorizeResource($class);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Cone\Bazar\Http\Requests\TransactionStoreRequest  $request
     * @param  \Cone\Bazar\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function store(StoreRequest $request, Order $order): JsonResponse
    {
        $data = $request->validated();

        try {
            $transaction = $data['type'] === Transaction::REFUND
                ? Gateway::driver($data['driver'])->refund($order, $data['amount'])
                : $order->pay($data['amount'], $data['driver'], array_merge(
                    ['completed_at' => time()],
                    Arr::except($data, ['type', 'amount', 'driver'])
                ));
        } catch (Throwable $exception) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, $exception->getMessage());
        }

        return Response::json($transaction, JsonResponse::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Cone\Bazar\Http\Requests\TransactionUpdateRequest  $request
     * @param  \Cone\Bazar\Models\Order  $order
     * @param  \Cone\Bazar\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Order $order, Transaction $transaction): JsonResponse
    {
        if ($transaction->completed()) {
            $transaction->markAsPending();
        } else {
            $transaction->markAsCompleted();
        }

        return Response::json(['updated' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Bazar\Models\Order  $order
     * @param  \Cone\Bazar\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Order $order, Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return Response::json(['deleted' => true]);
    }
}
