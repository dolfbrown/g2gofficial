<?php

namespace App\Http\Controllers\Admin;

// use DB;
// use Hash;
use App\System;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Artisan;
// use Symfony\Component\Console\Output\BufferedOutput;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use Codedge\Updater\UpdaterManager;
use App\Http\Requests\Validations\UpdateSystemRequest;


class UpdateController extends Controller
{
    use Authorizable;

    /**
     * Check if a new version is available and pass current version
     *
     * @return [type] [description]
     */
	public function check()
	{
	    return Updater::isNewVersionAvailable(System::VERSION) ? TRUE : FALSE;
	}

	public function update(UpdateSystemRequest $request, UpdaterManager $updater)
	{
        try{

			// echo "<pre>"; print_r($request->all()); echo "</pre>"; exit();
		 //    // This downloads and install the latest version of your repo
		 //    Updater::update();

		    // Just download the source and do the actual update elsewhere
		    $updater->fetch();

	        request()->session()->forget('new_version');
        }
        catch(\Exception $e) {
            \Log::info('Update failed: ' . $e->getMessage());
            \Log::error($e);

	        return redirect()->route('admin.admin.dashboard')->with('error', trans('messages.version_failed'));
        }

        return redirect()->route('admin.admin.dashboard')->with('success', trans('messages.version_updated'));
	}

}
