<?php

namespace Tests\Support;

use BenCarr\Hyperlink\Helpers\HyperlinkData;
use Illuminate\Contracts\Support\Arrayable;

class TestLink implements Arrayable
{
    public function __construct(
        public string $type = 'url',
        public string $link = '#',
        public ?string $text = null,
        public bool $newWindow = false,
    ) {
    }

    public static function url(string $link = '#')
    {
        return (new static)->type('url')->link($link);
    }

    public static function email(string $link = 'test@example.com')
    {
        return (new static)->type('email')->link("mailto:$link");
    }

    public static function phone(string $link = '+1 212-867-5309')
    {
        return (new static)->type('tel')->link("tel:$link");
    }

    public static function entry(string $link = 'entry::AAA111B2-3333-4444-C555-D6E7FGH8I910')
    {
        return (new static)->type('entry')->link($link);
    }

    public static function asset(string $link = 'asset::assets::AAA111B2-3333-4444-C555-D6E7FGH8I910')
    {
        return (new static)->type('asset')->link($link);
    }

    public static function term(string $link = 'term::AAA111B2-3333-4444-C555-D6E7FGH8I910')
    {
        return (new static)->type('term')->link($link);
    }

    public function type(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function link(string $link)
    {
        $this->link = $link;

        return $this;
    }

    public function newWindow(bool $newWindow = true)
    {
        $this->newWindow = $newWindow;

        return $this;
    }

    public function text(?string $text)
    {
        $this->text = $text;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'link' => $this->link,
            'text' => $this->text,
            'newWindow' => $this->newWindow,
        ];
    }

    public function toData()
    {
        return new HyperlinkData(...$this->toArray());
    }
}
