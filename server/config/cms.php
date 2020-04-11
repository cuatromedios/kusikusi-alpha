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
    "models" => [
        "home" => [
            "properties" => [
                "title" => [ "multilang" => true ],
                "welcome" => [ "multilang" => true ],
                "footer" => [ "multilang" => true ],
            ]
        ],
        "page" => [
            "properties" => [
                "title" => [ "multilang" => true ],
                "summary" => [ "multilang" => true ],
                "body" => [ "multilang" => true ],
            ],
            "allowedChildren" => []
        ],
        "section" => [
            "properties" => [
                "title" => [ "multilang" => true ],
                "summary" => [ "multilang" => true ],
                "body" => []
            ],
            "allowedChildren" => ['page']
        ],
        "medium" => [
            "properties" => [
                "title" => [ "multilang" => true ],
                "size"
            ],
            "allowedChildren" => []
        ]
    ]
];
