<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeletePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $post = $this->route('post');

        return $user->tokenCan('moderate-posts') || ($user->tokenCan('delete-posts') && $post->user_id == $user->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
