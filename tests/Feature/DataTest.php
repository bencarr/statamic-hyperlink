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

it('resolves email from value', function () {
    expect(TestLink::email('email@example.com')->toData())
        ->toHaveProperty('email', 'email@example.com');
});

it('resolves phone from value', function () {
    expect(TestLink::phone('1234567890')->toData())
        ->toHaveProperty('phone', '1234567890');
});

it('resolves linked entry relationships', function () {
    $entry = EntryFacade::query()->where('collection', 'pages')->first();
    expect($entry)
        ->toBeInstanceOf(Entry::class)
        ->and(TestLink::entry("entry::{$entry->id}")->toData())
        ->toHaveProperty('entry', $entry);
});

it('resolves linked asset relationships', function () {
    $container = AssetContainerFacade::findByHandle('assets');
    expect($container)->toBeInstanceOf(AssetContainer::class);

    $asset = AssetFacade::query()->where('container', $container->handle())->first();
    expect($asset)
        ->toBeInstanceOf(Asset::class)
        ->and(TestLink::asset("asset::{$asset->id}")->toData())
        ->toHaveProperty('asset', $asset);
});

it('resolves linked term relationships', function () {
    $term = TermFacade::query()->where('taxonomy', 'categories')->first();
    expect($term)
        ->toBeInstanceOf(LocalizedTerm::class)
        ->and(TestLink::term("term::{$term->id}")->toData())
        ->toHaveProperty('term', $term);
});