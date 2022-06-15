<?php

namespace App\Http\Requests;

use App\Traits\ResponseHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiBaseRequest extends FormRequest
{
    use ResponseHelpers;

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->messages();

        if (isset($this->parser) && !is_null($this->parser)) {
            $parser = (new $this->parser);
            $mirroredErrors = [];
            foreach ($errors as $key => $error) {
                $params = explode('.', $key);
                if (count($params) > 1) {
                    $mirroredErrors = $this->translateMirrorErrors($error, $params, $parser);
                } else {
                    $mirror = $parser->getMirror();
                    $mirroredKey = $mirror[$key];
                    $mirroredErrors[$mirroredKey] = $error;
                }
            }

            $errors = $mirroredErrors;
        }

        $response = $this->sendError('Invalid data', compact('errors'), 422)
            ->header('Status-Reason', 'Invalid Data');

        throw new HttpResponseException($response);
    }

    private function translateMirrorErrors($error, $params, $parser)
    {
        $attribute = array_shift($params);

        if (is_numeric($attribute)) {
            return [$attribute => $this->translateMirrorErrors($error, $params, $parser)];
        }

        $mirror = $parser->getMirror();
        $mirroredKey = $mirror[$attribute];

        $includeAttributes = $parser->getIncludeAttributes();
        if (count($includeAttributes) && array_key_exists($attribute, $includeAttributes)) {
            $newParser = (new $includeAttributes[$attribute]);
            return [$mirroredKey => $this->translateMirrorErrors($error, $params, $newParser)];
        }

        return [$mirroredKey => $error];
    }
}
