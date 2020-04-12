<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class EntityController extends Controller
{
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
     * @queryParam is-published (filter) Get only published, not deleted entities, true if not set. Example: true
     * @queryParam child-of (filter) The id or short id of the entity the result entities should be child of. Example: home
     * @queryParam parent-of (filter) The id or short id of the entity the result entities should be parent of (will return only one). Example: 801892f7-8dcb-4fdc-a1fd-5251ceb6af09
     * @queryParam ancestor-of (filter) The id or short id of the entity the result entities should be ancestor of. Example: 701892f7-8dcb-4fdc-a1fd-5251ceb6af08
     * @queryParam descendant-of (filter) The id or short id of the entity the result entities should be descendant of. Example: 601892f7-8dcb-4fdc-a1fd-5251ceb6af07
     * @queryParam siblings-of (filter) The id or short id of the entity the result entities should be siblings of. Example: 601892f7-8dcb-4fdc-a1fd-5251ceb6af07
     * @queryParam related-by (filter) The id or short id of the entity the result entities should have been called by using a relation. Can be added a filter to a kind of relation for example: theShortId:category. The ancestor kind of relations are discarted unless are explicity specified. Example: 501892f7-8dcb-4fdc-a1fd-5251ceb6af06
     * @queryParam relating (filter) The id or short id of the entity the result entities should have been a caller of using a relation. Can be added a filder to a kind o relation for example: shortFotoId:medium to know the entities has caller that medium. The ancestor kind of relations are discarted unless are explicity specified. Example: 401892f7-8dcb-4fdc-a1fd-5251ceb6af05
     * @queryParam media-of (filter) The id or short id of the entity the result entities should have a media relation to. Example: 401892f7-8dcb-4fdc-a1fd-5251ceb6af05
     * @queryParam with A comma separated list of relationships should be included in the result. Example: media,entityContents,entitiesRelated:short_id (just that field), entitiesRelated.entityContents (nested relations)
     * @urlParam model_name
     * @responseFile responses/entities.get.json
     * @return Response
     */
    public function index(Request $request, $model_name = null)
    {
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
            ->when(!$request->exists('is-published') || $request->get('is-published') === 'true' || $request->get('is-published') === '', function ($q) use ($request) {
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
     * @urlParam $entity_id string required The id or short id of the entity to be retrieved
     * @return Response
     */
    public function show($entity_id)
    {
        return Entity::findOrFail($entity_id);
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
                    $flatProperties = [];
                    $flatContents = [];
                    if (Str::startsWith( $select, 'properties.')) {
                        $flatProperties[] = Str::after($select, '.');
                    } else if (Str::startsWith( $select, 'contents.')) {
                        $flatContents[] = Str::after($select, '.');
                    } else if ($select === "contents") {
                        $modelInstance =  new $modelClassName();
                        $flatContents = array_merge($flatContents, $modelInstance->getContentFields()) ;
                    } else if ($select) {
                        $q->addSelect($select);
                    }
                    if (count($flatProperties) > 0) {
                        $q->flatProperties($flatProperties);
                    }
                    if (count($flatContents) > 0) {
                        $q->flatContents($lang, $flatContents);
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
                    if (!in_array($relation, $this->calledRelations)) {
                        $q->with($relation);
                        $this->calledRelations[] = $relation;
                    };
                }
                return $q;
            });
        return $query;
    }

}
