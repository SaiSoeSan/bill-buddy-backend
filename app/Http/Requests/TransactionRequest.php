<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
            'paid_by' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|max:50',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ];
    }
}
