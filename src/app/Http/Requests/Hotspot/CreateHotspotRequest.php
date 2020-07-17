<?php

namespace App\Http\Requests\Hotspot;

use App\Http\Requests\Request;

class CreateHotspotRequest extends Request
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
            'name' => 'required|string',
            'category_id' => 'required|numeric|exists:hotspot_category,id',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'city' => 'required|string',
            'country' => 'required|string',
        ];
    }
}
