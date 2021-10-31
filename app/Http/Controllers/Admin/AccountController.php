<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\System;
use App\Helpers\Authorize;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Profile\ProfileUpdated;
use App\Events\Profile\PasswordUpdated;
use App\Http\Requests\Validations\UpdatePhotoRequest;
use App\Http\Requests\Validations\DeletePhotoRequest;
use App\Http\Requests\Validations\UpdateProfileRequest;
use App\Http\Requests\Validations\UpdatePasswordRequest;

class AccountController extends Controller
{

    /**
     * Show the profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $profile =  Auth::user();

        return view('admin.account.index', compact('profile'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function showChangePasswordForm()
    {
        return view('admin.account._change_password');
    }

    /**
     * Update profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request)
    {
        if( config('app.demo') == true && Auth::user()->id <= config('system.demo.users', 3) ) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        if( ! $request->user()->update($request->all()) ) {
            return back()->with('error', trans('messages.failed'));
        }

        event(new ProfileUpdated(Auth::user()));

        return redirect()->route('admin.account.profile')->with('success', trans('messages.profile_updated'));
    }

    /**
     * Update login password only.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        if( config('app.demo') == true && Auth::user()->id <= config('system.demo.users', 3) ) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        $user = $request->user()->fill([
                        'password' => $request->input('password')
                    ]);

        if( ! $user->save() ) {
            return back()->with('error', trans('messages.failed'));
        }

        event(new PasswordUpdated(Auth::user()));

        return redirect()->route('admin.account.profile')->with('success', trans('messages.password_updated'));
    }

    /**
     * Update Photo only.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePhoto(UpdatePhotoRequest $request)
    {
        if ($request->hasFile('image')) {
            $request->user()->saveImage($request->file('image'));
        }

        return redirect()->route('admin.account.profile')->with('success', trans('messages.profile_updated'));
    }

    /**
     * Remove photo from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deletePhoto(DeletePhotoRequest $request)
    {
        $request->user()->deleteImage();

        return redirect()->route('admin.account.profile')->with('success', trans('messages.profile_updated'));
    }
}