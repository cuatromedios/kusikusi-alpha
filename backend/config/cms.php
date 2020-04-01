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
        "page" => [
            "content" => [
                "lang.title" => [
                    "fulltextsearch" => true
                ],
                "lang.summary" => [
                    "fulltextsearch" => true
                ],
                "lang.body" => [
                    "fulltextsearch" => true
                ],
            ],
            "allowedChildren" => []
        ],
        "section" => [
            "content" => [
                "lang.title" => [
                    "fulltextsearch" => true
                ],
                "lang.summary" => [
                    "fulltextsearch" => true
                ]
            ],
            "allowedChildren" => ['page']
        ],
        "medium" => [
            "content" => [
                "lang.title" => [
                    "fulltextsearch" => true
                ],
                "size"
            ],
            "allowedChildren" => []
        ]
    ]
];
