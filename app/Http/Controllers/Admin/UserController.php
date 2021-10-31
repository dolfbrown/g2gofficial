<?php
namespace App\Http\Controllers\Admin;

use App\User;
use App\Common\Authorizable;
use Illuminate\Http\Request;
use App\Events\User\UserCreated;
use App\Events\User\UserUpdated;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateUserRequest;
use App\Http\Requests\Validations\UpdateUserRequest;

class UserController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('role', 'image', 'primaryAddress')->get();

        $trashes = User::onlyTrashed()->with('image')->get();

        return view('admin.user.index', compact('users', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $user = User::create($request->all());

        if( ! $user )
            return back()->with('error', trans('messages.failed'));

        $user->addresses()->create($request->all());

        if ($request->hasFile('image'))
            $user->saveImage($request->file('image'));

        event(new UserCreated($user, auth()->user()->getName(), $request->get('password')));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('admin.user._show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.user._edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if( config('app.demo') == true && $user->id <= config('system.demo.users', 2) )
            return back()->with('warning', trans('messages.demo_restriction'));

        if( ! $user->update($request->all()) )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('image') || ($request->input('delete_image') == 1))
            $user->deleteImage();

        if ($request->hasFile('image'))
            $user->saveImage($request->file('image'));

        event(new UserUpdated($user));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, User $user)
    {
        if( config('app.demo') == true && $user->id <= config('system.demo.users', 2) )
            return back()->with('warning', trans('messages.demo_restriction'));

        if($user->delete())
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
        $user = User::onlyTrashed()->findOrFail($id);

        if($user->restore())
            return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));

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
        $user = User::onlyTrashed()->findOrFail($id);

        $user->flushAddresses();

        $user->flushImages();

        if($user->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }
}