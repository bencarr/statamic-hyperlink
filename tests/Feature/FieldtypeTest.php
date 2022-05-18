<?php

use Statamic\Facades\AssetContainer;
use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;
use Statamic\Facades\User;
use function Pest\Laravel\actingAs;

beforeEach(fn() => actingAs(User::query()->first()));

it('preloads default configuration', function () {
    $preload = (object) field()->fieldtype()->preload();
    expect($preload)
        ->type->toBeNull()
        ->text->toBeNull()
        ->link->toBeNull()
        ->newWindow->toBeFalse();

    expect(collect($preload->options)->pluck('value')->toArray())
        ->toEqual([null, 'entry', 'url', 'email', 'asset', 'term', 'tel']);
});

it('only adds “None” option when field is not required', function () {
    $optional = (object) field(['required' => false])->fieldtype()->preload();
    expect(collect($optional->options)->pluck('value'))->toContain(null);

    $required = (object) field(['required' => true])->fieldtype()->preload();
    expect(collect($required->options)->pluck('value'))->not()->toContain(null);
});

it('sorts type options based on config order', function () {
    $field = field(['profile' => 'custom', 'types' => ['url', 'email'], 'required' => true]);
    $preload = $field->fieldtype()->preload();

    expect(collect($preload['options'])->pluck('value')->toArray())->toEqual(['url', 'email']);
});

it('excludes “Term” option if no taxonomies are configured', function () {
    Taxonomy::all()->each(fn($taxonomy) => $taxonomy->delete());
    expect(Taxonomy::all()->isEmpty())->toBeTrue();

    $preload = field()->fieldtype()->preload();
    expect(collect($preload['options'])->pluck('value')->toArray())->not()->toContain('term');
});

it('excludes “Asset” option if no containers are configured', function () {
    AssetContainer::all()->each(fn($container) => $container->delete());
    expect(AssetContainer::all()->isEmpty())->toBeTrue();

    $preload = field()->fieldtype()->preload();
    expect(collect($preload['options'])->pluck('value')->toArray())->not()->toContain('asset');
});

it('excludes “Entry” option if no collections are configured', function () {
    Collection::all()->each(fn($collection) => $collection->delete());
    expect(Collection::all()->isEmpty())->toBeTrue();

    $preload = field()->fieldtype()->preload();
    expect(collect($preload['options'])->pluck('value')->toArray())->not()->toContain('entry');
});