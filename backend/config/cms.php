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
    "langs" => ["en", "es"], // The first lang will be the default each time the entity is loaded
    "models" => [
        "home" => [
            "content" => [
                "title" => [ "multilang" => true ],
                "welcome" => [ "multilang" => true ],
                "footer" => [ "multilang" => true ],
            ]
        ],
        "page" => [
            "content" => [
                "title" => [ "multilang" => true ],
                "summary" => [ "multilang" => true ],
                "body" => [ "multilang" => true ],
            ],
            "allowedChildren" => []
        ],
        "section" => [
            "content" => [
                "title" => [ "multilang" => true ],
                "summary" => [ "multilang" => true ],
                "body" => []
            ],
            "allowedChildren" => ['page']
        ],
        "medium" => [
            "content" => [
                "title" => [ "multilang" => true ],
                "size"
            ],
            "allowedChildren" => []
        ]
    ]
];
