<?php

namespace App\Http\Controllers\Admin;

use App\SystemConfig;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;

class ThemeController extends Controller
{
    use Authorizable;

    /**
     * All themes installed
     *
     * @return [type] [description]
     */
	public function all()
	{
        $storeFrontThemes = collect($this->storeFrontThemes());

        return view('admin.theme.index', compact('storeFrontThemes'));
	}

    /**
     * activate storefront theme
     *
     * @param  Request $request
     * @param  str  $theme   theme slug
     *
     * @return [type]           [description]
     */
    public function activate(Request $request, $theme)
    {
        $system = SystemConfig::orderBy('id', 'asc')->first();

        $this->authorize('update', $system); // Check permission

        $system->active_theme = $theme;

        if($system->save())
            return back()->with('success', trans('messages.theme_activated', ['theme' => $theme]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * StoreFront Themes
     * @return array
     */
    private function storeFrontThemes()
    {
        $storeFrontThemes = [];
        foreach (glob(theme_path('*'), GLOB_ONLYDIR) as $themeFolder) {
            $themeFolder = realpath($themeFolder);
            if (file_exists($jsonFilename = $themeFolder . '/' . 'theme.json')) {

                $folders = explode(DIRECTORY_SEPARATOR, $themeFolder);
                $themeName = end($folders);

                // If theme.json is not an empty file parse json values
                $json = file_get_contents($jsonFilename);
                if ($json !== "") {
                    $data = json_decode($json, true);
                    if ($data === null) {
                        throw new \Exception("Invalid theme.json file at [$themeFolder]");
                    }
                } else {
                    $data = [];
                }

                // We already know views-path since we have scaned folders.
                // we will overide this setting if exists
                $data['assets-path'] = theme_assets_path($data['slug']);
                $data['views-path'] = theme_views_path($data['slug']);

                $storeFrontThemes[] = $data;
            }
        }

        return $storeFrontThemes;
    }

}
