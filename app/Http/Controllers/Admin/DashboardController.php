<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Request;
use Carbon\Carbon;
use App\User;
use App\System;
use App\Dashboard;
use App\Common\Authorizable;
use Codedge\Updater\UpdaterManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\SecretLoginRequest;

class DashboardController extends Controller
{
    use Authorizable;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    /**
     * Display Dashboard of the logged in users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UpdaterManager $updater)
    {
        return view('admin.dashboard.platform');

        if(
            Auth::user()->isSuperAdmin() &&
            config('system_settings.last_update_check') == Null ||
            Carbon::parse(config('system_settings.last_update_check'))->addDays(config('self-update.check_update_interval_days')) < Carbon::now()
        ){
            try{
                session(['new_version' => $updater->isNewVersionAvailable(System::VERSION)]);

                setLastUpdateCheckAt(); // Update the last update check field in the system table
            }
            catch(\Exception $e) {
                \Log::info('Update failed: ' . $e->getMessage());
                \Log::error($e);
            }
        }

        return view('admin.dashboard.platform');
    }

    /**
     * Display the secret_login.
     *
     * @return \Illuminate\Http\Response
     */
    public function secretLogin(SecretLoginRequest $request, $id)
    {
        session(['impersonated' => $id, 'secretUrl' => \URL::previous()]);

        return redirect()->route('admin.admin.dashboard')->with('success', trans('messages.secret_logged_in'));
    }

    /**
     * Display the secret_login.
     *
     * @return \Illuminate\Http\Response
     */
    public function secretLogout()
    {
        $secret_url = Request::session()->get('secretUrl');

        Request::session()->forget('impersonated', 'secretUrl');

        return $secret_url ?
            redirect()->to($secret_url)->with('success', trans('messages.secret_logged_out')) :
            redirect()->route('admin.admin.dashboard');
    }

    /**
     * Toggle Configuration of the current user, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  str  $node
     * @return \Illuminate\Http\Response
     */
    public function toggleConfig(Request $request, $node)
    {
        $config = Dashboard::findOrFail(Auth::user()->id);

        $config->$node = !$config->$node;

        if($config->save()){
            return response("success", 200);
        }

        return response('error', 405);
    }
}
