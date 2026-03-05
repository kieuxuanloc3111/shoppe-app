<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // chưa login
        if (!Auth::check()) {
            return response()->json([
                'status' => 'not_login'
            ]);
        }

        $request->validate([
            'blog_id' => 'required',
            'content' => 'required'
        ]);

        $user = Auth::user();

        $comment = Comment::create([
            'blog_id'     => $request->blog_id,
            'user_id'     => $user->id,
            'parent_id'   => $request->parent_id,
            'level'       => $request->parent_id ? 1 : 0,
            'content'     => $request->content,
            'user_name'   => $user->name,
            'user_avatar' => $user->avatar ,
        ]);

        return response()->json([
            'status' => 'success',
            'html'   => view('frontend.blog._single_comment', compact('comment'))->render()
        ]);
    }
}
