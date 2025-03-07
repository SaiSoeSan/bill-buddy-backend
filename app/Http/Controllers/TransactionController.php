<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\TransactionRequest;

class TransactionController extends Controller
{
    // Create a new transaction
    public function store(TransactionRequest $request)
    {
        $group = Group::find($request->group_id);
        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        DB::beginTransaction();
        try {
            $transactionData = $request->validated();
            $transaction = Transaction::create($transactionData);

            if ($request->has('users')) {
                $userCount = count($request->users);
                $amountPerUser = $transaction->amount / $userCount;

                foreach ($request->users as $userId) {
                    if (!$group->users()->where('user_id', $userId)->exists()) {
                        DB::rollBack();
                        return response()->json(['error' => 'User does not belong to the group'], 400);
                    }
                    $transaction->transactionUsers()->attach($userId, [
                        'amount_owned' => $amountPerUser,
                        'status' => 'pending'
                    ]);
                }
            }

            DB::commit();
            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return response()->json(['error' => 'Transaction creation failed'], 500);
        }
    }

    // Get all transactions
    public function index()
    {
        $transactions = Transaction::all();
        return response()->json($transactions, 200);
    }

    // Get a single transaction by ID
    public function show($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
        return response()->json($transaction, 200);
    }

    // Update a transaction by ID
    public function update(TransactionRequest $request, $id) {}

    // Delete a transaction by ID
    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Detach related many-to-many records
        $transaction->transactionUsers()->detach();

        $transaction->delete();
        return response()->json(['message' => 'Transaction and related records deleted successfully'], 200);
    }
}
