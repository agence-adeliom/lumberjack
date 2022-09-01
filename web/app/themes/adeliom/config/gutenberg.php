<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'categories' => [
        "listing" => ['title' => __('Remontées automatiques'), 'icon'  => 'images-alt'],
    ],
    "settings" => [
      "disable_blocks" => "/((core|yoast|yoast-seo|gravityforms)\/\w*)/"
    ],
    "templates" => [
        "page" => [
            "blocks" => [
                "core/archives",
                "!remove/block-style"
            ],
            "template" => [
                ["core/image", [
                    'align' => 'left'
                ]],
                ["core/heading", [
                    'placeholder' => 'Add Author...',
                ]],
                ["core/paragraph", [
                    'placeholder' => 'Add Description2...'
                ]]
            ],
            //"template_lock" => "insert"
        ],
        "post" => [ "blocks" => "core/paragraph" ],
        "tpl-home.php" => [
            "template" => [
                ["core/image", [
                    'align' => 'left'
                ]],
                ["core/heading", [
                    'placeholder' => 'Add Author...',
                ]],
                ["core/paragraph", [
                    'placeholder' => 'Add Description2...'
                ]]
            ],
            "template_lock" => "insert"
        ],
        "tpl-test.php" => false
    ]
];
