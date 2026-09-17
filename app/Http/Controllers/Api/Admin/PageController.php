<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->paginate(15);

        return response()->json($pages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $page = Page::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'is_published' => $request->is_published ?? true,
        ]);

        return response()->json(['message' => 'Tạo trang tĩnh thành công', 'data' => $page], 201);
    }

    public function show($id)
    {
        $page = Page::findOrFail($id);

        return response()->json($page);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $page = Page::findOrFail($id);

        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'is_published' => $request->is_published ?? $page->is_published,
        ]);

        return response()->json(['message' => 'Cập nhật trang tĩnh thành công', 'data' => $page]);
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return response()->json(['message' => 'Xóa trang tĩnh thành công']);
    }
}
