<?php

namespace BenCarr\Hyperlink\Concerns;

use BenCarr\Hyperlink\Helpers\HyperlinkData;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;
use Statamic\Fields\Field;

trait InteractsWithVueComponents
{
    protected function labels(): array
    {
        return [
            'entry' => __('Entry'),
            'url' => __('URL'),
            'email' => __('Email'),
            'asset' => __('Asset'),
            'term' => __('Term'),
            'tel' => __('Phone'),
        ];
    }

    protected function vueComponentData(HyperlinkData $data): array
    {
        return [
            'url' => ['placeholder' => 'https://statamic.com'],
            'email' => ['placeholder' => 'test@example.com'],
            'tel' => ['placeholder' => '+1 212-867-5309'],
            'entry' => $this->nestedFieldtype($data, 'entries', [
                'max_items' => 1,
                'create' => false,
                'collections' => $this->profileConstraints('collections'),
            ]),
            'asset' => $this->nestedFieldtype($data, 'assets', [
                'max_files' => 1,
                'mode' => 'list',
                'containers' => $this->profileConstraints('containers'),
            ]),
            'term' => $this->nestedFieldtype($data, 'terms', [
                'max_items' => 1,
                'create' => false,
                'taxonomies' => $this->profileConstraints('taxonomies'),
            ]),
        ];
    }

    protected function nestedFieldtype(HyperlinkData $data, string $type, array $config = []): array
    {
        $fieldtype = (new Field($type, array_merge(['type' => $type], $config)))
            ->setValue($data->getValue())
            ->fieldtype();

        return [
            'config' => rescue(fn() => $fieldtype->config(), []),
            'meta' => rescue(fn() => $fieldtype->preload(), []),
        ];
    }

    protected function supports(string $type): bool
    {
        return match ($type) {
            'entry' => Collection::all()->isNotEmpty(),
            'asset' => AssetContainer::all()->isNotEmpty(),
            'term' => Taxonomy::all()->isNotEmpty(),
            default => true,
        };
    }

    protected function enabledOptions(): array
    {
        $defaults = $this->labels();
        $allowedTypes = array_keys($defaults);
        $enabledTypes = $this->profile('types', $allowedTypes);

        return collect($enabledTypes)
            ->intersect($allowedTypes)
            ->filter(fn($type) => $this->supports($type))
            ->when(! $this->field->isRequired(), fn($keys) => $keys->prepend(null))
            ->map(fn($key) => ['label' => $defaults[$key] ?? __('None'), 'value' => $key])
            ->toArray();
    }
}
