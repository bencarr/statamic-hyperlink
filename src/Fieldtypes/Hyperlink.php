<?php

namespace BenCarr\Hyperlink\Fieldtypes;

use BenCarr\Hyperlink\Concerns\InteractsWithProfileData;
use BenCarr\Hyperlink\Concerns\InteractsWithVueComponents;
use BenCarr\Hyperlink\Helpers\HyperlinkData;
use BenCarr\Hyperlink\Rules\HyperlinkRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Statamic\Fields\Field;
use Statamic\Fields\Fieldtype;

/**
 * @property-read Field $field
 */
class Hyperlink extends Fieldtype
{
    use InteractsWithProfileData, InteractsWithVueComponents;

    protected $categories = ['relationship', 'special'];

    protected function configFieldItems(): array
    {
        return [
            'profile' => [
                'display' => __('hyperlink::fieldtype.config.profile.display'),
                'instructions' => __('hyperlink::fieldtype.config.profile.instructions'),
                'type' => 'select',
                'default' => 'default',
                'options' => $this->availableProfiles(),
                'width' => 33,
            ],
            'types' => [
                'display' => __('hyperlink::fieldtype.config.types.display'),
                'instructions' => __('hyperlink::fieldtype.config.types.instructions'),
                'type' => 'checkboxes',
                'inline' => true,
                'width' => 66,
                'required' => true,
                'options' => $this->labels(),
                'default' => array_keys($this->labels()),
                'if' => [
                    'profile' => 'equals custom',
                ],
            ],
            'collections' => [
                'display' => __('Collections'),
                'instructions' => __('hyperlink::fieldtype.config.collections.instructions'),
                'type' => 'collections',
                'mode' => 'select',
                'width' => 33,
                'if' => [
                    'profile' => 'equals custom',
                    'types' => 'contains entry',
                ],
            ],
            'container' => [
                'display' => __('Container'),
                'instructions' => __('hyperlink::fieldtype.config.container.instructions'),
                'type' => 'asset_container',
                'mode' => 'select',
                'max_items' => 1,
                'width' => 33,
                'if' => [
                    'profile' => 'equals custom',
                    'types' => 'contains asset',
                ],
            ],
            'taxonomies' => [
                'display' => __('Taxonomies'),
                'instructions' => __('hyperlink::fieldtype.config.taxonomies.instructions'),
                'type' => 'taxonomies',
                'mode' => 'select',
                'width' => 33,
                'if' => [
                    'profile' => 'equals custom',
                    'types' => 'contains term',
                ],
            ],
            'min_items' => [
                'display' => __('hyperlink::fieldtype.config.min_items.display'),
                'instructions' => __('hyperlink::fieldtype.config.min_items.instructions'),
                'type' => 'integer',
                'inline' => true,
                'width' => 33,
                'required' => true,
                'default' => 0,
                'min' => 0,
                'if' => [
                    'profile' => 'equals custom',
                ],
            ],
            'max_items' => [
                'display' => __('hyperlink::fieldtype.config.max_items.display'),
                'instructions' => __('hyperlink::fieldtype.config.max_items.instructions'),
                'type' => 'integer',
                'inline' => true,
                'width' => 33,
                'required' => true,
                'default' => 1,
                'min' => 1,
                'if' => [
                    'profile' => 'equals custom',
                ],
            ],
        ];
    }

    /**
     * @return HyperlinkData|Collection<HyperlinkData>|null
     */
    public function augment($value): mixed
    {
        if ( ! $value) {
            return null;
        }

        if (Arr::isAssoc($value)) {
            return new HyperlinkData(...$value);
        }

        return collect($value)->map(fn($data) => new HyperlinkData(...$data));
    }

    public function rules(): array
    {
        return [
            new HyperlinkRule($this),
        ];
    }

    public function preProcessValidatable($value)
    {
        $payload = $this->normalizeValue(parent::preProcessValidatable($value));

        foreach($payload as &$row) {
            // Pass through email address for validation
            if ($row['type'] === "email") {
                $row['email'] = str($row['link'])->after('mailto:')->toString();
            }

            // Pass through phone for validation
            if ($row['type'] === "tel") {
                $row['tel'] = str($row['link'])->after('tel:')->toString();
            }

            // Pass through absolute URLs for validation
            if ($row['type'] === "url" && ! str($row['link'])->startsWith(['/', '#', '.'])) {
                $row['url'] = $row['link'];
            }
        }

        return $payload;
    }

    public function preload(): array
    {
        $data = $this->augment($this->field->value()) ?? new HyperlinkData;
        if ($data instanceof HyperlinkData) {
            $data = collect([$data]);
        }

        return [
            'items' => $data?->map([$this, 'toPreloadArray'])->toArray(),
            'defaults' => $this->toPreloadArray(new HyperlinkData),
            'lang' => collect(trans('hyperlink::fieldtype.field'))->except('item'),
        ];
    }

    public function toPreloadArray(HyperlinkData $link) {
        return [
            'type' => $link->type ?? null,
            'link' => $link->link ?? null,
            'text' => $link->text ?? null,
            'newWindow' => $link->newWindow ?? false,
            'lang' => trans('hyperlink::fieldtype.field.item'),
            'options' => $this->enabledOptions(),
            'components' => $this->vueComponentData($link),
        ];
    }

    public function process($data)
    {
        $data = parent::process($data);
        $data = $this->normalizeValue($data);

        if (empty($data)) {
            return null;
        }

        if ($this->profile('max_items', 1) === 1) {
            $data = $data[0] ?? null;
        }

        return $data;
    }

    public function normalizeValue($data): array
    {
        if (!$data) {
            return [];
        }

        if (Arr::isAssoc($data)) {
            $data = [$data];
        }

        return collect($data)->filter()->toArray();
    }
}
