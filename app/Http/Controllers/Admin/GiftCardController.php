<?php namespace App\Http\Controllers\Admin;

use App\GiftCard;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
// use App\Repositories\GiftCard\GiftCardRepository;
use App\Http\Requests\Validations\CreateGiftCardRequest;
use App\Http\Requests\Validations\UpdateGiftCardRequest;

class GiftCardController extends Controller
{
    use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.gift_card');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $valid_cards = GiftCard::valid()->with('image:path,imageable_id,imageable_type')->get();

        $invalid_cards = GiftCard::invalid()->get();

        $trashes = GiftCard::onlyTrashed()->get();

        return view('admin.gift-card.index', compact('valid_cards', 'invalid_cards', 'trashes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.gift-card._create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateGiftCardRequest $request)
    {
        $giftCard = GiftCard::create($request->all());

        if( ! $giftCard )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('image'))
            $giftCard->saveImage($request->file('image'));

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  GiftCard $giftCard
     * @return \Illuminate\Http\Response
     */
    public function show(GiftCard $giftCard)
    {
        return view('admin.gift-card._show', compact('giftCard'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  GiftCard $giftCard
     * @return \Illuminate\Http\Response
     */
    public function edit(GiftCard $giftCard)
    {
        return view('admin.gift-card._edit', compact('giftCard'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  GiftCard $giftCard
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGiftCardRequest $request, GiftCard $giftCard)
    {
        if( ! $giftCard->update($request->all()) )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('image') || ($request->input('delete_image') == 1))
            $giftCard->deleteImage();

        if ($request->hasFile('image'))
            $giftCard->saveImage($request->file('image'));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Trash the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  GiftCard $giftCard
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request, GiftCard $giftCard)
    {
        if($giftCard->delete())
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
        $giftCard = GiftCard::onlyTrashed()->findOrFail($id);

        if($giftCard->restore())
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
        $giftCard = GiftCard::onlyTrashed()->findOrFail($id);

        $carrier->flushImages();

        if($giftCard->forceDelete())
            return back()->with('success',  trans('messages.deleted', ['model' => $this->model_name]));

        return back()->with('error', trans('messages.failed'));
    }
}
