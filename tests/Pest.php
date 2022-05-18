<?php

use Illuminate\Validation\ValidationException;
use Statamic\Entries\Entry;
use Statamic\Fields\Field;
use Statamic\Fields\Fields;
use Tests\Support\TestLink;
use Tests\TestCase;

uses(TestCase::class)->in('Feature');

expect()->extend('toContainInstanceOf', function ($class) {
    return expect(collect($this->value)->some(fn($value) => $value instanceof $class))->toBeTrue();
});

expect()->extend('toHaveValidationErrorForField', function ($handle) {
    return $this->toBeInstanceOf(Fields::class)
        ->and(fn() => $this->value->validator()->validate())
        ->toThrow(fn(ValidationException $e) => expect($e)->errors()->toHaveKey($handle));
});

function page(array $data = []): Entry
{
    return tap(\Statamic\Facades\Entry::make()->collection('pages')->data($data))->save();
}

function pageWithLink(TestLink $config): Entry
{
    return tap(\Statamic\Facades\Entry::make()->collection('pages')
        ->data([
            'hyperlink' => (array) $config,
        ])
    )->save();
}

function pageWithLinkValue(TestLink $config): Fields
{
    return page()->blueprint()->fields()->addValues(['hyperlink' => (array) $config]);
}

function field(array $config = ['profile' => 'default']): Field
{
    return new Field('hyperlink', array_merge(['type' => 'hyperlink'], $config));
}