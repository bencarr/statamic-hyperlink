# Hyperlink

> Hyperlink is a Statamic addon that expands the default Link fieldtype and allows you to store link text and target options alongside the link destination.

Hyperlink is great for CTAs, callouts, and hero buttons, and has everything you need for a smooth author and developer experience:

- **Multiple links in a single field** to you simplify your blueprints and avoid needing a replicator or separate field to store more than one link
- **Link to everything** including entries, URLs, email addresses, assets, taxonomy terms, and phone numbers
- **Multi-site support** for localizing links
- **Flexible templating options** for both [Antlers](#antlers) and [Blade](#blade)
- **Reusable field configurations** across blueprints using [Profiles](#profiles) in your config

## Get Started
- [Installation](#installation)
- [Configuration](#configuration)
    - [Profiles](#profiles)
    - [Profile options](#profile-options)
    - [Field-specific settings](#field-specific-settings)
- [Templating](#templating)
    - [Antlers](#antlers)
    - [Blade](#blade)
    - [Available data](#available-data)
- [Validation](#validation)
- [Schema](#schema)
- [Licensing](#licensing)

## Installation

You can find and install Hyperlink from the Statamic control panel under _Tools → Addons_, or install from your project root using [Composer](https://getcomposer.org).

``` bash
composer require bencarr/statamic-hyperlink
```

## Configuration

Add a Hyperlink field to your blueprint by selecting it from the “Relationship” or “Special” groups.

### Profiles

Profiles let you re-use Hyperlink configurations across multiple blueprints. You can set up profiles in a config file, then select which profile each field should use. Future changes can be made in the profile configuration without needing to update each field’s settings.

#### Customizing the default profile

Start customizing profiles by publishing the `hyperlink-config` tag.

```bash
php artisan vendor:publish --tag=hyperlink-config
```

You can find the published configuration in `config/statamic/hyperlink.php`.

#### Adding additional profiles

Make additional profiles available by adding new keys to the `profiles` array.

```php
<?php
return [
    'profiles' => [
        'default' => [
            // ...
        ],
        'hero' => [
            // ...        
        ],
    ],
];
```

### Profile options

Each profile can be customized using the following options.

| Key           | Type     | Description                                                                                    |
|---------------|----------|------------------------------------------------------------------------------------------------|
| `types`       | `array`  | The enabled link types<br>**Default:** `['entry', 'url', 'email', 'asset', 'term', 'tel']`     |
| `collections` | `?array` | Available collections for “Entry” links. Leave blank for all collections.<br>**Default:** `[]` |
| `containers`  | `?array` | Available containers for “Asset” links for all containers.<br>**Default:** `[]`                |
| `taxonomies`  | `?array` | Available taxonomies for “Term” links. Leave blank for all taxonomies.<br>**Default:** `[]`    |
| `min_items`   | `int`    | Minimum number of links authors must provide<br>**Default:** `0`                               |
| `max_items`   | `int`    | Max number of links the field can accept<br>**Default:** `1`                                   |

> **Pro Tip** — You can also re-order the link type dropdown by adjusting the order of the `types` array in your profile. For example, to make the “Asset” option appear first, set the `types` property to `['asset', 'entry', 'url', 'email', 'term', 'tel']`.

### Field-specific settings

To bypass profiles and use a one-off configuration for a particular field, select the “Custom” profile in the field settings pane, which surfaces the same [profile configuration options](#profile-options) in the UI and saves the settings to the blueprint itself.

> **FYI** — When using the “Custom” profile, enabled link types cannot be reordered.

## Templating

Hyperlink works great with both Antlers and Blade, and has built-in conveniences to give you full control over your template markup.

### Antlers

In your Antlers templates, reference the field value like any other field to return a plain hyperlink with all the necessary info. For example, if your field handle was `cta`:

```handlebars
{{ cta }}

<!-- Output -->
<a href="https://statamic.com">Rad Button Text</a>
```

> **Note** — The `target` attribute will be added automatically when the “New window?” toggle is checked.

For maximum flexibility, you can access any of the [available data](#available-data) from the Hyperlink field:

```handlebars
<a href="{{ cta.url }}" target="{{ cta.target }}" class="button">
    {{ svg src="{cta.type}" }}
    {{ cta.text }}
</a>
```

If your field configuration supports multiple links, treat the field value like a collection. For example, if your field handle was `buttons`:

```handlebars
{{ buttons }}
    <a href="{{ url }}" target="{{ target }}">{{ text }}</a>
{{ /buttons }}
```


### Blade

In your Blade templates, reference the field value like any other field to return a plain hyperlink with all the necessary info. For example, if your field handle was `cta`:

```blade
{{ $page->cta }}

<!-- Output -->
<a href="https://statamic.com">Rad Button Text</a>
```

> **Note** — The `target` attribute will be added automatically when the “New window?” toggle is checked.

For simple markup modifications like adding a class or an attribute, you can chain `class` and `attributes`
methods similar to [Blade component attributes](https://laravel.com/docs/9.x/blade#component-attributes):

```blade
{{ $page->cta->class('button')->attributes(['x-on:click' => 'doSomething' ]) }}

<!-- Output -->
<a href="https://statamic.com" class="button" x-on:click="doSomething">Rad Button Text</a>
```

For maximum flexibility, you can access any of the [available data](#available-data) from the Hyperlink field:

```blade
<a href="{{ $page->cta->url }}" target="{{ $page->cta->target }}" class="button">
    @svg($page->cta-type)
    {{ $page->cta->text }}
</a>
```

If your field configuration supports multiple links, treat the field value like a collection. For example, if your field handle was `buttons`:

```blade
@foreach($page->buttons as $button)
    <a href="{{ $button->url }}" target="{{ $button->target }}">{{ $button->text }}</a>
@endforeach
```


### Available data

The field value of a Hyperlink field is a `HyperlinkData` object, which works just like an array in your templates. Inside you’ve got access to everything you’ll need to completely customize your presentation:

| Property    | Type             | Value                                                                                        |
|-------------|------------------|----------------------------------------------------------------------------------------------|
| `url`       | `string`         | The full URL to the destination                                                              |
| `text`      | `string`         | The provided link text                                                                       |
| `target`    | `string`         | A `target` attribute string; `_blank` when opening in a new window                           |
| `type`      | `string`         | The chosen link type<br>**Possible values:** `entry`, `url`, `email`, `asset`, `term`, `tel` |
| `email`     | `?String`        | The entered email address<br>**Note:** Only populated for “Email” links                      |
| `phone`     | `?String`        | The entered phone number<br>**Note:** Only populated for “Phone” links                       |
| `entry`     | `?Entry`         | The full `Entry` object<br>**Note:** Only populated for “Entry” links                        |
| `asset`     | `?Asset`         | The full `Asset` object<br>**Note:** Only populated for “Asset” links                        |
| `term`      | `?LocalizedTerm` | The full `LocalizedTerm` object<br>**Note:** Only populated for “Term” links                 |
| `newWindow` | `bool`           | Whether the field is set to open in a new window                                             |
| `link`      | `string`         | The raw URL, `mailto:`/`tel:` string, or Statamic relationship reference                     |

> **Note** — When linking to a Term, Hyperlink will generate a URL to the [global term view](https://statamic.dev/taxonomies#routing). If you’d like to link to the term in a specific collection, you’ll need to use the `term` property to generate that collection-specific URL in your templates. 

## Validation

In general, Hyperlink fields require both the link value and link text. If the Hyperlink field is optional in the Blueprint, a “None” option is added to the available link types for authors to explicitly leave the field blank (and it’s the default for new entries).

### Type-Specific Validation
Some link types are also validated conditionally:
- **URL** — Uses Laravel’s [`url`](https://laravel.com/docs/master/validation#rule-url) validation rule unless the value begins with `#`, `.`, or `/` (so anchors like `#contact` and relative paths like `./sub-page` work just fine)
- **Email** — Uses Laravel’s [`email`](https://laravel.com/docs/master/validation#rule-email) validation rule
- **Phone** — Uses Laravel’s [`regex`](https://laravel.com/docs/master/validation#rule-regex) validation rule with the broad pattern `/[\d,.+\-()]/`, which aims to prevent big errors, but accommodate the variety of formats allowed in `tel:` links

## Schema

Hyperlink data is stored as a simple YAML structure. For example, if your field handle ws `cta`:

```yaml
---
title: My Entry
cta:
  type: url
  link: 'https://statamic.com'
  text: 'Rad Button Text'
  newWindow: false
---
```

Entry, Asset, and Term links are stored using an ID reference, so links stay up-to-date event if the related item’s URL changes:

```yaml
---
title: My Entry
cta:
  type: entry
  link: 'entry::AAA111B2-3333-4444-C555-D6E7FGH8I910'
  text: 'Rad Entry Link'
  newWindow: false
---
```

If your field configuration supports multiple links, the YAML structure will be an array. For example, if your field handle was `buttons`:

```yaml
---
title: My Entry
buttons:
  -
    type: entry
    link: 'entry::AAA111B2-3333-4444-C555-D6E7FGH8I910'
    text: 'Rad Entry Link'
    newWindow: false
  -
    type: url
    link: 'https://statamic.com'
    text: 'Even More Rad Link'
    newWindow: true
---
```

## Licensing

Hyperlink is a Commercial Addon.

You can use it for free while in development, but requires a license to use on a live site. Learn more or buy a license on [The Statamic Marketplace](https://statamic.com/addons/bencarr/hyperlink)!

