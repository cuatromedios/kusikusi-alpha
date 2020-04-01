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
        $searchResult = Entity::select("id", "model");
        foreach ($langs as $lang) {
            $searchResult = $searchResult->orWhere("content->".$lang."->url", $url)->first();
        }
        if (!$searchResult) {
            return view('html.404', $request, ["lang" => $lang]);
        }
        if (!$searchResult->isPublished()) {
            return view('html.404', $request, ["lang" => $lang]);
        }
        $entity = Entity::select("*")->where("id", $searchResult->id);
        foreach (config("cms.models.$searchResult->model.content") as $configModelKey => $configModelValue) {
            if (Str::startsWith($configModelKey, "lang.")) {
                $field = Str::replaceFirst("lang.", "", $configModelKey);
                $entity = $entity->addSelect("content->".$lang."->".$field." as $field");
            } else {
                $entity = $entity->addSelect("content->$configModelKey as $configModelKey");
            }
        }
        $entity = $entity->first();
        $request->request->add(['lang' => $lang]);
        $model_name = $entity->model;
        $controllerClassName = "App\\Http\\Controllers\\Web\\" . ucfirst($format) . 'Controller';
        $controller = new $controllerClassName;
        if (method_exists($controller, $model_name)) {
            return ($controller->$model_name($request, $entity, $lang));
        } else {
            return view('html.501', $request, ["lang" => $lang]);
        }
    }
}
