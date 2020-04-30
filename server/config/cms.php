<?php
/*
 * This is general configuration needed for the CMS.
 *
 * WARNING !!!!!
 *
 * THIS INFORMATION IS FOR PUBLIC ACCESS!!! DO NOT SET HERE SENSITIVE INFORMATION LIKE API KEYS OR PASSWORDS
 *
 */

/*
 * Return the configuration structure
 * Icons are the name of the Material Design Icons https://material.io/tools/icons/?style=baseline
 */

return [
    "langs" => ["en_US", "es_ES"], // The first lang will be the default each time the entity is loaded
    "page_size" => 25, // Default page size if not defined in the call
    "token_expiration_in_seconds" => 0, // Seconds to the token to be expired or 0
    "short_id_length" => 10, // Change if you database is going to be veeeery big. Maximum 16.
    "models" => [
        "home" => [
            "icon" => "home",
            "name" => "models.home",
            "views" => ["home", "home2"],
            "form" => [
                [
                    "label" => "contents.contents",
                    "components" => [
                        ["component" => "nq-input", "value" => "contents.title", "label" => "contents.title", "props" => ["size" => "xl"]],
                        ["component" => "nq-input", "value" => "contents.welcome", "label" => "contents.summary", "props" => ["type" => "textarea"]],
                        ["component" => "nq-input", "value" => "contents.slug", "label" => "contents.slug"],
                        ["component" => "nq-input", "value" => "view", "label" => "Vista"]
                    ],
                ],
                [
                    "label" => "contents.children",
                    "components" => [
                        ["component" => "children", "props" => ["models" => ["section", "page"]]]
                    ],
                ]
            ]
        ],
        "page" => [
            "icon" => "description",
            "name" => "models.page",
            "form" => [
                [
                    "label" => "content.contents",
                    "components" => [
                        ["component" => "nq-input", "value" => "contents.title", "label" => "contents.title"],
                        ["component" => "nq-input", "value" => "contents.welcome", "label" => "content.summary"],
                        ["component" => "nq-input", "value" => "contents.slug", "label" => "contents.slug"]
                    ],
                ]
            ]
        ],
        "section" => [
            "icon" => "folder",
            "name" => "models.section",
            "form" => [
                [
                    "label" => "content.contents",
                    "components" => [
                        ["component" => "nq-input", "value" => "contents.title", "label" => "contents.title"],
                        ["component" => "nq-input", "value" => "contents.summary", "label" => "contents.summary"],
                        ["component" => "nq-input", "value" => "contents.slug", "label" => "contents.slug"]
                    ],
                ],
                [
                    "label" => "content.children",
                    "components" => [
                        ["component" => "children", "props" => ["models" => ["page"]]]
                    ],
                ]
            ]
        ],
        "medium" => [
        ]
    ]
];
