<?php

it('ignores unknown link types', function () {
    config(['statamic.hyperlink.profiles.default.types' => ['invalid', 'entry']]);

    $field = field()->fieldtype()->preload();
    expect(collect($field['options'])->pluck('value'))
        ->toContain('entry')
        ->not()->toContain('invalid');
});

it('treats null and empty config constraints to mean all possible values', function () {
    $emptyValues = [null, true, false, '', []];

    foreach ($emptyValues as $value) {
        $config = [
            'profile' => 'custom',
            'collections' => $value,
            'taxonomies' => $value,
            'containers' => $value,
        ];
        $preload = field($config)->fieldtype()->preload();
        expect($preload['defaults']['components'])
            ->toHaveKey('entry.config.collections', [])
            ->tohaveKey('term.config.taxonomies', [])
            ->tohaveKey('asset.config.containers', []);
    }
});
