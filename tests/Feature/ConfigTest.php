<?php

it('ignores unknown link types', function () {
    config(['statamic.hyperlink.profiles.default.types' => ['invalid', 'entry']]);

    $field = (object) field()->fieldtype()->preload();
    expect(collect($field->options)->pluck('value'))
        ->toContain('entry')
        ->not()->toContain('invalid');
});

it('treats null and empty config constraints to mean all possible values', function () {
    $emptyValues = [null, true, false, '', []];

    foreach ($emptyValues as $value) {
        $config = collect(['collections', 'taxonomies', 'containers'])
            ->mapWithKeys(fn($key) => [$key => $value])
            ->put('profile', 'custom')
            ->toArray();
        $preload = (object) field($config)->fieldtype()->preload();
        expect($preload->components)
            ->toHaveKey('entry.config.collections', [])
            ->tohaveKey('term.config.taxonomies', [])
            ->tohaveKey('asset.config.containers', []);
    }
});