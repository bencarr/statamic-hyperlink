<?php

use BenCarr\Hyperlink\Rules\HyperlinkRule;
use Tests\Support\TestLink;

it('adds custom validation rule to fieldtype', function () {
    $rules = collect(field()->rules());
    expect($rules->get('hyperlink'))->toContainInstanceOf(HyperlinkRule::class);
});

it('requires a value for the link fields', function () {
    $blank = pageWithLinkValue(TestLink::url(''));
    expect($blank)->toHaveValidationErrorForField('hyperlink');

    $emptyLink = pageWithLinkValue(TestLink::url()->link(''));
    expect($emptyLink)->toHaveValidationErrorForField('hyperlink');

    $valid = pageWithLinkValue(TestLink::url());
    expect($valid)->not()->toHaveValidationErrorForField('hyperlink');
});

it('validates emails for email links', function () {
    $invalid = pageWithLinkValue(TestLink::email('invalid'));
    expect($invalid)->toHaveValidationErrorForField('hyperlink');

    $valid = pageWithLinkValue(TestLink::email('valid@example.com'));
    expect($valid)->not()->toHaveValidationErrorForField('hyperlink');
});

it('validates phone numbers for phone links', function () {
    $invalid = ['invalid', '@twitter', '#anchor', '/path'];
    expect(collect($invalid))
        ->map(fn($value) => pageWithLinkValue(TestLink::phone($value)))
        ->each->toHaveValidationErrorForField('hyperlink');

    // Valid Formats
    $valid = ['1112223333', '(111) 222-3333', '+1 222 333 4444', '+44.1234567890', '+44 123-456-7890', '1112223333,44'];
    expect(collect($valid))
        ->map(fn($value) => pageWithLinkValue(TestLink::phone($value)))
        ->each->not->toHaveValidationErrorForField('hyperlink');
});

it('validates URLs for URL links', function () {
    $invalid = pageWithLinkValue(TestLink::url('invalid'));
    expect($invalid)->toHaveValidationErrorForField('hyperlink');

    $invalid = pageWithLinkValue(TestLink::url('https://example.com'));
    expect($invalid)->not()->toHaveValidationErrorForField('hyperlink');
});

it('allows anchor URLs and relative paths', function () {
    $anchor = pageWithLinkValue(TestLink::url('#anchor'));
    expect($anchor)->not()->toHaveValidationErrorForField('hyperlink');

    $anchor = pageWithLinkValue(TestLink::url('/some/path'));
    expect($anchor)->not()->toHaveValidationErrorForField('hyperlink');

    $anchor = pageWithLinkValue(TestLink::url('./some/path'));
    expect($anchor)->not()->toHaveValidationErrorForField('hyperlink');

    $anchor = pageWithLinkValue(TestLink::url('../some/path'));
    expect($anchor)->not()->toHaveValidationErrorForField('hyperlink');
});

it('uses conditional validation messages', function () {
    $anchor = pageWithLinkValue(TestLink::url('#anchor'));
    expect($anchor)->not()->toHaveValidationErrorForField('hyperlink');

    $anchor = pageWithLinkValue(TestLink::url('/some/path'));
    expect($anchor)->not()->toHaveValidationErrorForField('hyperlink');

    $anchor = pageWithLinkValue(TestLink::url('./some/path'));
    expect($anchor)->not()->toHaveValidationErrorForField('hyperlink');

    $anchor = pageWithLinkValue(TestLink::url('../some/path'));
    expect($anchor)->not()->toHaveValidationErrorForField('hyperlink');
});
