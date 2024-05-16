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
        $value = $this->value();

        if (empty($value)) {
            $value = null;
        }

        $this->validator = ValidatorFacade::make(['value' => $value], [
            'value' => ['nullable', 'array', "min:$min", "max:$max"],
            'value.*.link' => 'required',
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
        $append_link_number = $this->fieldtype->profile('max_items', 1) > 1;
        $base_messages = [
            'value.*.text.required' => __('hyperlink::validation.text.required'),
            'value.*.email.email' => __('hyperlink::validation.email.email'),
            'value.*.url.url' => __('hyperlink::validation.url.url'),
            'value.*.tel.regex' => __('hyperlink::validation.tel.regex'),
        ];

        $messages = [];

        foreach($this->value() as $i => $link) {
            $link_number_prefix = __('hyperlink::validation.link_number_prefix', ['n' => $i + 1]);
            $type = $link['type'] ?? 'none';
            $messages["value.$i.link.required"] = __("hyperlink::validation.link.required.{$type}");
            if ($append_link_number) {
                $messages["value.$i.link.required"] = $link_number_prefix.$messages["value.$i.link.required"];
                foreach($base_messages as $key => $text) {
                    $indexed_key = str_replace('*', $i, $key);
                    $messages[$indexed_key] = $link_number_prefix . $text;
                }
            }
        }

        return $messages;
    }

    protected function value(): array
    {
        return $this->fieldtype->normalizeValue($this->fieldtype->field()->value());
    }
}
