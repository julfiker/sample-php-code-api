<?php

namespace App\Services\Validation;

use Illuminate\Validation\Validator as IlluminateValidator;

class ValidatorExtension extends IlluminateValidator
{

    public function __construct($translator, $data, $rules, $messages = [], $customAttributes = [])
    {
        parent::__construct($translator, $data, $rules, $messages, $customAttributes);
    }

    public function validateAlphaSpaceDash($attribute, $value)
    {
        return (bool) preg_match("/^[a-zA-z\040-]+$/", $value);
    }

    /**
     * Checks if entities exists in database.
     *
     * It is different than validateExists because validateExists expects array of ids, not objects.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateEntityExists($attribute, $value, $parameters)
    {
        $ids = [];
        foreach($value as $entity) {
            $ids[] = $entity['id'];
        }

        return $this->validateExists($attribute, $ids, $parameters);
    }

}