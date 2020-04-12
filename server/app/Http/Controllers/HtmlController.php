<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HtmlController extends Controller
{

    private function common(Request $request, Entity $currentEntity) {
        $result = [
            "lang" => $request->lang,
            "entity" => $currentEntity,
            "media" => Entity::mediaOf($currentEntity->id)->get(),
            "ancestors" => Entity::select('id', 'model')
                ->ancestorOf($currentEntity->id)
                ->descendantOf('root')
                ->orderBy('ancestor_relation_depth', 'desc')
                ->flatContents($request->lang, ['title'])
                ->with(['route' => function($q) use ($request) {$q->where('lang', $request->lang);} ])
                ->get()
        ];
        return $result;
    }
    private function children(Request $request, Entity $entity) {
        $children = Entity::select('id', 'model')
            ->childOf($entity->id)
            ->flatProperties(['title'])
            ->flatContents($request->lang, ['title'])
            ->with(['route' => function($q) use ($request) {$q->where('lang', $request->lang);} ])
            ->with(['medium' => function ($q) { $q->select('id','model','properties->format as format')->whereJsonContains('tags', 'icon'); }])
            ->orderBy('position')
            ->orderBy('title')
            ->get();
        return $children;
    }

    public function home(Request $request, Entity $entity)
    {
        $result = $this->common($request, $entity);
        $result['children'] = $this->children($request, $entity);
        return view('html.'.$entity->view, $result);
    }

    public function section(Request $request, Entity $entity)
    {
        $result = $this->common($request, $entity);
        $result['children'] = $this->children($request, $entity);
        return view('html.'.$entity->view, $result);
    }

    public function page(Request $request, Entity $entity, $lang)
    {
        $result = $this->common($request, $entity);
        return view('html.'.$entity->view, $result);
    }
    public function product(Request $request, Entity $entity, $lang)
    {
        $result = $this->common($request, $entity);
        return view('html.'.$entity->view, $result);
    }
    public function error(Request $request, $status)
    {
        $result = [ "status" => $status ];
        $result['lang'] = $request->lang;
        return view('html.error', $result);
    }
}
