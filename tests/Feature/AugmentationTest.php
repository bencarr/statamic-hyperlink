<?php

use BenCarr\Hyperlink\Helpers\HyperlinkData;
use Tests\Support\TestLink;

it('augments field value into data class', function () {
    $entry = pageWithLink(TestLink::url()->text('Example'));

    expect($entry->hyperlink)->toBeInstanceOf(HyperlinkData::class);
});

it('supports array accessors on nested properties', function () {
    $entry = pageWithLink(TestLink::url()->text('Example'));

    expect((array) $entry->hyperlink)
        ->toMatchArray(['type' => 'url', 'link' => '#', 'text' => 'Example', 'newWindow' => false]) // Core data
        ->toMatchArray(['url' => '#', 'target' => null]) // Computed properties
        ->toMatchArray(['email' => null, 'entry' => null, 'asset' => null, 'term' => null, 'phone' => null]); // Convenience properties
});

it('generates a fluent html interface', function () {
    $entry = pageWithLink(TestLink::url()->text('Example'));

    expect($entry->hyperlink)
        ->toHtml()->toEqual('<a href="#">Example</a>')
        ->class('test')->toHtml()->toEqual('<a href="#" class="test">Example</a>');

    $external = pageWithLink(TestLink::url()->text('Example')->newWindow());

    expect($external->hyperlink)
        ->toHtml()->toEqual('<a href="#" target="_blank">Example</a>');
});
