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
            "allowedChildren" => ["section", "page"],
            "ui" => [
                [
                    "title" => "content.contents",
                    "components" => [
                        ["component" => "input", "value" => "contents.title", "label" => "content.title"],
                        ["component" => "input", "value" => "contents.welcome", "label" => "content.summary"]
                    ],
                ]
            ]
        ],
        "page" => [
        ],
        "section" => [
            "allowedChildren" => ['page']
        ],
        "medium" => [
        ]
    ]
];
