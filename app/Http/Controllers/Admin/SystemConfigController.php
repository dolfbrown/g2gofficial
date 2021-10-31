<?php

namespace App\Http\Controllers\Admin;

use Hash;
use App\SystemConfig;
use App\PaymentMethod;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\System\SystemConfigUpdated;
use App\Http\Requests\Validations\UpdateSystemConfigRequest;

class SystemConfigController extends Controller
{
    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.settings');
    }

   /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view()
    {
        $system = SystemConfig::orderBy('id', 'asc')->first();

        $this->authorize('view', $system); // Check permission

        return view('admin.system.config', compact('system'));
    }

    public function update(UpdateSystemConfigRequest $request)
    {
        if( config('app.demo') == true ) {
            return response('error', 444);
        }

        $system = SystemConfig::orderBy('id', 'asc')->first();

        $this->authorize('update', $system); // Check permission

        if($system->update($request->all())){
            event(new SystemConfigUpdated($system));

            return response("success", 200);
        }

        return response('error', 405);
    }

    public function editManualPaymentInstructions(Request $request, $code)
    {
        $paymentMethod = PaymentMethod::where('code', $code)->firstOrFail();

        return view('admin.system.manual_payment_instructions', compact('paymentMethod'));
    }

    public function updateManualPaymentInstructions(UpdateSystemConfigRequest $request, $code)
    {
        if( config('app.demo') == true ) {
            return redirect()->to(url('admin/setting/system/config').'#payment_method_tab')->with('warning', trans('messages.demo_restriction'));
        }

        $system = SystemConfig::orderBy('id', 'asc')->first();

        $this->authorize('update', $system); // Check permission

        $additional_details = $code.'_additional_details';
        $payment_instructions = $code.'_payment_instructions';

        $system->$additional_details = $request->input('additional_details');
        $system->$payment_instructions = $request->input('payment_instructions');

        if($system->save()){
            event(new SystemConfigUpdated($system));

            return redirect()->to(url('admin/setting/system/config').'#payment_method_tab')->with('success', trans('messages.updated', ['model' => $this->model_name]));
        }

        return redirect()->to(url('admin/setting/system/config').'#payment_method_tab')->with('error', trans('messages.failed'));
    }

    public function payment_methods(UpdateSystemConfigRequest $request)
    {
        return view('admin.system.payment_methods');
    }

    /**
     * Show the BMessengerConfigFile file editor.
     *
     * @return \Illuminate\Http\Response
     */
    public function modifyFBMessengerConfigFile(UpdateSystemConfigRequest $request)
    {
        $file_content = file_get_contents(base_path('.fb_messenger'));

        return view('admin.system.modify_fb_messenger_config_file', compact('file_content'));
    }

    /**
     * Reset the database and import demo data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveFBMessengetConfigFile(UpdateSystemConfigRequest $request)
    {
        if( config('app.demo') == true ) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        try {

            file_put_contents(base_path('.fb_messenger'), $request->file_content);

        } catch(\Exception $e){

            \Log::error('.fb_messenger modification failed: ' . $e->getMessage());

            // add your error messages:
            $error = new \Illuminate\Support\MessageBag();
            $error->add('errors', trans('responses.failed'));

            return back()->withErrors($error);
        }

        $system = SystemConfig::orderBy('id', 'asc')->first();

        event(new SystemConfigUpdated($system));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));

    }

    /**
     * Toggle payment method of the given id, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function togglePaymentMethod(UpdateSystemConfigRequest $request, $id)
    {
        if( config('app.demo') == true ) {
            return response('error', 444);
        }

        $system = SystemConfig::orderBy('id', 'asc')->first();

        $this->authorize('update', $system);    // Check permission

        $paymentMethod = PaymentMethod::findOrFail($id);

        $paymentMethod->enabled = !$paymentMethod->enabled;

        if($paymentMethod->save()){
            event(new SystemConfigUpdated($system));

            return response("success", 200);
        }

        return response('error', 405);
    }

    /**
     * Toggle notification of the given node, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  str  $node
     * @return \Illuminate\Http\Response
     */
    public function toggleNotification(UpdateSystemConfigRequest $request, $node)
    {
        if( config('app.demo') == true ) {
            return response('error', 444);
        }

        $system = SystemConfig::orderBy('id', 'asc')->first();

        $this->authorize('update', $system); // Check permission

        $system->$node = !$system->$node;

        if($system->save()){
            event(new SystemConfigUpdated($system));

            return response("success", 200);
        }

        return response('error', 405);
    }
}
