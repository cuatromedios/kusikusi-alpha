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
    "langs" => ["en"], // The first lang will be the default each time the entity is loaded
    "models" => [
        "home" => [
            "content" => [
                "title" => [ "multilang" => false ],
                "welcome" => ["multilang" => false ],
                "footer" => [ "multilang" => false ],
            ]
        ],
        "page" => [
            "content" => [
                "title" => [
                    "fulltextsearch" => true
                ],
                "summary" => [
                    "fulltextsearch" => true
                ],
                "body" => [
                    "fulltextsearch" => true
                ],
            ],
            "allowedChildren" => []
        ],
        "section" => [
            "content" => [
                "title" => [
                    "fulltextsearch" => true
                ],
                "summary" => [
                    "fulltextsearch" => true
                ],
                "body" => []
            ],
            "allowedChildren" => ['page']
        ],
        "medium" => [
            "content" => [
                "title" => [
                    "fulltextsearch" => true
                ],
                "size"
            ],
            "allowedChildren" => []
        ]
    ]
];
