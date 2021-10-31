<?php namespace App\Http\Controllers\Admin;

use App\Blog;
use App\Common\Authorizable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Validations\CreateBlogRequest;
use App\Http\Requests\Validations\UpdateBlogRequest;

class BlogController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.blog');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = Blog::with('author','image')->orderBy('created_at', 'desc')->withCount('comments')->get();

        $trashes = Blog::with('image')->orderBy('created_at', 'desc')->onlyTrashed()->get();

        return view('admin.blog.index', compact('blogs', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.blog._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBlogRequest $request)
    {
        $blog = Blog::create($request->all());

        if( ! $blog ) {
            return back()->with('error', trans('messages.failed'));
        }

        if ($request->hasFile('image')) {
            $blog->saveImage($request->file('image'));
        }

        if ($request->input('tag_list')) {
            $blog->syncTags($blog, $request->input('tag_list'));
        }

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        return view('admin.blog._edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $blog->syncTags($blog, $request->input('tag_list', []));

        if ($request->hasFile('image') || ($request->input('delete_image') == 1))
            $blog->deleteImage();

        if ($request->hasFile('image'))
            $blog->saveImage($request->file('image'));

        if( $blog->update($request->all()) )
            return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, Blog $blog)
    {
        if ($blog->delete())
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
        if(Blog::onlyTrashed()->findOrFail($id)->delete())
            return back()->with('success', trans('messages.restored', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $blog = Blog::onlyTrashed()->findOrFail($id);

        $blog->detachTags($id, 'blog');

        $blog->flushImages();

        if($blog->forceDelete())
            return back()->with('success', trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }
}
