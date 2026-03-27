<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class BlogController extends Controller
{
    //
    public $successStatus = 200;

    public function index()
    {
        $blogs = Blog::orderBy('created_at','desc')->paginate(5);

        return response()->json([
            'blog' => $blogs
        ], $this->successStatus);
    }
    public function detail($id){
        $blog = Blog::find($id);
        if (!$blog) {
        return response()->json([
            'response' => 'error',
            'message' => 'Blog not found'
        ], 404);
        }

        $prevBlog = Blog::where('id' , '<' , $id) 
                        ->orderBy('id', 'desc')->first();
        $nextBlog = Blog::where('id' , '>' , $id) 
                        ->orderBy('id', 'asc')->first();

        return response()->json([
            'response' => 'success',
            'data' => $blog,
            'prev' => $prevBlog,
            'next' => $nextBlog
        ], $this->successStatus);
    }
    public function getComment($id)
    {
        $comments = Comment::where('blog_id',$id)
                    ->orderBy('created_at','asc')
                    ->get();

        return response()->json([
            'response' => 'success',
            'data' => $comments
        ],200);
    }
    public function storeComment(Request $request, $id)
    {
        $user = Auth::user();

        if(!$user){
            return response()->json([
                'response' => 'error',
                'message' => 'Unauthenticated'
            ],401);
        }

        // xác định level comment
        $level = 0;
        if($request->id_comment != 0){
            $level = 1;
        }

        $comment = Comment::create([
            'blog_id' => $id,
            'user_id' => $request->id_user,
            'parent_id' => $request->id_comment == 0 ? null : $request->id_comment,
            'level' => $level,
            'content' => $request->comment,
            'user_name' => $request->name_user,
            'user_avatar' => $request->image_user
        ]);

        return response()->json([
            'response' => 'success',
            'data' => $comment
        ],200);
    }
    
}
