---
title: API Reference

language_tabs:
- javascript
- php
- bash

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://127.0.0.1:8000/docs/collection.json)

<!-- END_INFO -->

#Cms


<!-- START_d6d109bb80788f15184b241103150600 -->
## Get the CMS configuration.

Returns an object with the configuration for the CMS frontend.

> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/cms/config"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'http://127.0.0.1:8000/api/cms/config',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/api/cms/config" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```


> Example response (200):

```json
{
    "langs": [
        "en_US",
        "es_ES"
    ],
    "page_size": 25,
    "token_expiration_in_seconds": 0,
    "short_id_length": 10,
    "models": {
        "home": {
            "icon": "home",
            "name": "models.home",
            "views": [
                "home",
                "home2"
            ],
            "form": [
                {
                    "label": "contents.contents",
                    "components": [
                        {
                            "component": "nq-input",
                            "value": "contents.title",
                            "label": "contents.title",
                            "props": {
                                "size": "xl"
                            }
                        },
                        {
                            "component": "html-editor",
                            "value": "contents.welcome",
                            "label": "contents.summary",
                            "props": {
                                "type": "textarea"
                            }
                        },
                        {
                            "component": "slug",
                            "value": "contents.slug",
                            "label": "contents.slug"
                        },
                        {
                            "component": "nq-input",
                            "value": "view",
                            "label": "Vista"
                        }
                    ]
                },
                {
                    "label": "contents.children",
                    "components": [
                        {
                            "component": "children",
                            "props": {
                                "models": [
                                    "section",
                                    "page"
                                ],
                                "order_by": "contents.title",
                                "tags": [
                                    "menu",
                                    "footer"
                                ]
                            }
                        }
                    ]
                },
                {
                    "label": "contents.media",
                    "components": [
                        {
                            "component": "media",
                            "props": {
                                "allowed": [
                                    "*"
                                ],
                                "tags": [
                                    "hero",
                                    "og"
                                ]
                            }
                        }
                    ]
                }
            ]
        },
        "page": {
            "icon": "description",
            "name": "models.page",
            "form": [
                {
                    "label": "contents.contents",
                    "components": [
                        {
                            "component": "nq-input",
                            "value": "contents.title",
                            "label": "contents.title"
                        },
                        {
                            "component": "nq-input",
                            "value": "contents.welcome",
                            "label": "content.summary"
                        },
                        {
                            "component": "slug",
                            "value": "contents.slug",
                            "label": "contents.slug"
                        }
                    ]
                },
                {
                    "label": "contents.media",
                    "components": [
                        {
                            "component": "media",
                            "props": {
                                "allowed": [
                                    "webImages",
                                    "webVideos",
                                    "xhr"
                                ],
                                "tags": [
                                    "icon",
                                    "gallery"
                                ]
                            }
                        }
                    ]
                }
            ]
        },
        "section": {
            "icon": "folder",
            "name": "models.section",
            "form": [
                {
                    "label": "contents.contents",
                    "components": [
                        {
                            "component": "nq-input",
                            "value": "contents.title",
                            "label": "contents.title"
                        },
                        {
                            "component": "nq-input",
                            "value": "contents.summary",
                            "label": "contents.summary"
                        },
                        {
                            "component": "nq-input",
                            "value": "contents.slug",
                            "label": "contents.slug"
                        }
                    ]
                },
                {
                    "label": "contents.children",
                    "components": [
                        {
                            "component": "children",
                            "props": {
                                "models": [
                                    "page"
                                ]
                            }
                        }
                    ]
                }
            ]
        },
        "medium": {
            "icon": "insert_drive_file",
            "name": "models.medium",
            "form": [
                {
                    "label": "contents.contents",
                    "components": [
                        {
                            "component": "nq-input",
                            "value": "contents.title",
                            "label": "contents.title"
                        }
                    ]
                }
            ]
        }
    },
    "formats": {
        "webImages": [
            "jpeg",
            "jpg",
            "png",
            "gif"
        ],
        "images": [
            "jpeg",
            "jpg",
            "png",
            "gif",
            "tif",
            "tiff",
            "iff",
            "bmp",
            "psd"
        ],
        "audios": [
            "mp3",
            "wav",
            "aiff",
            "aac",
            "oga",
            "pcm",
            "flac"
        ],
        "webAudios": [
            "mp3",
            "oga"
        ],
        "videos": [
            "mov",
            "mp4",
            "qt",
            "avi",
            "mpe",
            "mpeg",
            "ogg",
            "m4p",
            "m4v",
            "flv",
            "wmv"
        ],
        "webVideos": [
            "webm",
            "mp4",
            "ogg",
            "m4p",
            "m4v"
        ],
        "documents": [
            "doc",
            "docx",
            "xls",
            "xlsx",
            "ppt",
            "pptx",
            "pdf",
            "htm",
            "html",
            "txt",
            "rtf",
            "csv",
            "pps",
            "ppsx",
            "odf",
            "key",
            "pages",
            "numbers"
        ]
    }
}
```

### HTTP Request
`GET api/cms/config`


<!-- END_d6d109bb80788f15184b241103150600 -->

#Entity


<!-- START_e361c4dd3cbd2391c19b5f4811a2b853 -->
## Get a collection of  entities.

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
Returns a paginated collection of entities, filtered by all set conditions.

> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/entities[/expedita]"
);

let params = {
    "select": "id,model,properties.price",
    "order-by": "model,properties.price:desc,contents.title",
    "of-model": "page",
    "only-published": "true",
    "child-of": "home",
    "parent-of": "8fguTpt5SB",
    "ancestor-of": "enKSUfUcZN",
    "descendant-of": "xAaqz2RPyf",
    "siblings-of": "_tuKwVy8Aa",
    "related-by": "ElFYpgEvWS",
    "relating": "enKSUfUcZN",
    "media-of": "enKSUfUcZN",
    "with": "media,contents,entities_related, entities_related.contents (nested relations)",
    "per-page": "6",
};
Object.keys(params)
    .forEach(key => url.searchParams.append(key, params[key]));

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'http://127.0.0.1:8000/api/entities[/expedita]',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        'query' => [
            'select'=> 'id,model,properties.price',
            'order-by'=> 'model,properties.price:desc,contents.title',
            'of-model'=> 'page',
            'only-published'=> 'true',
            'child-of'=> 'home',
            'parent-of'=> '8fguTpt5SB',
            'ancestor-of'=> 'enKSUfUcZN',
            'descendant-of'=> 'xAaqz2RPyf',
            'siblings-of'=> '_tuKwVy8Aa',
            'related-by'=> 'ElFYpgEvWS',
            'relating'=> 'enKSUfUcZN',
            'media-of'=> 'enKSUfUcZN',
            'with'=> 'media,contents,entities_related, entities_related.contents (nested relations)',
            'per-page'=> '6',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/api/entities[/expedita]?select=id%2Cmodel%2Cproperties.price&order-by=model%2Cproperties.price%3Adesc%2Ccontents.title&of-model=page&only-published=true&child-of=home&parent-of=8fguTpt5SB&ancestor-of=enKSUfUcZN&descendant-of=xAaqz2RPyf&siblings-of=_tuKwVy8Aa&related-by=ElFYpgEvWS&relating=enKSUfUcZN&media-of=enKSUfUcZN&with=media%2Ccontents%2Centities_related%2C+entities_related.contents+%28nested+relations%29&per-page=6" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```


