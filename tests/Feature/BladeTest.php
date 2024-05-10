<?php

use Illuminate\Support\Facades\Blade;
use Tests\Support\TestLink;

it('outputs HTML using Blade', function () {
    $entry = pageWithLink(TestLink::url()->text('Example'));

    $template = '{{ $entry->hyperlink }}';
    expect(Blade::render($template, ['entry' => $entry]))
        ->toEqual('<a href="#">Example</a>');
});

it('chains attributes using Blade', function () {
    $entry = pageWithLink(TestLink::url()->text('Example'));

    $template = '{{ $entry->hyperlink->class("test")->attributes(["id" => "example", "aria-label" => "Test"]) }}';
    expect(Blade::render($template, ['entry' => $entry]))
        ->toEqual('<a href="#" class="test" id="example" aria-label="Test">Example</a>');
});

it('allows custom markup using Blade', function () {
    $entry = pageWithLink(TestLink::url()->text('Example'));

    $template = '<a href="{{ $entry->hyperlink->url }}" target="{{ $entry->hyperlink->target }}">{{ $entry->hyperlink->text }}</a>';
    expect(Blade::render($template, ['entry' => $entry]))
        ->toEqual('<a href="#" target="">Example</a>');
});
