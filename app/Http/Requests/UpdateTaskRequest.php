<?php

namespace App\Http\Requests;

use App\Models\TaskList;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'is_completed' => ['sometimes', 'boolean'],
            'is_important' => ['sometimes', 'boolean'],
            'due_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'task_list_id' => ['nullable', 'integer'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $listId = $this->input('task_list_id');

        return $listId === null
            || TaskList::whereKey($listId)
                ->where('user_id', $this->user()->id)
                ->exists();
    }
}