> Example response (200):

```json
{
    "current_page": 1,
    "data": [
        {
            "id": "35337182-7a0c-44c4-a11f-68cd9da930b2",
            "content": {
                "body": {
                    "en_US": "Consequatur tempora deleniti ea cum totam. Qui quidem quis eius expedita atque officia incidunt."
                },
                "slug": {
                    "en_US": "mrs-karlie-torp"
                },
                "title": {
                    "en_US": "Felipa Haley PhD"
                },
                "summary": {
                    "en_US": "Railroad Inspector"
                }
            },
            "model": "page",
            "kind": "ancestor",
            "position": 0,
            "depth": 1,
            "tags": null
        },
        {
            "id": "4cbbd1cd-3708-4ac2-8ff7-7261cd6fbe81",
            "content": {
                "body": {
                    "en_US": "Voluptatem sed autem voluptas eum fuga amet neque. Odit accusantium nemo et architecto."
                },
                "slug": {
                    "en_US": "amely-koepp"
                },
                "title": {
                    "en_US": "Ashley D'Amore"
                },
                "summary": {
                    "en_US": "Homeland Security"
                }
            },
            "model": "page",
            "kind": "ancestor",
            "position": 0,
            "depth": 1,
            "tags": null
        },
        {
            "id": "6d776ddf-b416-42c7-86cf-c665770c96ff",
            "content": {
                "body": {
                    "en_US": "Error animi autem sunt et. Qui quia eos sunt sint dicta eligendi quasi. Ut quae aut facilis vel."
                },
                "slug": {
                    "en_US": "janis-jenkins-jr"
                },
                "title": {
                    "en_US": "Mr. Reagan Deckow I"
                },
                "summary": {
                    "en_US": "Pesticide Sprayer"
                }
            },
            "model": "page",
            "kind": "ancestor",
            "position": 0,
            "depth": 1,
            "tags": null
        },
        {
            "id": "7d504be2-ca0f-4836-b6d2-f00c2dff209c",
            "content": {
                "body": {
                    "en_US": "Consequatur deserunt non quo sint. Voluptas sint et aliquam qui."
                },
                "slug": {
                    "en_US": "mr-xavier-yundt-iv"
                },
                "title": {
                    "en_US": "Joyce Kohler MD"
                },
                "summary": {
                    "en_US": "Transportation Equipment Maintenance"
                }
            },
            "model": "page",
            "kind": "ancestor",
            "position": 0,
            "depth": 1,
            "tags": null
        },
        {
            "id": "801892f7-8dcb-4fdc-a1fd-5251ceb6af09",
            "content": {
                "body": {
                    "en_US": "Assumenda quaerat ipsam dolores ducimus itaque earum sit. Aut dolorem nisi et harum sunt molestiae."
                },
                "slug": {
                    "en_US": "ms-lauretta-rohan"
                },
                "title": {
                    "en_US": "Dr. Briana Bergstrom DVM"
                },
                "summary": {
                    "en_US": "Hotel Desk Clerk"
                }
            },
            "model": "page",
            "kind": "ancestor",
            "position": 0,
            "depth": 1,
            "tags": null
        }
    ],
    "first_page_url": "http:\/\/127.0.0.1:8000\/api\/entities?relating=bee7a88a-459c-419a-9b3f-96ad3d3822b5%3Aancestor&page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http:\/\/127.0.0.1:8000\/api\/entities?relating=bee7a88a-459c-419a-9b3f-96ad3d3822b5%3Aancestor&page=1",
    "next_page_url": null,
    "path": "http:\/\/127.0.0.1:8000\/api\/entities",
    "per_page": 100,
    "prev_page_url": null,
    "to": 5,
    "total": 5
}
```

