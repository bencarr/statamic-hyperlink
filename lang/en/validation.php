<?php

return [
    'link' => [
        'required' => [
            'none' => 'Link type is required',
            'entry' => 'Entry is required',
            'asset' => 'Asset is required',
            'term' => 'Term is required',
            'email' => 'Email address is required',
            'url' => 'URL is required',
        ],
    ],
    'text' => [
        'required' => 'Link text is required',
    ],
    'email' => [
        'email' => 'Email is not valid',
    ],
    'tel' => [
        'regex' => 'Phone number is not valid',
    ],
    'url' => [
        'url' => 'URL is not valid',
    ],
];
