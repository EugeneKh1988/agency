<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Messages;
use Illuminate\Validation\Rule;

class MessagesController extends Controller
{
    // get messages list
    public function index(Request $request): JsonResponse {
        $request->validate([
            'count' => ['nullable','numeric','min:1','max:20'],
            'skip' => ['nullable','numeric','min:0'],
            'create_sort' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'answered' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ]);

        // set pagination values
        $count = 10;
        $skip = 0;
        if($request->has('skip')) {
            $skip = $request->skip;
        }
        if($request->has('count')) {
            $count = $request->count;
        }

        // set user_id value
        $user_id = null;
        $user = $request->user();
        if($user && $user->id) {
            $user_id = $user->id;
        }

        // set sort by creating data value
        $create_sort = null;
        if($request->has('create_sort')) {
            $create_sort = $request->create_sort;
        }

        // set sort by answer value
        $answered = null;
        if($request->has('answered')) {
            $answered = $request->answered;
        }

        $res_query = null;

        // filter by user
        $res_query = Messages::when($user_id > 1, function (Builder $query) {
            $query->where('user_id', $user_id);
        });

        // sort by creating date
        if($create_sort == 'asc') {
            $res_query = $res_query->oldest();
        }
        if($create_sort == 'desc') {
            $res_query = $res_query->latest();
        }

        // sort by answer status
        if($answered == 'desc') {
            $res_query = $res_query->orderBy('was_answered', 'desc');
        }
        if($answered == 'asc') {
            $res_query = $res_query->orderBy('was_answered', 'asc');
        }

        $res_query = $res_query->skip($skip)->take($count);
        $values = $res_query->get();

        return response()->json(['messages' => $values, 'count' => $res_query->count()]);
    }

    // create new message
    public function store(Request $request): JsonResponse {
        $request->validate([
            'name' => ['required','min:3','string','max:255'],
            'email' => ['required', 'email','string','max:255'],
            'phone' => ['nullable', 'string','max:255'],
            'question' => ['required','min:6','string','max:255'],
        ]);

        $message = new Messages();

        $this->createOrEditMessage($message, $request);

        return response()->json(['status' => 'The message was created']);
    }

    // edit new message
    public function edit(Request $request, int $id): JsonResponse {
        $request->validate([
            'name' => ['nullable','min:3','string','max:255'],
            'email' => ['nullable', 'email','string','max:255'],
            'phone' => ['nullable', 'string','max:255'],
            'question' => ['nullable','min:6','string','max:255'],
            'answer' => ['nullable', 'string'],
            'was_answered' => ['nullable', 'boolean'],
        ]);

        $message_id = 0;
        if($id) {
            $message_id = intval($id);
        }
        // find message by id
        $message = Messages::findOr($message_id, function () {
            return null;
        });
        
        if(!$message) {
            return response()->json(['id' => 'Not found'], 422);
        }

        $this->createOrEditMessage($message, $request);

        return response()->json(['status' => 'The message was edited']);
    }

    // delete new message
    public function delete(Request $request, int $id): JsonResponse {
        
        $message_id = 0;
        if($id) {
            $message_id = intval($id);
        }
        // find message by id
        $message = Messages::findOr($message_id, function () {
            return null;
        });
        
        if(!$message) {
            return response()->json(['id' => 'Not found'], 422);
        }

        $message->delete();

        return response()->json(['status' => 'The message was deleted']);
    }

    private function createOrEditMessage(Messages $message, Request $request) {
        $user = $request->user();

        if($request->has('name')) {
            $message->name = $request->name;
        }
        if($request->has('email')) {
            $message->email = $request->email;
        }
        if($request->has('question')) {
            $message->question = $request->question;
        }
        // set user_id if it wasn't settled
        if($user && $user->id && !$message->user_id) {
            $message->user_id = $user->id;
        }
        if($request->has('phone')) {
            $message->phone = $request->phone;
        }
        // if admin
        if($user && $user->id == 1) {
            if($request->has('was_answered')) {
                $message->was_answered = $request->was_answered;
            }
            elseif(!$message->was_answered) {
                $message->was_answered = false;
            }
            if($request->has('answer')) {
                $message->answer = $request->answer;
            }
        }
        elseif(!$message->was_answered) {
                $message->was_answered = false;
        }
        
        if($message->isDirty()) {
            $message->save();
        }
    }
}
