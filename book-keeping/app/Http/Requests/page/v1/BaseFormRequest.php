<?php

namespace App\Http\Requests\page\v1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the parameter from the request as a string.
     *
     * Trims leading and trailing whitespace before returning. Returns an
     * empty string if the key is missing or its value is not a string.
     *
     * @param  string  $key  The request parameter key.
     * @return string The trimmed string value, or an empty string if unavailable.
     */
    protected function get_string(string $key): string
    {
        $str_value = $this->input($key);
        if (is_string($str_value)) {
            return trim($str_value);
        }

        return '';
    }
}
