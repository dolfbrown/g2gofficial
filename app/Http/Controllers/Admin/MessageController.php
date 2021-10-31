<?php

namespace App\Http\Controllers\Admin;

use App\Message;
use Illuminate\Http\Request;
use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use App\Events\Message\NewMessage;
use App\Events\Message\MessageReplied;
use App\Http\Requests\Validations\DraftSendRequest;
use App\Http\Requests\Validations\ReplyMessageRequest;
use App\Http\Requests\Validations\CreateMessageRequest;
use App\Http\Requests\Validations\UpdateMessageRequest;

class MessageController extends Controller
{
    use Authorizable;

    private $model;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = trans('app.model.message');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function labelOf($label = 1)
    {
        $messages = Message::labelOf($label)->with('customer')->withCount('replies')->orderBy('updated_at', 'desc')->paginate(getPaginationValue());

        return view('admin.message.index', compact('messages'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function statusOf($status = 1)
    {
        $messages = Message::statusOf($status)->with('customer')->withCount('replies')->orderBy('updated_at', 'desc')->paginate(getPaginationValue());

        return view('admin.message.index', compact('messages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($template = Null)
    {
        return view('admin.message._create', compact('template'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateMessageRequest $request)
    {
        $message = Message::create($request->all());

        if ($request->hasFile('attachments'))
            $message->saveAttachments($request->file('attachments'));

        if( ! $message )
            return back()->with('error', trans('messages.failed'));

        event(new NewMessage($message));

        return back()->with('success', trans('messages.created', ['model' => $this->model]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Message $message
     * @return \Illuminate\Http\Response
     */
    public function draftSend(DraftSendRequest $request, Message $message)
    {
        if( ! $message->update($request->all()) )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('attachments'))
            $message->saveAttachments($request->file('attachments'));

        if($request->has('draft'))
            return back()->with('success', trans('messages.updated', ['model' => $this->model]));

        return back()->with('success', trans('messages.sent', ['model' => $this->model]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Message $message
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Message $message)
    {
        $this->updateStatusOrLabel($request, $message, Message::STATUS_READ, 'status');

        return view('admin.message.show', compact('message'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Message $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        return view('admin.message._edit', compact('message'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Message $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message, $statusOrLabel, $type = 'label')
    {
        $backLabel = $message->label;

        if( ! $this->updateStatusOrLabel($request, $message, $statusOrLabel, $type) )
            return back()->with('error', trans('messages.failed'));

        return redirect()->route('admin.support.message.labelOf', $backLabel)->with('success', trans('messages.updated', ['model' => $this->model]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param str statusOrLabel
     * @param str type
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request, $statusOrLabel, $type = 'label')
    {
        Message::whereIn('id', $request->ids)->update([$type => $statusOrLabel]);

        return response()->json(['success' => trans('messages.updated', ['model' => $this->model])]);
    }

    /**
     * Display the reply form.
     *
     * @param Message $message
     * @return \Illuminate\Http\Response
     */
    public function reply(Message $message, $template = Null)
    {
        return view('admin.message._reply', compact('message','template'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Message Message
     * @return \Illuminate\Http\Response
     */
    public function storeReply(ReplyMessageRequest $request, Message $message)
    {
        $message->update($request->all());

        $reply = $message->replies()->create($request->all());

        if( ! $reply )
            return back()->with('error', trans('messages.failed'));

        if ($request->hasFile('attachments'))
            $reply->saveAttachments($request->file('attachments'));

        event(new MessageReplied($reply));

        return back()->with('success', trans('messages.updated', ['model' => $this->model]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Message $Message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Message $message)
    {
        $backLabel = $message->label;

        if($message->hasAttachments())
            $message->flushAttachments();

        if($message->hasReplies())
            $message->flushReplies();

        if( ! $message->forceDelete() )
            return redirect()->route('admin.support.message.labelOf', $backLabel)->with('error', trans('messages.failed'));

        return redirect()->route('admin.support.message.labelOf', $backLabel)->with('success', trans('messages.deleted', ['model' => $this->model]));
    }

    public function massDestroy(Request $request)
    {
        foreach ($request->ids as $id)
            $this->destroy($id);

        return response()->json(['success' => trans('messages.deleted', ['model' => $this->model])]);
    }

    private function updateStatusOrLabel(Request $request, Message $message, $statusOrLabel, $type)
    {
        if ($type == 'status')
            $data['status'] = $statusOrLabel;
        else
            $data['label'] = $statusOrLabel;

        return $message->update($data);
    }
}
