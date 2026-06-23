<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Contracts\Services\MenuServiceInterface;
use App\Http\Requests\Menu\StoreTagRequest;
use App\Http\Requests\Menu\UpdateTagRequest;

class TagController extends Controller
{
    protected $menuService;

    public function __construct(MenuServiceInterface $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        $tags = $this->menuService->getTags();
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(StoreTagRequest $request)
    {
        $this->menuService->createTag($request->validated());
        return redirect()->route('admin.tags.index')->with('success', 'Tag created successfully.');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());
        return redirect()->route('admin.tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag)
    {
        $this->menuService->deleteTag($tag->id);
        return redirect()->route('admin.tags.index')->with('success', 'Tag deleted successfully.');
    }
}
