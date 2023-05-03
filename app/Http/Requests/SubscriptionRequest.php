<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|bail|exists:users,id',
            'website_id' => ['required','bail','exists:websites,id',
                Rule::unique('subscriptions','website_id')->where(function ($query) {
                    $query->where('user_id', $this->request->get('user_id'));
                })
            ],
        ];
    }
}
