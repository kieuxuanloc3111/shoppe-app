<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->get();
        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'parent_id'       => 'nullable|exists:categories,id',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $data['commission_rate'] = $data['commission_rate'] ?? 0;

        Category::create($data); // slug tự sinh trong model

        return redirect()->route('admin.category.index');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view('admin.category.edit', [
            'category'   => $category,
            // loại chính nó khỏi danh sách cha (không tự làm cha mình)
            'categories' => Category::where('id', '<>', $id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'parent_id'       => 'nullable|exists:categories,id',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $data['commission_rate'] = $data['commission_rate'] ?? 0;

        Category::findOrFail($id)->update($data);

        return redirect()->route('admin.category.index');
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return redirect()->route('admin.category.index');
    }
}
