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
        $min = $this->fieldtype->profile('min_items', 0);
        $max = $this->fieldtype->profile('max_items', 1);
        $value = $this->fieldtype->normalizeValue($this->value());

        if (empty($value)) {
            $value = null;
        }

        $this->validator = ValidatorFacade::make(['value' => $value], [
            'value' => ['nullable', 'array', "min:$min", "max:$max"],
            'value.*.link' => 'required',
            'value.*.text' => 'required',
            'value.*.email' => ['sometimes', 'email'],
            'value.*.url' => ['sometimes', 'url'],
            'value.*.tel' => ['sometimes', 'regex:/[\d,.+\-()]/']
        ], $this->messages());
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
            'value.*.link.required' => __("hyperlink::validation.link.required.{$this->value('type')}"),
            'value.*.text.required' => __('hyperlink::validation.text.required'),
            'value.*.email.email' => __('hyperlink::validation.email.email'),
            'value.*.url.url' => __('hyperlink::validation.url.url'),
            'value.*.tel.regex' => __('hyperlink::validation.tel.regex'),
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
