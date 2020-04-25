<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;

class CmsController extends Controller
{

    /**
     * Get the CMS configuration.
     *
     * Returns an object with the configuration for the CMS frontend.
     *
     * @group Cms
     * @return \Illuminate\Http\JsonResponse
     */
    public function showConfig(Request $request)
    {
        return Config::get('cms', ["error" => "Configuration could not be loaded"]);
    }
}
