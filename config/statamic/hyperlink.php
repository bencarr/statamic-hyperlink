<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Profiles
    |--------------------------------------------------------------------------
    |
    | Here you may specify available profiles that your Hyperlink fields
    | can use. Key the array with a name for each profile. Choose the
    | profile to use on each field from the field settings screen.
    |
    */
    'profiles' => [

        'default' => [
            /*
             |-----------------------------------------------------------------
             | Enabled Types
             |-----------------------------------------------------------------
             |
             | Choose which link types authors should be allowed to select.
             | Enabled types appear in the order set here. All available
             | link types are enabled by default in the order listed.
             |
             | Available types: "entry", "url", "email", "asset", "term", "tel"
             |
             */

            'types' => [
                'entry',
                'url',
                'email',
                'asset',
                'term',
                'tel',
            ],

            /*
             |-----------------------------------------------------------------
             | Collections, Containers, and Taxonomies
             |-----------------------------------------------------------------
             |
             | Restrict relationship selections to only specific collections,
             | containers, or taxonomies. Constraints here will only apply
             | if the corresponding link type is enabled in the profile.
             |
             | Available constraints: "collections", "containers", "taxonomies"
             |
             */

            'collections' => [],
            'containers' => [],
            'taxonomies' => [],

            /*
             |-----------------------------------------------------------------
             | Link Count
             |-----------------------------------------------------------------
             |
             | Choose how many links users can provide when authoring content.
             | Minimum count will only be validated when field is required.
             |
             */

            'min_items' => 0,
            'max_items' => 1,
        ],

        // 'my-next-profile' => [
        //     'types' => ['entry', 'url', 'email', 'asset', 'term'],
        //     'collections' => ['pages'],
        //     'containers' => ['assets'],
        //     'taxonomies' => ['categories'],
        // ],
    ],
];
