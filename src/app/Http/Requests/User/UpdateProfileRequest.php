<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Carbon\Carbon;

class UpdateProfileRequest extends Request
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
            'first_name'         => 'string|between:2,254',
            'last_name'          => 'string|between:2,254',
            'birthday'           => 'date_format:Y-m-d|before:' . $this->getMinDate(),
            'shirtname'          => 'string|between:2,254',
            'gender'             => 'in:male,female,other',
            'about_me'           => 'string|between:0,3000',
            'current_city'       => 'string|between:2,254',
            'current_country'    => 'string|between:2,254',
            'current_country_code'    => 'string|between:2,3',
            'current_latitude'   => 'numeric|between:-90,90',
            'current_longitude'  => 'numeric|between:-180,180',
            'birth_country'      => 'string|between:2,254',
            'languages'          => 'array|entityExists:language,id',
            'brands'             => 'array|entityExists:brand,id',
            'sports'             => 'array|entityExists:sport,id',
            'nationality_id'     => 'numeric|exists:nationality,id',
            'facebook_token'     => 'string|between:0,254',
            'instagram_token'    => 'string|between:0,254',
            'twitter_token'      => 'string|between:0,254',
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
