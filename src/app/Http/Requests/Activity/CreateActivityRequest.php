<?php

namespace App\Http\Requests\Activity;

use App\Http\Requests\JsonRequest;
use App\Http\Requests\Request;

class CreateActivityRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:254',
            'sport_id' => 'required|integer|exists:sport,id',
            'start_time' => "required|date",
            'end_time' => "required|date",
            'description' => 'required|max:254',
            'recurring' => 'required|in:no,daily,weekly,monthly',
            'privacy' => 'required|in:open,closed',
            'max_participants' => 'required|integer|between:1,50',
            'invite_users' => 'array|max:'.$this->input('max_participants'),
        ];

    }
}
