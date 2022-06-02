<?php

return [
    'config' => [
        'profile' => [
            'display' => 'Hyperlink Profile',
            'instructions' => 'Re-use configurations across blueprints using Profiles. Configure profiles in `config/statamic/hyperlink.php`. [Learn more about profiles](https://statamic.com/addons/bencarr/hyperlink)',
        ],
        'types' => [
            'display' => 'Enabled Link Types',
            'instructions' => 'Select which types of links authors should be able to select.',
        ],
        'collections' => [
            'instructions' => 'Entries from these collections will be available. Leave blank for all collections.',
        ],
        'container' => [
            'instructions' => 'Assets from this container will be available. Leave blank for all containers.',
        ],
        'taxonomies' => [
            'instructions' => 'Terms from these taxonomies will be available. Leave blank for all taxonomies.',
        ],
    ],
    'field' => [
        'labels' => [
            'text' => 'Link Text',
            'new_window' => 'New window?',
            'link' => 'Destination',
        ],
    ],
];
