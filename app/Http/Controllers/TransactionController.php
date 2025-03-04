<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionUser;
use App\Models\Group;
use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Create a new transaction
    public function store(TransactionRequest $request)
    {
        $group = Group::find($request->group_id);
        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        $transaction = Transaction::create($request->validated());

        if ($request->has('users')) {
            foreach ($request->users as $userId) {
                if (!$group->users()->where('user_id', $userId)->exists()) {
                    return response()->json(['error' => 'User does not belong to the group'], 400);
                }
                $transaction->users()->attach($userId);
            }
        }

        return response()->json($transaction, 201);
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
    public function update(TransactionRequest $request, $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
        $transaction->update($request->all());
        return response()->json($transaction, 200);
    }

    // Delete a transaction by ID
    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Detach related many-to-many records
        $transaction->users()->detach();

        $transaction->delete();
        return response()->json(['message' => 'Transaction and related records deleted successfully'], 200);
    }
}
