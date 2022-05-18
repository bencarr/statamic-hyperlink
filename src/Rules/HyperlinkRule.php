<?php

namespace BenCarr\Hyperlink\Rules;

use BenCarr\Hyperlink\Fieldtypes\Hyperlink;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class HyperlinkRule implements Rule
{
    public ?Validator $validator;

    public function __construct(public Hyperlink $fieldtype)
    {
        if ($value = $this->value()) {
            $this->validator = ValidatorFacade::make($value, [
                'link' => 'required',
                'text' => 'required',
                'email' => ['sometimes', 'email'],
                'url' => ['sometimes', 'url'],
                'tel' => ['sometimes', 'regex:/[\d,.+\-()]/']
            ], $this->messages());
        }
    }

    public function passes($attribute, $value): bool
    {
        if ($value === null || ! $this->validator) {
            return true;
        }

        return ! $this->validator->fails();
    }

    public function message(): string
    {
        return $this->validator->getMessageBag()->first();
    }

    protected function messages(): array
    {
        return [
            'link.required' => __("hyperlink::validation.link.required.{$this->value('type')}"),
            'text.required' => __('hyperlink::validation.text.required'),
            'email.email' => __('hyperlink::validation.email.email'),
            'url.url' => __('hyperlink::validation.url.url'),
            'tel.regex' => __('hyperlink::validation.tel.regex'),
        ];
    }

    protected function value(?string $key = null, $default = null)
    {
        $value = $this->fieldtype->field()->value();
        if ( ! $key) {
            return $value;
        }

        return data_get($value, $key, $default);
    }
}
