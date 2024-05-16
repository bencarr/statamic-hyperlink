<?php

use Statamic\Assets\Asset;
use Statamic\Assets\AssetContainer;
use Statamic\Entries\Entry;
use Statamic\Facades\Asset as AssetFacade;
use Statamic\Facades\AssetContainer as AssetContainerFacade;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Term as TermFacade;
use Statamic\Taxonomies\LocalizedTerm;
use Tests\Support\TestLink;

it('resolves URL from value', function ($url) {
    expect(TestLink::url($url)->toData())
        ->toHaveProperty('url', $url);
})->with([
    'path' => '/some/path',
    'url' => 'https://statamic.com',
]);

it('resolves email from value', function () {
    expect(TestLink::email('email@example.com')->toData())
        ->toHaveProperty('email', 'email@example.com')
        ->url->toEqual('mailto:email@example.com');
});

it('resolves phone from value', function () {
    expect(TestLink::phone('1234567890')->toData())
        ->toHaveProperty('phone', '1234567890')
        ->url->toEqual('tel:1234567890');
});

it('resolves linked entry relationships', function () {
    $entry = EntryFacade::query()->where('collection', 'pages')->first();
    expect($entry)
        ->toBeInstanceOf(Entry::class)
        ->and(TestLink::entry("entry::{$entry->id}")->toData())
        ->toHaveProperty('entry', $entry)
        ->url->not()->toBeEmpty()
        ->url->not()->toStartWith('entry::');
});

it('resolves linked asset relationships', function () {
    $container = AssetContainerFacade::findByHandle('assets');
    expect($container)->toBeInstanceOf(AssetContainer::class);

    $asset = AssetFacade::query()->where('container', $container->handle())->first();
    expect($asset)->toBeInstanceOf(Asset::class);
    expect(TestLink::asset("asset::{$asset->id}")->toData())
        ->asset->toBeInstanceOf(Asset::class)
        ->asset->id->toEqual($asset->id)
        ->url->not()->toBeEmpty()
        ->url->not()->toStartWith('asset::');
});

it('resolves linked term relationships', function () {
    $term = TermFacade::query()->where('taxonomy', 'categories')->first();
    expect($term)
        ->toBeInstanceOf(LocalizedTerm::class)
        ->and(TestLink::term("term::{$term->id}")->toData())
        ->toHaveProperty('term', $term)
        ->url->not()->toBeEmpty()
        ->url->not()->toStartWith('term::');
});

it('fails gracefully on broken linked relationships', function ($link, $property) {
    expect($link->toData())
        ->toHaveProperty($property, null)
        ->url->toBeNull();
})->with([
    'entry' => [TestLink::entry('entry::BROKEN'), 'entry'],
    'asset' => [TestLink::entry('asset::BROKEN'), 'asset'],
    'term' => [TestLink::entry('term::BROKEN'), 'term'],
]);

it('uses hostname as default link text for URL links', function ($value, $text) {
    expect(TestLink::url($value)->text(null)->toData())->toHaveProperty('text', $text);
})->with([
    'path' => ['https://statamic.com/addons/bencarr/hyperlink', 'statamic.com'],
    'www' => ['https://www.flatcamp.com', 'flatcamp.com'],
    'mixed-case' => ['https://FlatCamp.com', 'FlatCamp.com'],
    'subdomain' => ['https://v2.statamic.com', 'v2.statamic.com'],
]);

it('uses email as default link text for email links', function ($value) {
    expect(TestLink::email($value)->text(null)->toData())->toHaveProperty('text', $value);
})->with([
    'hello@statamic.com',
    'hello+hyperlink@statamic.com',
]);

it('uses phone as default link text for phone links', function ($value) {
    expect(TestLink::phone($value)->text(null)->toData())->toHaveProperty('text', $value);
})->with([
    '(555) 555-5555',
    '555.555.5555',
]);

it('uses entry title as default link text for email links', function () {
    $entry = EntryFacade::query()->where('collection', 'pages')->first();
    expect(TestLink::entry("entry::{$entry->id}")->text(null)->toData())->toHaveProperty('text', $entry->title);
});

it('uses asset basename as default link text for asset links', function () {
    $container = AssetContainerFacade::findByHandle('assets');
    $asset = AssetFacade::query()->where('container', $container->handle())->first();
    expect(TestLink::asset("asset::{$asset->id}")->text(null)->toData())->toHaveProperty('text', $asset->basename());
});

it('uses term title as default link text for term links', function () {
    $term = TermFacade::query()->where('taxonomy', 'categories')->first();
    expect(TestLink::term("term::{$term->id}")->text(null)->toData())->toHaveProperty('text', $term->title());
});
