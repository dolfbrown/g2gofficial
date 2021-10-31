<?php
namespace App\Http\Controllers\Admin;

use App\Role;
use App\Common\Authorizable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateRoleRequest;
use App\Http\Requests\Validations\UpdateRoleRequest;

class RoleController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.role');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::lowerPrivileged()->withCount('users')->get();

        $trashes = Role::onlyTrashed()->lowerPrivileged()->withCount('users')->get();

        return view('admin.role.index', compact('roles', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.role._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRoleRequest $request)
    {
        $role = Role::create($request->all());

        if( ! $role )
            return back()->with('error', trans('messages.failed'));

        $role->permissions()->sync($request->input('permissions', []));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role_permissions = $role->permissions()->pluck('module_id', 'slug')->toArray();

        return view('admin.role._show', compact('role', 'role_permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('admin.role._edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        if( config('app.demo') == true && $role->id <= config('system.demo.roles', 3) )
            return back()->with('warning', trans('messages.demo_restriction'));

        if( ! $role->update($request->all()) )
            return back()->with('error', trans('messages.failed'));

        $role->permissions()->sync($request->input('permissions', []));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Role $role)
    {
        if( config('app.demo') == true && $role->id <= config('system.demo.roles', 3) )
            return back()->with('warning', trans('messages.demo_restriction'));

        $role->users()->delete();

        if($role->delete())
            return back()->with('success', trans('messages.trashed', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Restore the specified resource from soft delete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        $role = Role::onlyTrashed()->findOrFail($id);

        if($role->restore()){
            $role->users()->restore();

            return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));
        }

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $role = Role::onlyTrashed()->findOrFail($id);

        $role->users()->forceDelete();

        if($role->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }
}