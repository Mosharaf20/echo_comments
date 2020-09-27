<?php

namespace App\Http\Controllers;
use App\Comment;
use App\Events\NewComment;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->comments()->with('user')->latest()->get();

        return response()->json($comments);
    }

    public function store(Request $request, Post $post)
    {
        $comment = $post->comments()->create([
            'body'=>$request['body'],
            'user_id'=> Auth::id(),
        ]);

        $comment = Comment::where('id',$comment->id)->with('user')->first();

        broadcast(new NewComment($comment))->toOthers();

        return $comment->toJson();
    }
}
