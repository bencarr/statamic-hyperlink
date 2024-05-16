<?php

namespace BenCarr\Hyperlink\Helpers;

use Facades\Statamic\Routing\ResolveRedirect;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\ComponentAttributeBag;
use Statamic\Contracts\Assets\Asset;
use Statamic\Contracts\Entries\Entry;
use Statamic\Contracts\Taxonomies\Term;

class HyperlinkData implements Arrayable, Htmlable
{
    public ?string $url = null;
    public ?string $target = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?Entry $entry = null;
    public ?Asset $asset = null;
    public ?Term $term = null;

    protected ComponentAttributeBag $attributes;

    public function __construct(
        public ?string $type = null,
        public ?string $link = null,
        public ?string $text = null,
        public bool $newWindow = false,
    ) {
        $redirect = ResolveRedirect::resolve($this->link);
        $this->url = $redirect === 404 ? '' : $redirect;
        $this->target = $this->newWindow ? '_blank' : null;
        $this->attributes = new ComponentAttributeBag();
        $this->appendLinkSpecificProperties();
    }

    public function class($classes): static
    {
        $this->attributes = $this->attributes->class($classes);

        return $this;
    }

    public function attributes(array $attributes): static
    {
        return $this->merge($attributes);
    }

    public function merge(array $attributes): static
    {
        $this->attributes = $this->attributes->merge($attributes);

        return $this;
    }

    public function getValue(): ?string
    {
        if (in_array($this->type, ['entry', 'asset', 'term'])) {
            return str($this->link)->after("{$this->type}::")->toString();
        }

        if ($this->type === 'email') {
            return str($this->link)->after('mailto:')->toString();
        }

        if ($this->type === 'tel') {
            return str($this->link)->after('tel:')->toString();
        }

        return $this->link;
    }

    public function toHtml()
    {
        return sprintf('<a %s>%s</a>', $this->attributes->merge([
            'href' => $this->url,
            'target' => $this->newWindow ? '_blank' : null,
        ]), $this->text);
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'text' => $this->text,
            'type' => $this->type,
            'target' => $this->target,
            'email' => $this->email,
            'phone' => $this->phone,
            'entry' => $this->entry,
            'asset' => $this->asset,
            'term' => $this->term,
            'newWindow' => $this->newWindow,
            'link' => $this->link,
        ];
    }

    protected function appendLinkSpecificProperties()
    {
        if ($this->type === 'url') {
            $this->text = $this->text ?? $this->link;
        }

        if ($this->type === 'email') {
            $this->email = $this->getValue();
            $this->url = $this->link;
            $this->text = $this->text ?? str($this->link)->replace('mailto:', '');
        }

        if ($this->type === 'tel') {
            $this->phone = $this->getValue();
            $this->url = $this->link;
            $this->text = $this->text ?? str($this->link)->replace('tel:', '');
        }

        if ($this->type === 'entry') {
            $this->entry = \Statamic\Facades\Entry::find($this->getValue());
            $this->url = $this->entry?->url();
            $this->text = $this->text ?? $this->entry?->title;
        }

        if ($this->type === 'term') {
            $this->term = \Statamic\Facades\Term::find($this->getValue());
            $this->url = $this->term?->url();
            $this->text = $this->text ?? $this->term?->title;
        }

        if ($this->type === 'asset') {
            $this->asset = \Statamic\Facades\Asset::findById($this->getValue());
            $this->url = $this->asset?->url();
            $this->text = $this->text ?? $this->asset?->title;
        }
    }
}
