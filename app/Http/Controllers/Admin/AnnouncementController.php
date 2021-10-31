<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Announcement;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Events\Announcement\AnnouncementCreated;
use App\Http\Requests\Validations\CreateAnnouncementRequest;
use App\Http\Requests\Validations\UpdateAnnouncementRequest;

class AnnouncementController extends Controller
{

    private $model_name;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.announcement');
    }

    /**
     * Get all of the application's recent announcements.
     *
     * @return Response
     */
    public function index()
    {
        $announcements = Announcement::with('creator')->orderBy('created_at', 'desc')->take(8)->get();

        return view('admin.announcement.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.announcement._create');
    }

    /**
     * Create a new announcement.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(CreateAnnouncementRequest $request)
    {
        $announcement = Announcement::create([
                                'id' => Uuid::uuid4(),
                                'user_id' => $request->user()->id,
                                'body' => $request->input('body'),
                                'action_text' => $request->input('action_text'),
                                'action_url' => $request->input('action_url'),
                            ]);

        if( ! $announcement ) {
            return back()->with('error', trans('messages.failed'));
        }

        event(new AnnouncementCreated($announcement));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcement._edit', compact('announcement'));
    }

    /**
     * Update the given announcement.
     *
     * @param  Request  $request
     * @param  Announcement  $announcement
     * @return Response
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement)
    {
        $announcement->fill([
            'body' => $request->input('body'),
            'action_text' => $request->input('action_text'),
            'action_url' => $request->input('action_url'),
        ]);

        if( ! $announcement->save() ) {
            return back()->with('error', trans('messages.failed'));
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Delete the announcement with the given ID.
     *
     * @param  string  $id
     * @return Response
     */
    public function destroy($id)
    {
        Announcement::findOrFail($id)->delete();

        return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));
    }

    /**
     * Update announcement read timestamp.
     */
    public function read()
    {
        User::findOrFail(Auth::id())->forceFill(['read_announcements_at' => \Carbon\Carbon::now()])->save();

        return response("success", 200);
    }
}
