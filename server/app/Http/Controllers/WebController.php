<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

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
     * Locates an entity based on the url, and returns the HTML view of that entity as a webpage
     *
     * @group Web
     * @param $request \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function any(Request $request)
    {
        $path = $request->path() == '/' ? '/' : '/' . $request->path();
        $format = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($format === '') {
            $format = 'html';
        } else {
            $path = substr($path, 0, strrpos($path, "."));
        }
        $path = preg_replace('/\/index$/', '', $path);
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        // Search for the entity is being called by its url, ignore inactive and soft deleted.
        // TODO: Is there a better way using Laravel Query builder or native
        /* $langs = config('cms.langs', ['en']);
        $searchResult = Entity::select("id", "model")
            ->orWhere("properties->url", $url);
        foreach ($langs as $searchLang) {
            $searchResult->orWhere("properties->url->$searchLang", $url);
        }
        $searchResult = $searchResult->first();
        */
        $searchResult = Route::where('path', $path)->first();
        if (!$searchResult) {
            $request->lang = config('cms.langs', ['en_US'])[0];
            $controllerClassName = "App\\Http\\Controllers\\HtmlController";
            $controller = new $controllerClassName;
            return ($controller->error($request, 404));
        }
        // Select an entity with its properties
        $lang = $searchResult->lang;
        App::setLocale($lang);
        $entity = Entity::select("*")
            ->where("id", $searchResult->entity_id)
            ->appendProperties($searchResult->entity_model)
            ->appendContents($searchResult->entity_model, $lang)
            ->with('entitiesRelated')
            ->with('routes');
        $entity=$entity->first();
        if (!$entity->isPublished()) {
            $controllerClassName = "App\\Http\\Controllers\\HtmlController";
            $controller = new $controllerClassName;
            return ($controller->error($request, 404));
        }
        $request->request->add(['lang' => $lang]);
        $model_name = $entity->model;
        $controllerClassName = "App\\Http\\Controllers\\" . ucfirst($format) . 'Controller';
        $controller = new $controllerClassName;
        if (method_exists($controller, $model_name)) {
            return ($controller->$model_name($request, $entity, $lang));
        } else {
            return ($controller->error($request, 501));
        }
    }
}
