<?php

namespace App\Http\Controllers\Web;

use App\Models\Entity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HtmlController extends Controller
{

    private function common(Request $request, Entity $currentEntity) {
        $result = [
            "entity" => $currentEntity,
            "media" =>
                Entity::mediaOf($currentEntity->id)
                    ->get(),
            "ancestors" =>
                Entity::ancestorOf($currentEntity->id)
                    ->get()
        ];
        return $result;
    }
    private function children(Request $request, Entity $entity) {
        $children = Entity::childOf($entity->id)
            ->withContents(['title', 'summary', 'url'], $request->lang)
            ->with(['media' => EntityModel::onlyTags('icon')])
            ->get()->compact();
        return $children;
    }

    public function home(Request $request, Entity $entity)
    {
        $result = $this->common($request, $entity);
        $result['children'] = $this->children($request, $entity);
        return view('html.home', $result);
    }

    public function section(Request $request, Entity $entity)
    {
        $result = $this->common($request, $entity);
        $result['children'] = $this->children($request, $entity);
        return view('html.section', $result);
    }

    public function page(Request $request, Entity $entity, $lang)
    {
        $result = $this->common($request, $entity);
        return view('html.'.$entity->view, $result);
    }
}
