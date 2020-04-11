<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class EntityController extends Controller
{
    /**
     * Get a collection of  entities.
     *
     * Returns a paginated collection of entities, filtered by all set conditions.
     *
     * @group Entity
     * @authenticated
     * @queryParam select
     * @queryParam of-model (filter) The name of the model the entities should be. Example: page
     * @queryParam is-published (filter) Get only published, not deleted entities, true if not set. Example: true
     * @queryParam child-of (filter) The id or short id of the entity the result entities should be child of. Example: home
     * @queryParam parent-of (filter) The id or short id of the entity the result entities should be parent of (will return only one). Example: 801892f7-8dcb-4fdc-a1fd-5251ceb6af09
     * @queryParam ancestor-of (filter) The id or short id of the entity the result entities should be ancestor of. Example: 701892f7-8dcb-4fdc-a1fd-5251ceb6af08
     * @queryParam descendant-of (filter) The id or short id of the entity the result entities should be descendant of. Example: 601892f7-8dcb-4fdc-a1fd-5251ceb6af07
     * @queryParam related-by (filter) The id or short id of the entity the result entities should have been called by using a relation. Can be added a filter to a kind of relation for example: theShortId:category. The ancestor kind of relations are discarted unless are explicity specified. Example: 501892f7-8dcb-4fdc-a1fd-5251ceb6af06
     * @queryParam relating (filter) The id or short id of the entity the result entities should have been a caller of using a relation. Can be added a filder to a kind o relation for example: shortFotoId:medium to know the entities has caller that medium. The ancestor kind of relations are discarted unless are explicity specified. Example: 401892f7-8dcb-4fdc-a1fd-5251ceb6af05
     * @queryParam media-of (filter) The id or short id of the entity the result entities should have a media relation to. Example: 401892f7-8dcb-4fdc-a1fd-5251ceb6af05
     * @responseFile responses/entities.get.json
     * @return Response
     */
    public function index(Request $request)
    {
        $entities = Entity::select('*')
            ->when($request->get('of-model'), function ($q) use ($request) {
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
            })

            ->paginate(Config::get('cms.page_size', 100));
        $entities->withQueryString();
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

}
