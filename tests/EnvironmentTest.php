<?php

use Statamic\Facades\Collection;
use Statamic\Facades\User;
use Statamic\Fields\Blueprint;
use Statamic\Fields\Field;
use Tests\TestCase;

uses(TestCase::class);

it('has a super user', function () {
    $user = User::query()->first();
    expect($user)
        ->toBeInstanceOf(\Statamic\Auth\User::class)
        ->email->toEqual('admin@example.com')
        ->isSuper()->toBeTrue();
});

it('has pages collection with an optional hyperlink field', function () {
    $collection = Collection::findByHandle('pages');
    expect($collection)->toBeInstanceOf(Statamic\Entries\Collection::class);

    $blueprint = $collection->entryBlueprint();
    expect($blueprint)->toBeInstanceOf(Blueprint::class);

    $field = $blueprint->field('hyperlink');
    expect($field)
        ->toBeInstanceOf(Field::class)
        ->type()->toEqual('hyperlink')
        ->isRequired()->toBeFalse();
});