<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    /**
     * Retrieve the entities.
     *
     * @param  uuid  $entity_id
     * @return Response
     */
    public function index()
    {
        return Entity::paginate(5);
    }

    /**
     * Retrieve the entity for the given ID.
     *
     * @param  uuid  $entity_id
     * @return Response
     */
    public function show($entity_id)
    {
        return Entity::findOrFail($entity_id);
    }

    /**
     * Retrieve the children entities for the given ID.
     *
     * @param  uuid  $entity_id
     * @return Response
     */
    public function children($entity_id)
    {
        return Entity::childOf($entity_id);
    }
}