### HTTP Request
`GET api/entities[/{model_name}]`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `model_name` |  optional  | If a model name is provided, the results will have the corresponding scope and special defined relations and accesosrs will be available.
#### Query Parameters

Parameter | Status | Description
--------- | ------- | ------- | -----------
    `select` |  optional  | A comma separated list of fields of the entity to include. It is possible to flat the properties json column using a dot syntax.
    `order-by` |  optional  | A comma separated lis of fields to order by.
    `of-model` |  optional  | (filter) The name of the model the entities should be.
    `only-published` |  optional  | (filter) Get only published, not deleted entities, true if not set.
    `child-of` |  optional  | (filter) The id or short id of the entity the result entities should be child of.
    `parent-of` |  optional  | (filter) The id or short id of the entity the result entities should be parent of (will return only one).
    `ancestor-of` |  optional  | (filter) The id or short id of the entity the result entities should be ancestor of.
    `descendant-of` |  optional  | (filter) The id or short id of the entity the result entities should be descendant of.
    `siblings-of` |  optional  | (filter) The id or short id of the entity the result entities should be siblings of.
    `related-by` |  optional  | (filter) The id or short id of the entity the result entities should have been called by using a relation. Can be added a filter to a kind of relation for example: theShortId:category. The ancestor kind of relations are discarted unless are explicity specified.
    `relating` |  optional  | (filter) The id or short id of the entity the result entities should have been a caller of using a relation. Can be added a filder to a kind o relation for example: shortFotoId:medium to know the entities has caller that medium. The ancestor kind of relations are discarted unless are explicity specified.
    `media-of` |  optional  | (filter) The id or short id of the entity the result entities should have a media relation to.
    `with` |  optional  | A comma separated list of relationships should be included in the result.
    `per-page` |  optional  | The amount of entities per page the result should be the amount of entities on a single page.

<!-- END_e361c4dd3cbd2391c19b5f4811a2b853 -->

<!-- START_f8974bfc4b59ca48260d995e658e9903 -->
## Reorders an array of relations

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
Receive an array of relation ids, and sets the individual position to its index in the array.

> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/entities/relations/reorder"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "relation_ids": []
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch(
    'http://127.0.0.1:8000/api/entities/relations/reorder',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        'json' => [
            'relation_ids' => [],
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X PATCH \
    "http://127.0.0.1:8000/api/entities/relations/reorder" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"relation_ids":[]}'

```


> Example response (200):

```json
{
    "relations": [
        {
            "relation_id": "JvE3WPG504",
            "position": 1
        },
        {
            "relation_id": "izMhhYpXA7",
            "position": 2
        }
    ]
}
```

### HTTP Request
`PATCH api/entities/relations/reorder`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `relation_ids` | array |  required  | An array of relation ids to reorder. Example ['s4FG56mkdRT5', 'FG56mkdRT5s3', '4FG56mkdRT5d']
    
<!-- END_f8974bfc4b59ca48260d995e658e9903 -->

<!-- START_452b2ebcc9c1afb7cfdc2a7e8a3caf40 -->
## Retrieve the entity for the given ID.

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/entity/corporis"
);

let params = {
    "select": "id,model,properties.price",
    "with": "media,contents,entities_related, entities_related.contents (nested relations)",
};
Object.keys(params)
    .forEach(key => url.searchParams.append(key, params[key]));

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'http://127.0.0.1:8000/api/entity/corporis',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        'query' => [
            'select'=> 'id,model,properties.price',
            'with'=> 'media,contents,entities_related, entities_related.contents (nested relations)',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/api/entity/corporis?select=id%2Cmodel%2Cproperties.price&with=media%2Ccontents%2Centities_related%2C+entities_related.contents+%28nested+relations%29" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```


> Example response (200):

```json
{
    "id": "DkESBOv-wT",
    "model": "page-m",
    "properties": {
        "prop1": "Z",
        "prop2": "Z"
    },
    "view": "yuyxo",
    "parent_entity_id": "home",
    "is_active": true,
    "created_by": null,
    "updated_by": null,
    "published_at": "2020-08-27 14:55:30",
    "unpublished_at": "9999-12-31 23:59:59",
    "version": 18,
    "version_tree": 0,
    "version_relations": 9,
    "version_full": 27,
    "created_at": "2020-04-20T19:36:58.000000Z",
    "updated_at": "2020-04-20T20:25:19.000000Z",
    "deleted_at": null
}
```

### HTTP Request
`GET api/entity/{entity_id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `entity_id` |  optional  | The id of the entity to show.
#### Query Parameters

Parameter | Status | Description
--------- | ------- | ------- | -----------
    `select` |  optional  | A comma separated list of fields of the entity to include. It is possible to flat the properties json column using a dot syntax.
    `with` |  optional  | A comma separated list of relationships should be included in the result.

<!-- END_452b2ebcc9c1afb7cfdc2a7e8a3caf40 -->

<!-- START_af9c0c62a0bddef37647abebdc3538da -->
## Creates a new entity.

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/entity"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "model": "page.",
    "view": "page",
    "published_at": "2020-02-02 12:00:00.",
    "unpublished_at": "2020-02-02 12:00:00.",
    "properties": "{\"price\": 200, \"format\": \"jpg\"}",
    "id": "home",
    "contents": "{ \"title\": {\"en_US\": \"The page M\", \"es_ES\": \"La p\u00e1gina M\"}, \"slug\": {\"en_US\": \"page-m\", \"es_ES\": \"pagina-m\"}}",
    "relations": "\"relations\": [{\"called_entity_id\": \"mf4gWE45pm\",\"kind\": \"category\",\"position\": 2, \"tags\":[\"main\"]}]"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'http://127.0.0.1:8000/api/entity',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        'json' => [
            'model' => 'page.',
            'view' => 'page',
            'published_at' => '2020-02-02 12:00:00.',
            'unpublished_at' => '2020-02-02 12:00:00.',
            'properties' => '{"price": 200, "format": "jpg"}',
            'id' => 'home',
            'contents' => '{ "title": {"en_US": "The page M", "es_ES": "La página M"}, "slug": {"en_US": "page-m", "es_ES": "pagina-m"}}',
            'relations' => '"relations": [{"called_entity_id": "mf4gWE45pm","kind": "category","position": 2, "tags":["main"]}]',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X POST \
    "http://127.0.0.1:8000/api/entity" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"model":"page.","view":"page","published_at":"2020-02-02 12:00:00.","unpublished_at":"2020-02-02 12:00:00.","properties":"{\"price\": 200, \"format\": \"jpg\"}","id":"home","contents":"{ \"title\": {\"en_US\": \"The page M\", \"es_ES\": \"La p\u00e1gina M\"}, \"slug\": {\"en_US\": \"page-m\", \"es_ES\": \"pagina-m\"}}","relations":"\"relations\": [{\"called_entity_id\": \"mf4gWE45pm\",\"kind\": \"category\",\"position\": 2, \"tags\":[\"main\"]}]"}'

```


> Example response (200):

```json
{
    "model": "page-m",
    "view": "page",
    "parent_entity_id": "home",
    "published_at": "2020-08-27T14:55:30",
    "properties": {
        "prop1": "b",
        "prop2": 1
    },
    "id": "DkESBOv-wT",
    "updated_at": "2020-04-20T19:36:58.000000Z",
    "created_at": "2020-04-20T19:36:58.000000Z"
}
```

### HTTP Request
`POST api/entity`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `model` | string |  required  | The model name.
        `view` | string |  optional  | The name of the view to use. Default: the same name of the model.
        `published_at` | date |  optional  | A date time the entity should be published. Default: current date time.
        `unpublished_at` | date |  optional  | A date time the entity should be published. Default: 9999-12-31 23:59:59.
        `properties` | string |  optional  | An object with properties.
        `id` | string |  optional  | You can set your own ID, a maximum of 16, safe characters: A-Z, a-z, 0-9, _ and -. Default: autogenerated.
        `contents` | array |  optional  | An array of contents to be created for the entity.
        `relations` | arrya |  optional  | An array of relations to be created for the entity.
    
<!-- END_af9c0c62a0bddef37647abebdc3538da -->

<!-- START_ec8e08b9a5493ca2f4077a1528c5fc8d -->
## Updates an entity.

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/entity/minus"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "view": "page",
    "published_at": "2020-02-02 12:00:00.",
    "unpublished_at": "2020-02-02 12:00:00.",
    "properties": "{\"price\": 200, \"format\": \"jpg\"}",
    "id": "home",
    "contents": "{ \"title\": {\"en_US\": \"The page M\", \"es_ES\": \"La p\u00e1gina M\"}, \"slug\": {\"en_US\": \"page-m\", \"es_ES\": \"pagina-m\"}}",
    "relations": "\"relations\": [{\"called_entity_id\": \"mf4gWE45pm\",\"kind\": \"category\",\"position\": 2, \"tags\":[\"main\"]}]"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->patch(
    'http://127.0.0.1:8000/api/entity/minus',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        'json' => [
            'view' => 'page',
            'published_at' => '2020-02-02 12:00:00.',
            'unpublished_at' => '2020-02-02 12:00:00.',
            'properties' => '{"price": 200, "format": "jpg"}',
            'id' => 'home',
            'contents' => '{ "title": {"en_US": "The page M", "es_ES": "La página M"}, "slug": {"en_US": "page-m", "es_ES": "pagina-m"}}',
            'relations' => '"relations": [{"called_entity_id": "mf4gWE45pm","kind": "category","position": 2, "tags":["main"]}]',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X PATCH \
    "http://127.0.0.1:8000/api/entity/minus" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"view":"page","published_at":"2020-02-02 12:00:00.","unpublished_at":"2020-02-02 12:00:00.","properties":"{\"price\": 200, \"format\": \"jpg\"}","id":"home","contents":"{ \"title\": {\"en_US\": \"The page M\", \"es_ES\": \"La p\u00e1gina M\"}, \"slug\": {\"en_US\": \"page-m\", \"es_ES\": \"pagina-m\"}}","relations":"\"relations\": [{\"called_entity_id\": \"mf4gWE45pm\",\"kind\": \"category\",\"position\": 2, \"tags\":[\"main\"]}]"}'

```


> Example response (200):

```json
{
    "model": "page-m",
    "view": "page",
    "parent_entity_id": "home",
    "published_at": "2020-08-27T14:55:30",
    "properties": {
        "prop1": "b",
        "prop2": 1
    },
    "id": "DkESBOv-wT",
    "updated_at": "2020-04-20T19:36:58.000000Z",
    "created_at": "2020-04-20T19:36:58.000000Z"
}
```

### HTTP Request
`PATCH api/entity/{entity_id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `entity_id` |  optional  | The id of the entity to update
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `view` | string |  optional  | The name of the view to use. Default: the same name of the model.
        `published_at` | date |  optional  | A date time the entity should be published. Default: current date time.
        `unpublished_at` | date |  optional  | A date time the entity should be published. Default: 9999-12-31 23:59:59.
        `properties` | string |  optional  | An object with properties.
        `id` | string |  optional  | You can set your own ID, a maximum of 16, safe characters: A-Z, a-z, 0-9, _ and -. Default: autogenerated.
        `contents` | array |  optional  | An array of contents to be created for the entity.
        `relations` | arrya |  optional  | An array of relations to be created for the entity.
    
<!-- END_ec8e08b9a5493ca2f4077a1528c5fc8d -->

<!-- START_df16605fe65733c4ef3009e2320cd8ea -->
## Deletes an entity.

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/entity/totam"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete(
    'http://127.0.0.1:8000/api/entity/totam',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X DELETE \
    "http://127.0.0.1:8000/api/entity/totam" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```


> Example response (200):

```json
{
    "id": "NiCJ5xKaIy",
    "deleted_at": "2020-04-20T21:19:27.000000Z"
}
```

### HTTP Request
`DELETE api/entity/{entity_id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `entity_id` |  optional  | The id of the entity to delete

<!-- END_df16605fe65733c4ef3009e2320cd8ea -->

<!-- START_7e4e246e270a0e3f971f7e4b399224bc -->
## Creates or updates a relation.

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/entity/1/relation"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "entity_called_id": "s4FG56mkdRT5",
    "kind": "medium | category",
    "tags": [],
    "position": 3,
    "depth": 13
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'http://127.0.0.1:8000/api/entity/1/relation',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        'json' => [
            'entity_called_id' => 's4FG56mkdRT5',
            'kind' => 'medium | category',
            'tags' => [],
            'position' => 3,
            'depth' => 13,
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X POST \
    "http://127.0.0.1:8000/api/entity/1/relation" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"entity_called_id":"s4FG56mkdRT5","kind":"medium | category","tags":[],"position":3,"depth":13}'

```


> Example response (200):

```json
{
    "relation_id": "WZ3PnzvNP4",
    "caller_entity_id": "Z4bdjFSzn5",
    "called_entity_id": "DtPatk4FNG",
    "kind": "reltest",
    "position": 2,
    "depth": 3,
    "tags": [
        "icon"
    ],
    "created_at": "2020-04-23T15:01:32.000000Z",
    "updated_at": "2020-04-23T15:10:14.000000Z"
}
```

### HTTP Request
`POST api/entity/{caller_entity_id}/relation`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `entity_caller_id` |  required  | The id of the entity to create or update a relation
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `entity_called_id` | string |  required  | The id of the entity to relate.
        `kind` | string |  required  | The kind of relation to create or update.
        `tags` | array |  optional  | An array of tags to add to the relation. Defaults to an empty array. Example ["icon", 'gallery"].
        `position` | integer |  optional  | The position of the relation.
        `depth` | integer |  optional  | Yet another number value to use freely for the relation, used in ancestor type of relation to define the distance between an entity and other in the tree. Example 1.
    
<!-- END_7e4e246e270a0e3f971f7e4b399224bc -->

<!-- START_50b6dbb4b64779ad76feb6520602228c -->
## Deletes a relation if exists.

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/entity/1/relation/1/medium | category"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->delete(
    'http://127.0.0.1:8000/api/entity/1/relation/1/medium | category',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X DELETE \
    "http://127.0.0.1:8000/api/entity/1/relation/1/medium | category" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```


> Example response (200):

```json
{
    "relation_id": "673KBPT778"
}
```

### HTTP Request
`DELETE api/entity/{caller_entity_id}/relation/{called_entity_id}/{kind}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `entity_caller_id` |  required  | The id of the entity to create or update a relation
    `entity_called_id` |  optional  | string required The id of the entity to relate.
    `kind` |  optional  | string required The kind of relation to create or update.

<!-- END_50b6dbb4b64779ad76feb6520602228c -->

<!-- START_523351e4c81e5b0e7f197835980dcc51 -->
## Creates a new entity with a relation.

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
Creates a new entity with a specific relation to another entity, the entity "id" and "caller_entity_id" should the same.

> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/entity/1/create_and_relate"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "model": "home",
    "kind": "medium | category",
    "view": "home",
    "published_at": "2020-02-02 12:00:00.",
    "unpublished_at": "2020-02-02 12:00:00.",
    "properties": "{\"price\": 200, \"format\": \"jpg\"}",
    "contents": "{ \"title\": {\"en_US\": \"The page M\", \"es_ES\": \"La p\u00e1gina M\"}, \"slug\": {\"en_US\": \"page-m\", \"es_ES\": \"pagina-m\"}}",
    "relations": "\"relations\": [{\"called_entity_id\": \"mf4gWE45pm\",\"kind\": \"category\",\"position\": 2, \"tags\":[\"main\"]}]",
    "tags": "[\"1\", '2\"].",
    "position": 3,
    "depth": 3
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'http://127.0.0.1:8000/api/entity/1/create_and_relate',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        'json' => [
            'model' => 'home',
            'kind' => 'medium | category',
            'view' => 'home',
            'published_at' => '2020-02-02 12:00:00.',
            'unpublished_at' => '2020-02-02 12:00:00.',
            'properties' => '{"price": 200, "format": "jpg"}',
            'contents' => '{ "title": {"en_US": "The page M", "es_ES": "La página M"}, "slug": {"en_US": "page-m", "es_ES": "pagina-m"}}',
            'relations' => '"relations": [{"called_entity_id": "mf4gWE45pm","kind": "category","position": 2, "tags":["main"]}]',
            'tags' => '["1", \'2"].',
            'position' => 3,
            'depth' => 3,
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X POST \
    "http://127.0.0.1:8000/api/entity/1/create_and_relate" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"model":"home","kind":"medium | category","view":"home","published_at":"2020-02-02 12:00:00.","unpublished_at":"2020-02-02 12:00:00.","properties":"{\"price\": 200, \"format\": \"jpg\"}","contents":"{ \"title\": {\"en_US\": \"The page M\", \"es_ES\": \"La p\u00e1gina M\"}, \"slug\": {\"en_US\": \"page-m\", \"es_ES\": \"pagina-m\"}}","relations":"\"relations\": [{\"called_entity_id\": \"mf4gWE45pm\",\"kind\": \"category\",\"position\": 2, \"tags\":[\"main\"]}]","tags":"[\"1\", '2\"].","position":3,"depth":3}'

```


> Example response (200):

```json
{
    "id": "tEQxMPF8hs",
    "model": "medium",
    "properties": [],
    "view": "medium",
    "parent_entity_id": null,
    "is_active": true,
    "created_by": null,
    "updated_by": null,
    "published_at": "2020-05-09T00:17:54+00:00",
    "unpublished_at": null,
    "version": 1,
    "version_tree": 0,
    "version_relations": 0,
    "version_full": 1,
    "created_at": "2020-05-09T00:17:54+00:00",
    "updated_at": "2020-05-09T00:17:54+00:00",
    "deleted_at": null,
    "thumb": "\/media\/tEQxMPF8hs\/thumb\/media.jpg",
    "preview": "\/media\/tEQxMPF8hs\/preview\/media.jpg",
    "entities_relating": [
        {
            "id": "4dnK2CJspO",
            "model": "page",
            "properties": {
                "exif": {
                    "COMPUTED": {
                        "html": "width=\"1280\" height=\"1102\"",
                        "Width": 1280,
                        "Height": 1102,
                        "IsColor": 1
                    },
                    "FileName": "phpovyCKl",
                    "FileSize": 82033,
                    "FileType": 2,
                    "MimeType": "image\/jpeg",
                    "FileDateTime": 1588959027,
                    "SectionsFound": ""
                },
                "size": 82033,
                "type": "image",
                "prop1": "Z",
                "prop2": "Z",
                "width": 1280,
                "format": "jpg",
                "height": 1102,
                "isAudio": false,
                "isImage": true,
                "isVideo": false,
                "mimeType": "image\/jpeg",
                "isDocument": false,
                "isWebAudio": false,
                "isWebImage": true,
                "isWebVideo": false,
                "originalName": "3evient.jpeg"
            },
            "view": "yuyxo",
            "parent_entity_id": "0qtTYPQhm4",
            "is_active": true,
            "created_by": null,
            "updated_by": null,
            "published_at": "2020-05-07T12:50:49+00:00",
            "unpublished_at": null,
            "version": 67,
            "version_tree": 0,
            "version_relations": 46,
            "version_full": 113,
            "created_at": "2020-05-07T12:50:49+00:00",
            "updated_at": "2020-05-08T17:31:54+00:00",
            "deleted_at": null,
            "relation": {
                "called_entity_id": "tEQxMPF8hs",
                "caller_entity_id": "4dnK2CJspO",
                "kind": "medium",
                "position": 41,
                "depth": 0,
                "tags": []
            }
        }
    ]
}
```

### HTTP Request
`POST api/entity/{caller_entity_id}/create_and_relate`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `entity_caller_id` |  required  | The id of the entity to create or update a relation
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `model` | string |  required  | The model name.
        `kind` | string |  required  | The kind of relation to create or update.
        `view` | string |  optional  | The name of the view to use. Default: the same name of the model.
        `published_at` | date |  optional  | A date time the entity should be published. Default: current date time.
        `unpublished_at` | date |  optional  | A date time the entity should be published. Default: 9999-12-31 23:59:59.
        `properties` | string |  optional  | An object with properties.
        `contents` | array |  optional  | An array of contents to be created for the entity.
        `relations` | arrya |  optional  | An array of relations to be created for the entity.
        `tags` | array |  optional  | An array of tags to add to the relation. Defaults to an empty array.
        `position` | integer |  optional  | The position of the relation.
        `depth` | integer |  optional  | Yet another number value to use freely for the relation, used in ancestor type of relation to define the distance between an entity and other in the tree. Example 1.
    
<!-- END_523351e4c81e5b0e7f197835980dcc51 -->

#Media


<!-- START_9936b325d480b36838e12b42abfc57fa -->
## Uploads a medium

> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/medium/odio/upload"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "file": "perspiciatis",
    "thumb": "quisquam"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'http://127.0.0.1:8000/api/medium/odio/upload',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        'json' => [
            'file' => 'perspiciatis',
            'thumb' => 'quisquam',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X POST \
    "http://127.0.0.1:8000/api/medium/odio/upload" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"file":"perspiciatis","thumb":"quisquam"}'

```


> Example response (200):

```json
{
    "current_page": 1,
    "data": [
        {
            "id": "35337182-7a0c-44c4-a11f-68cd9da930b2",
            "content": {
                "body": {
                    "en_US": "Consequatur tempora deleniti ea cum totam. Qui quidem quis eius expedita atque officia incidunt."
                },
                "slug": {
                    "en_US": "mrs-karlie-torp"
                },
                "title": {
                    "en_US": "Felipa Haley PhD"
                },
                "summary": {
                    "en_US": "Railroad Inspector"
                }
            },
            "model": "page",
            "kind": "ancestor",
            "position": 0,
            "depth": 1,
            "tags": null
        },
        {
            "id": "4cbbd1cd-3708-4ac2-8ff7-7261cd6fbe81",
            "content": {
                "body": {
                    "en_US": "Voluptatem sed autem voluptas eum fuga amet neque. Odit accusantium nemo et architecto."
                },
                "slug": {
                    "en_US": "amely-koepp"
                },
                "title": {
                    "en_US": "Ashley D'Amore"
                },
                "summary": {
                    "en_US": "Homeland Security"
                }
            },
            "model": "page",
            "kind": "ancestor",
            "position": 0,
            "depth": 1,
            "tags": null
        },
        {
            "id": "6d776ddf-b416-42c7-86cf-c665770c96ff",
            "content": {
                "body": {
                    "en_US": "Error animi autem sunt et. Qui quia eos sunt sint dicta eligendi quasi. Ut quae aut facilis vel."
                },
                "slug": {
                    "en_US": "janis-jenkins-jr"
                },
                "title": {
                    "en_US": "Mr. Reagan Deckow I"
                },
                "summary": {
                    "en_US": "Pesticide Sprayer"
                }
            },
            "model": "page",
            "kind": "ancestor",
            "position": 0,
            "depth": 1,
            "tags": null
        },
        {
            "id": "7d504be2-ca0f-4836-b6d2-f00c2dff209c",
            "content": {
                "body": {
                    "en_US": "Consequatur deserunt non quo sint. Voluptas sint et aliquam qui."
                },
                "slug": {
                    "en_US": "mr-xavier-yundt-iv"
                },
                "title": {
                    "en_US": "Joyce Kohler MD"
                },
                "summary": {
                    "en_US": "Transportation Equipment Maintenance"
                }
            },
            "model": "page",
            "kind": "ancestor",
            "position": 0,
            "depth": 1,
            "tags": null
        },
        {
            "id": "801892f7-8dcb-4fdc-a1fd-5251ceb6af09",
            "content": {
                "body": {
                    "en_US": "Assumenda quaerat ipsam dolores ducimus itaque earum sit. Aut dolorem nisi et harum sunt molestiae."
                },
                "slug": {
                    "en_US": "ms-lauretta-rohan"
                },
                "title": {
                    "en_US": "Dr. Briana Bergstrom DVM"
                },
                "summary": {
                    "en_US": "Hotel Desk Clerk"
                }
            },
            "model": "page",
            "kind": "ancestor",
            "position": 0,
            "depth": 1,
            "tags": null
        }
    ],
    "first_page_url": "http:\/\/127.0.0.1:8000\/api\/entities?relating=bee7a88a-459c-419a-9b3f-96ad3d3822b5%3Aancestor&page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http:\/\/127.0.0.1:8000\/api\/entities?relating=bee7a88a-459c-419a-9b3f-96ad3d3822b5%3Aancestor&page=1",
    "next_page_url": null,
    "path": "http:\/\/127.0.0.1:8000\/api\/entities",
    "per_page": 100,
    "prev_page_url": null,
    "to": 5,
    "total": 5
}
```

### HTTP Request
`POST api/medium/{entity_id}/upload`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `entity_id` |  optional  | The id of the entity to upload a medium or file
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `file` | required |  optional  | The file to be uploaded
        `thumb` | optional |  optional  | An optional file to represent the media, for example a thumb of a video
    
<!-- END_9936b325d480b36838e12b42abfc57fa -->

<!-- START_8199ecb40612afeb4a54ea4b88c95b86 -->
## Gets a medium: Optimized using a preset if it is an image or the original one if not.

> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/media/djr4sd7Gmd/icon.[/1]"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'http://127.0.0.1:8000/media/djr4sd7Gmd/icon.[/1]',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/media/djr4sd7Gmd/icon.[/1]" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```


> Example response (200):

```json
null
```

### HTTP Request
`GET media/{entity_id}/{preset}[/{friendly}]`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `entity_id` |  required  | The id of the entity of type medium to get.
    `preset` |  required  | A preset configured in config/media.php to process the image.

<!-- END_8199ecb40612afeb4a54ea4b88c95b86 -->

#User


<!-- START_57e3b4272508c324659e49ba5758c70f -->
## Authenticate a user.

> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/user/login"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "laudantium",
    "password": "dignissimos"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'http://127.0.0.1:8000/api/user/login',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
        'json' => [
            'email' => 'laudantium',
            'password' => 'dignissimos',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X POST \
    "http://127.0.0.1:8000/api/user/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email":"laudantium","password":"dignissimos"}'

```


> Example response (200):

```json
{
    "token": "JDJ5JDEwJEcwRlFrQmxEM04uQnNXMTNjWE5wME9QYncuZ2ZnUGZlQzJ3SUpsZFhIMUl6MXZ0TVprb2RD",
    "user": {
        "id": "8M1KRk1kLe",
        "email": "admin@example.com",
        "name": "Administrator",
        "profile": "admin"
    }
}
```

### HTTP Request
`POST api/user/login`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `email` | string |  required  | 
        `password` | string |  required  | 
    
<!-- END_57e3b4272508c324659e49ba5758c70f -->

#Web


<!-- START_e910fbe49f78f1e787a9e4fd647a09a2 -->
## Locates an entity based on the url, and returns the HTML view of that entity as a webpage

> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'http://127.0.0.1:8000/1',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```


> Example response (200):

```json
null
```

### HTTP Request
`GET {path:.*}`


<!-- END_e910fbe49f78f1e787a9e4fd647a09a2 -->

#general


<!-- START_0b975e87a66d92fbc5e8df29cdaf518b -->
## Returns the current logged user

> Example request:

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/user/me"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'http://127.0.0.1:8000/api/user/me',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/api/user/me" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```


> Example response (401):

```json
{
    "error": "Unauthorized"
}
```

### HTTP Request
`GET api/user/me`


<!-- END_0b975e87a66d92fbc5e8df29cdaf518b -->


