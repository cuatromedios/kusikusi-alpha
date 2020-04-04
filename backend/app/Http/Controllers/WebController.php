<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use Illuminate\Support\Str;

class WebController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Search an entity based on the url.
     *
     * @param $request \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function any(Request $request)
    {
        $query = ($request->query());
        $url = $request->path() == '/' ? '/' : '/' . $request->path();
        $format = strtolower(pathinfo($url, PATHINFO_EXTENSION));

        if ($format === '') {
            $format = 'html';
        } else {
            $url = substr($url, 0, strrpos($url, "."));
        }
        $url = preg_replace('/\/index$/', '', $url);
        $filename = strtolower(pathinfo($url, PATHINFO_FILENAME));

        // Search for the entity is being called by its url, ignore inactive and soft deleted.
        // TODO: Is there a better way using Laravel Query builder or native
        $langs = config('cms.langs', ['en']);
        $searchResult = Entity::select("id", "model")
            ->orWhere("content->url", $url);
        foreach ($langs as $searchLang) {
            $searchResult->orWhere("content->url->$searchLang", $url);
        }
        $searchResult = $searchResult->first();
        $defaultLang = $langs[0];
        if (!$searchResult || !$searchResult->isPublished()) {
            $controllerClassName = "App\\Http\\Controllers\\Web\\HtmlController";
            $controller = new $controllerClassName;
            return ($controller->error($request, 404));
        }
        // Select an entity with its contents
        $lang = "en";
        $entity = Entity::select("*")
            ->where("id", $searchResult->id)
            ->flatContents($searchResult->model, $lang)
            ->with('entitiesRelated')
            ->first();
        $request->request->add(['lang' => $lang]);
        $model_name = $entity->model;
        $controllerClassName = "App\\Http\\Controllers\\Web\\" . ucfirst($format) . 'Controller';
        $controller = new $controllerClassName;
        if (method_exists($controller, $model_name)) {
            return ($controller->$model_name($request, $entity, $lang));
        } else {
            return ($controller->error($request, 501));
        }
    }
}
