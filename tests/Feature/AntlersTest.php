<?php

use Statamic\Facades\Antlers;
use Tests\Support\TestLink;

it('outputs HTML using Antlers', function () {
    $entry = pageWithLink(TestLink::url()->text('Example'));

    $template = '{{ entry.hyperlink }}';
    expect((string) Antlers::parse($template, ['entry' => $entry]))
        ->toEqual('<a href="#">Example</a>');
});

it('supports custom markup using Antlers', function () {
    $entry = pageWithLink(TestLink::url()->text('Example'));

    $template = '<a href="{{ entry.hyperlink.url }}" target="{{ entry.hyperlink.target }}">{{ entry.hyperlink.text }}</a>';
    expect((string) Antlers::parse($template, ['entry' => $entry]))
        ->toEqual('<a href="#" target="">Example</a>');
});
