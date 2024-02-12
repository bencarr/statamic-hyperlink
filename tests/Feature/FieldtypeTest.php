<?php

use Statamic\Facades\AssetContainer;
use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;
use Statamic\Facades\User;
use function Pest\Laravel\actingAs;

beforeEach(fn() => actingAs(User::query()->first()));

it('preloads default configuration', function () {
    $preload = field()->fieldtype()->preload();
    $default_link = ['type' => null, 'text' => null, 'link' => null, 'newWindow' => false];

    // Empty items array
    expect($preload['items'])->toHaveCount(0);

    // Default options set
    $options = collect($preload['options'])->pluck('value')->toArray();
    expect($options)->toEqual([null, 'entry', 'url', 'email', 'asset', 'term', 'tel']);

    // Defaults array for new items
    expect($preload['defaults'])->toMatchArray($default_link);
});

it('only adds “None” option when field is not required', function () {
    $optional = field(['required' => false])->fieldtype()->preload();
    $optional_options = collect($optional['options'])->pluck('value')->toArray();
    expect($optional_options)->toContain(null);

    $required = field(['required' => true])->fieldtype()->preload();
    $required_options = collect($required['options'])->pluck('value')->toArray();
    expect($required_options)->not()->toContain(null);
});

it('sorts type options based on config order', function () {
    $field = field(['profile' => 'custom', 'types' => ['url', 'email'], 'required' => true]);
    $preload = $field->fieldtype()->preload();
    $options = collect($preload['options'])->pluck('value')->toArray();

    expect($options)->toEqual(['url', 'email']);
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
