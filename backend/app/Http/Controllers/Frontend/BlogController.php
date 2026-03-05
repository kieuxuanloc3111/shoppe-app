<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('id', 'desc')->paginate(3);
        return view('frontend.blog.index', compact('blogs'));
    }

    public function detail($id)
    {
        $blog = Blog::findOrFail($id);

        $prevBlog = Blog::where('id', '<', $blog->id)->orderBy('id', 'desc')->first();
        $nextBlog = Blog::where('id', '>', $blog->id)->orderBy('id', 'asc')->first();
        $avgRate = Rate::where('blog_id', $blog->id)->avg('rate');

        $comments = Comment::where('blog_id', $blog->id)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('id', 'desc')
            ->get();

        return view('frontend.blog.detail', compact(
            'blog',
            'prevBlog',
            'nextBlog',
            'comments',
            'avgRate'
        ));

    }

    // rate ajax
    public function rate(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'not_login'
            ]);
        }

        Rate::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'blog_id' => $request->blog_id,
            ],
            [
                'rate' => $request->rate,
            ]
        );

        $avg = Rate::where('blog_id', $request->blog_id)->avg('rate');

        return response()->json([
            'status' => 'success',
            'avg' => round($avg, 1),
        ]);
    }
}
