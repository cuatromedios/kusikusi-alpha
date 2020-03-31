<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;

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
        $entity = Entity::where("content->en->url", $url)->first();
        if (!$entity) {
            return ('404 not found');
        }
        $request->request->add(['lang' => 'en']);
        if (!$entity->isPublished()) {
            return ('Temporary not available');
        }
        $model_name = $entity->model;
        $controllerClassName = "App\\Http\\Controllers\\Web\\" . ucfirst($format) . 'Controller';
        $controller = new $controllerClassName;
        if (method_exists($controller, $model_name)) {
            return ($controller->$model_name($request, $entity, $format));
        } else {
            //TODO: Send 404 or what the controller sends as 404 (for example a json error)
            //TODO: Hide sensitive data if not in debug mode?
            return ("Error: method '" . $method_name . "' not found in app/Controllers/Web/" . $controllerClassName);
        }
    }
}
