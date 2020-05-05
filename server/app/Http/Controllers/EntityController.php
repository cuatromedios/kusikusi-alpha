<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\EntityRelation;
use App\Models\Medium;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class EntityController extends Controller
{
    const ID_RULE = 'string|min:1|max:16|regex:/^[A-Za-z0-9_-]+$/';
    const ID_RULE_WITH_FILTER = 'string|min:1|max:40|regex:/^[A-Za-z0-9_-]+:?[a-z0-9]+$/';
    const MODEL_RULE = 'string|min:1|max:32|regex:/^[a-z0-9-]+$/';
    const TIMEZONED_DATE = 'nullable|date_format:Y-m-d\TH:i:sP|after_or_equal:1000-01-01T00:00:00-12:00|before_or_equal:9999-12-31T23:59:59-12:00';
    private $calledRelations = [];
    private $addedSelects = [];
    /**
     * Get a collection of  entities.
     *
     * Returns a paginated collection of entities, filtered by all set conditions.
     *
     * @group Entity
     * @authenticated
     * @queryParam select A comma separated list of fields of the entity to include. It is possible to flat the properties json column using a dot syntax. Example: id,model,properties.price
     * @queryParam order-by A comma separated lis of fields to order by. Example: model,properties.price:desc,contents.title
     * @queryParam of-model (filter) The name of the model the entities should be. Example: page
     * @queryParam only-published (filter) Get only published, not deleted entities, true if not set. Example: true
     * @queryParam child-of (filter) The id or short id of the entity the result entities should be child of. Example: home
     * @queryParam parent-of (filter) The id or short id of the entity the result entities should be parent of (will return only one). Example: 8fguTpt5SB
     * @queryParam ancestor-of (filter) The id or short id of the entity the result entities should be ancestor of. Example: enKSUfUcZN
     * @queryParam descendant-of (filter) The id or short id of the entity the result entities should be descendant of. Example: xAaqz2RPyf
     * @queryParam siblings-of (filter) The id or short id of the entity the result entities should be siblings of. Example: _tuKwVy8Aa
     * @queryParam related-by (filter) The id or short id of the entity the result entities should have been called by using a relation. Can be added a filter to a kind of relation for example: theShortId:category. The ancestor kind of relations are discarted unless are explicity specified. Example: ElFYpgEvWS
     * @queryParam relating (filter) The id or short id of the entity the result entities should have been a caller of using a relation. Can be added a filder to a kind o relation for example: shortFotoId:medium to know the entities has caller that medium. The ancestor kind of relations are discarted unless are explicity specified. Example: enKSUfUcZN
     * @queryParam media-of (filter) The id or short id of the entity the result entities should have a media relation to. Example: enKSUfUcZN
     * @queryParam with A comma separated list of relationships should be included in the result. Example: media,contents,entities_related, entities_related.contents (nested relations)
     * @queryParam per-page The amount of entities per page the result should be the amount of entities on a single page. Example: 6
     * @urlParam model_name If a model name is provided, the results will have the corresponding scope and special defined relations and accesosrs will be available.
     * @responseFile responses/entities.index.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $model_name = null)
    {
        $this->validate($request, $this->queryParamsValidation());
        if ($model_name) {
            $modelClassName = "App\\Models\\".Str::studly(Str::singular($model_name));
            $entities = $modelClassName::query();
        } else {
            $modelClassName = "App\\Models\\Entity";
            $entities = Entity::query();
        }
        $lang = $request->get('lang') ?? Config::get('cms.langs')[0] ?? '';
        // Add selects
        $entities = $this->addSelects($entities, $request, $lang, $modelClassName);
        // Add relations
        $entities = $this->addRelations($entities, $request);
        // Orders by
        $entities = $entities->when($request->get('order-by'), function ($q) use ($request) {
            $orders = explode(",", $request->get('order-by'));
            foreach ($orders as $order) {
                $parts = explode(":", last(explode(".", $order)));
                if (isset($parts[1])) {
                    return $q->orderBy($parts[0], $parts[1] === 'desc' ? $parts[1] : 'asc');
                } else {
                    return $q->orderBy($parts[0]);
                }
            }
        });
        // Filters
        $entities = $entities->when($request->get('of-model'), function ($q) use ($request) {
                return $q->ofModel($request->get('of-model'));
            })
            ->when($request->exists('only-published') && ($request->get('only-published') === 'true' || $request->get('only-published') === ''), function ($q) use ($request) {
                return $q->isPublished();
            })
            ->when($request->get('child-of'), function ($q) use ($request) {
                return $q->childOf($request->get('child-of'));
            })
            ->when($request->get('parent-of'), function ($q) use ($request) {
                return $q->parentOf($request->get('parent-of'));
            })
            ->when($request->get('ancestor-of'), function ($q) use ($request) {
                return $q->ancestorOf($request->get('ancestor-of'));
            })
            ->when($request->get('descendant-of'), function ($q) use ($request) {
                return $q->descendantOf($request->get('descendant-of'));
            })
            ->when($request->get('siblings-of'), function ($q) use ($request) {
                return $q->siblingsOf($request->get('siblings-of'));
            })
            ->when($request->get('related-by'), function ($q) use ($request) {
                $values = explode(":", $request->get('related-by'));
                if (isset($values[1])) {
                    return $q->relatedBy($values[0], $values[1]);
                } else {
                    return $q->relatedBy($values[0]);
                }

            })
            ->when($request->get('relating'), function ($q) use ($request) {
                $values = explode(":", $request->get('relating'));
                if (isset($values[1])) {
                    return $q->relating($values[0], $values[1]);
                } else {
                    return $q->relating($values[0]);
                }

            })
            ->when($request->get('media-of'), function ($q) use ($request) {
                return $q->mediaOf($request->get('media-of'));
            });

        $entities = $entities->paginate($request->get('per-page') ? intval($request->get('per-page')) : Config::get('cms.page_size', 100))
            ->withQueryString();
        return $entities;
    }

    /**
     * Retrieve the entity for the given ID.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_id The id of the entity to show.
     * @queryParam select A comma separated list of fields of the entity to include. It is possible to flat the properties json column using a dot syntax. Example: id,model,properties.price
     * @queryParam with A comma separated list of relationships should be included in the result. Example: media,contents,entities_related, entities_related.contents (nested relations)
     * @responseFile responses/entities.show.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $entity_id)
    {
        $validator = Validator::make(get_defined_vars(),
            ['entity_id' => self::ID_RULE]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $entityFound = Entity::select('id', 'model')
            ->where('id', $entity_id)
            ->firstOrFail();
        $modelClassName = "App\\Models\\".Str::studly(Str::singular($entityFound->model));
        if(!class_exists('$modelClassName')) {
            $modelClassName = "App\\Models\\Entity";
        }
        $entity = $modelClassName::select('id');
        $lang = $request->get('lang') ?? Config::get('cms.langs')[0] ?? '';
        $entity = $this->addSelects($entity, $request, $lang, $modelClassName);
        $entity = $this->addRelations($entity, $request);
        return $entity->findOrFail($entityFound->id);;
    }

    /**
     * Creates a new entity.
     *
     * @group Entity
     * @authenticated
     * @bodyParam model string required The model name. Example: page.
     * @bodyParam view string The name of the view to use. Default: the same name of the model. Example: page
     * @bodyParam published_at date A date time the entity should be published. Default: current date time. Example: 2020-02-02 12:00:00.
     * @bodyParam unpublished_at date A date time the entity should be published. Default: 9999-12-31 23:59:59. Example: 2020-02-02 12:00:00.
     * @bodyParam properties string An object with properties. Example: {"price": 200, "format": "jpg"}
     * @bodyParam id string You can set your own ID, a maximum of 16, safe characters: A-Z, a-z, 0-9, _ and -. Default: autogenerated. Example: home
     * @bodyParam contents array An array of contents to be created for the entity. Example: { "title": {"en_US": "The page M", "es_ES": "La página M"}, "slug": {"en_US": "page-m", "es_ES": "pagina-m"}}
     * @bodyParam relations arrya An array of relations to be created for the entity. Example: "relations": [{"called_entity_id": "mf4gWE45pm","kind": "category","position": 2, "tags":["main"]}]
     * @responseFile responses/entities.create.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'model' => 'required|string|max:32',
            'view' => 'string|max:32',
            'id' => self::ID_RULE,
            'published_at' => self::TIMEZONED_DATE,
            'unpublished_at' => self::TIMEZONED_DATE,
            'is_active' => 'boolean'
        ]);
        $payload = $request->only('id', 'model', 'view', 'parent_entity_id', 'published_at', 'unpublished_at', 'properties', 'contents', 'entities_related', 'is_active');
        $entity = new Entity($payload);
        $entity->save();
        $createdEntity = Entity::with('contents')->find($entity->id);
        return($createdEntity);
    }

    /**
     * Updates an entity.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_id The id of the entity to update
     * @bodyParam view string The name of the view to use. Default: the same name of the model. Example: page
     * @bodyParam published_at date A date time the entity should be published. Default: current date time. Example: 2020-02-02 12:00:00.
     * @bodyParam unpublished_at date A date time the entity should be published. Default: 9999-12-31 23:59:59. Example: 2020-02-02 12:00:00.
     * @bodyParam properties string An object with properties. Example: {"price": 200, "format": "jpg"}
     * @bodyParam id string You can set your own ID, a maximum of 16, safe characters: A-Z, a-z, 0-9, _ and -. Default: autogenerated. Example: home
     * @bodyParam contents array An array of contents to be created for the entity. Example: { "title": {"en_US": "The page M", "es_ES": "La página M"}, "slug": {"en_US": "page-m", "es_ES": "pagina-m"}}
     * @bodyParam relations arrya An array of relations to be created for the entity. Example: "relations": [{"called_entity_id": "mf4gWE45pm","kind": "category","position": 2, "tags":["main"]}]
     * @responseFile responses/entities.update.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $entity_id)
    {
        $this->validate($request, [
            'view' => 'string|max:32',
            'published_at' => self::TIMEZONED_DATE,
            'unpublished_at' => self::TIMEZONED_DATE,
            'is_active' => 'boolean'
        ]);
        $validator = Validator::make(get_defined_vars(),
            ['entity_id' => self::ID_RULE]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $payload = $request->only('id', 'model', 'view', 'parent_entity_id', 'published_at', 'unpublished_at', 'properties', 'contents', 'entities_related', 'is_active');
        $entity = Entity::find($entity_id);
        $entity->fill($payload);
        $entity->save();
        return($entity);
    }

    /**
     * Deletes an entity.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_id The id of the entity to delete
     * @responseFile responses/entities.delete.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $entity_id)
    {
        $validator = Validator::make(get_defined_vars(),
            ['entity_id' => self::ID_RULE]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        Entity::where('id', $entity_id)->delete();
        $entity = Entity::select('id', 'deleted_at')->withTrashed()->find($entity_id);
        $entity->makeVisible('deleted_at');
        return($entity);
    }

    /**
     * Creates or updates a relation.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_caller_id required The id of the entity to create or update a relation
     * @bodyParam entity_called_id string required The id of the entity to relate. Example: s4FG56mkdRT5
     * @bodyParam kind string required The kind of relation to create or update. Example: medium | category
     * @bodyParam tags array An array of tags to add to the relation. Defaults to an empty array. Example ["icon", 'gallery"].
     * @bodyParam position integer The position of the relation. Example: 3.
     * @bodyParam depth integer Yet another number value to use freely for the relation, used in ancestor type of relation to define the distance between an entity and other in the tree. Example 1.
     * @responseFile responses/entities.createRelation.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRelation(Request $request, $caller_entity_id)
    {
        $this->validate($request, [
            'called_entity_id' => 'required|'.self::ID_RULE,
            'kind' => 'string|max:25|regex:/^[a-z0-9]+$/',
            'position' => 'integer',
            'tags.*' => 'string',
            'depth' => 'integer'
        ]);
        $validator = Validator::make(get_defined_vars(),
            [
                'caller_entity_id' => self::ID_RULE
            ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $payload = $request->only( 'called_entity_id', 'kind', 'position', 'depth', 'tags');
        $payload['caller_entity_id'] = $caller_entity_id;
        Entity::createRelation($payload);
        $relation = EntityRelation::where('caller_entity_id', $payload['caller_entity_id'])
            ->where('called_entity_id', $payload['called_entity_id'])
            ->where('kind', $payload['kind'])
            ->firstOrFail()
            ->makeVisible('caller_entity_id', 'called_entity_id', 'created_at', 'updated_at');
        return($relation);
    }

    /**
     * Deletes a relation if exists.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_caller_id required The id of the entity to create or update a relation
     * @urlParam entity_called_id string required The id of the entity to relate. Example: s4FG56mkdRT5
     * @urlParam kind string required The kind of relation to create or update. Example: medium | category
     * @responseFile responses/entities.deleteRelation.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRelation(Request $request, $caller_entity_id, $called_entity_id, $kind)
    {
       $validator = Validator::make(get_defined_vars(),
           [
            'caller_entity_id' => self::ID_RULE,
            'called_entity_id' => self::ID_RULE,
            'kind' => 'required|string|max:25'
            ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $relation = EntityRelation::where('caller_entity_id', $caller_entity_id)
            ->where('called_entity_id', $called_entity_id)
            ->where('kind', $kind)
            ->first();
        if ($relation) {
            $relation_id = $relation ? $relation->relation_id : null;
            EntityRelation::where('relation_id', $relation_id)->delete();
            return(["relation_id" => $relation_id]);
        } else {
            return(JsonResponse::create(["error" => "Relation not found"], 404));
        }
    }

    /**
     * Reorders an array of relations
     *
     * Receive an array of relation ids, and sets the individual position to its index in the array.
     *
     * @group Entity
     * @authenticated
     * @bodyParam relation_ids array required An array of relation ids to reorder. Example ['s4FG56mkdRT5', 'FG56mkdRT5s3', '4FG56mkdRT5d']
     * @responseFile responses/entities.reorderRelations.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorderRelations(Request $request)
    {
        $this->validate($request, [
            'relation_ids' => 'required',
            'relation_ids.*' => self::ID_RULE
        ]);
        for ($r = 0; $r < count($request['relation_ids']); $r++) {
            EntityRelation::where('relation_id', $request['relation_ids'][$r])
                ->update(['position' => ($r + 1)]);
        }
        return(["relations" =>EntityRelation::select('relation_id', 'position')->orderBy('position')->find($request['relation_ids'])]);
    }

    private function queryParamsValidation() {
        return [
            'child-of' => self::ID_RULE,
            'parent-of' => self::ID_RULE,
            'ancestor-of' => self::ID_RULE,
            'descendant-of' => self::ID_RULE,
            'siblings-of' => self::ID_RULE,
            'related-by' => self::ID_RULE_WITH_FILTER,
            'relating' => self::ID_RULE_WITH_FILTER,
            'media-of' => self::ID_RULE,
            'of-model' => self::MODEL_RULE,
            'model_name' => self::MODEL_RULE,
            'only-published' => 'in:true,false',
        ];
    }

    /**
     * Process the request to know for select query parameter and add the corresponding select statments
     *
     * @param $query
     * @param $request
     * @return mixed
     */
    private function addSelects($query, $request, $lang, $modelClassName) {
        // Selects
        $query->when(!$request->exists('select') && !$request->exists('order-by'), function ($q) use ($request) {
            return $q->select('entities.*');
        })
        ->when($request->get('select') || $request->get('order-by'), function ($q) use ($request, $lang, $modelClassName) {
            $selects = explode(',', $request->get('select'));
            $ordersBy = explode(',', $request->get('order-by'));
            foreach (array_merge($selects, $ordersBy) as $select) {
                $select = explode(":", $select)[0];
                if (!in_array($select, $this->addedSelects)) {
                    $appendProperties = [];
                    $appendContents = [];
                    if (Str::startsWith( $select, 'properties.')) {
                        $appendProperties[] = Str::after($select, '.');
                    } else if (Str::startsWith( $select, 'contents.')) {
                        $contentsParts = $this->getParts($select);
                        foreach ($contentsParts['fields'] as $field) {
                            $appendContents[] = $field;
                        }
                    } else if ($select === "route") {
                        $q->appendRoute($lang);
                    } else if ($select === "contents") {
                        $modelInstance =  new $modelClassName();
                        $appendContents = array_merge($appendContents, $modelInstance->getContentFields()) ;
                    } else if (Str::startsWith( $select, 'medium')) {
                        $q->appendMedium(null, null, $lang);
                    } else if ($select) {
                        $q->addSelect($select);
                    }
                    if (count($appendProperties) > 0) {
                        $q->appendProperties($appendProperties);
                    }
                    if (count($appendContents) > 0) {
                        $q->appendContents($appendContents, $lang);
                    }
                    $this->addedSelects[] = $select;
                }
            }
            return $q;
        });
        return $query;
    }

    /**
     * Process the request to know for relations query parameter and add the corresponding select statments
     *
     * @param $query
     * @param $request
     * @return mixed
     */
    private function addRelations($query, $request) {
        // Selects
        $query->when($request->get('with'), function ($q) use ($request) {
                $relations = explode(',', $request->get('with'));
                foreach ($relations as $relation) {
                    $relationParts = $this->getParts($relation);
                    if (!in_array($relation, $this->calledRelations)) {
                        if ($relationParts['relation'] === 'medium') {
                            $q->appendMedium($relationParts['param'], $relationParts['fields'], $request->lang);
                        } else {
                            $q->with($relationParts['relation']);
                        }
                        $this->calledRelations[] = $relationParts['relation'];
                    };
                }
                return $q;
            });
        return $query;
    }

    /**
     * Get the parts of a item in the query
     */
    private function getParts($item) {
        $partsParam = explode(':', $item);
        $partsFields = explode('.', $partsParam[0]);
        $param = $partsParam[1] ?? null;
        $object = array_shift($partsFields);
        return [
          "relation" => $object,
          "fields" => $partsFields,
          "param" => $param
        ];
    }

}
