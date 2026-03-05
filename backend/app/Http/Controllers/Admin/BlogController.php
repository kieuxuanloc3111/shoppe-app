<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('id', 'desc')->get();
        return view('admin.blog.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        Blog::create([
            'title'       => $request->title,
            'image'       => $request->image,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.blog.index');
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return view('admin.blog.edit', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $blog->update([
            'title'       => $request->title,
            'image'       => $request->image,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.blog.index');
    }

    public function destroy($id)
    {
        Blog::destroy($id);
        return redirect()->route('admin.blog.index');
    }
}
