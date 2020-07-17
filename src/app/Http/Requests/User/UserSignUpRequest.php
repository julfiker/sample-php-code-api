<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Carbon\Carbon;

class UserSignUpRequest extends Request
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
            'first_name' => 'required|string|between:2,254',
            'last_name'  => 'required|string|between:2,254',
            'birthday'   => 'required|date_format:Y-m-d|before:' . $this->getMinDate(),
            'email'      => 'required|email|unique:user,email',
            'password'   => 'required|between:6,24'
        ];
    }

    private function getMinDate()
    {
        return Carbon::now()->subYear(13)->startOfDay()->toDateString();
    }

    public function messages()
    {
        return [
            'birthday.before' => 'Sorry. Legally you need to be at least 13 years old to use Spoly.',
        ];
    }

}
