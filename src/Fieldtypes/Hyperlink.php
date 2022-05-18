<?php

namespace BenCarr\Hyperlink\Fieldtypes;

use BenCarr\Hyperlink\Concerns\InteractsWithProfileData;
use BenCarr\Hyperlink\Concerns\InteractsWithVueComponents;
use BenCarr\Hyperlink\Helpers\HyperlinkData;
use BenCarr\Hyperlink\Rules\HyperlinkRule;
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
        ];
    }

    public function augment($value): ?HyperlinkData
    {
        if ( ! $value) {
            return null;
        }

        return new HyperlinkData(...$value);
    }

    public function rules(): array
    {
        return [
            new HyperlinkRule($this),
        ];
    }

    public function preProcessValidatable($value)
    {
        $payload = parent::preProcessValidatable($value);

        if (!$payload) {
            return null;
        }

        // Pass through email address for validation
        if ($payload['type'] === "email") {
            $payload['email'] = str($payload['link'])->after('mailto:')->toString();
        }

        // Pass through phone for validation
        if ($payload['type'] === "tel") {
            $payload['tel'] = str($payload['link'])->after('tel:')->toString();
        }

        // Pass through absolute URLs for validation
        if ($payload['type'] === "url" && ! str($payload['link'])->startsWith(['/', '#', '.'])) {
            $payload['url'] = $payload['link'];
        }

        return $payload;
    }

    public function preload(): array
    {
        $data = $this->augment($this->field->value());

        return [
            'type' => $data->type ?? null,
            'link' => $data->link ?? null,
            'text' => $data->text ?? null,
            'newWindow' => $data->newWindow ?? false,
            'lang' => trans('hyperlink::fieldtype.field.labels'),
            'options' => $this->enabledOptions(),
            'components' => $this->vueComponentData(),
        ];
    }
}
