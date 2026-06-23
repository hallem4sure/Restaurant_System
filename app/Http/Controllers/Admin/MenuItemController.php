<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\MenuSubcategory;
use App\Models\Tag;
use App\Contracts\Services\MenuServiceInterface;
use App\Http\Requests\Menu\StoreMenuItemRequest;
use App\Http\Requests\Menu\UpdateMenuItemRequest;

class MenuItemController extends Controller
{
    protected $menuService;

    public function __construct(MenuServiceInterface $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        $items = $this->menuService->getItems();
        return view('admin.menu.items.index', compact('items'));
    }

    public function create()
    {
        $categories = MenuCategory::where('is_active', true)->get();
        $subcategories = MenuSubcategory::where('is_active', true)->get();
        $tags = Tag::all();
        return view('admin.menu.items.create', compact('categories', 'subcategories', 'tags'));
    }

    public function store(StoreMenuItemRequest $request)
    {
        $this->menuService->createItem($request->validated());
        return redirect()->route('admin.menu-items.index')->with('success', 'Menu Item created successfully.');
    }

    public function show(MenuItem $menuItem)
    {
        $menuItem->load(['category', 'subcategory', 'tags', 'images']);
        return view('admin.menu.items.show', compact('menuItem'));
    }

    public function edit(MenuItem $menuItem)
    {
        $categories = MenuCategory::where('is_active', true)->get();
        $subcategories = MenuSubcategory::where('is_active', true)->get();
        $tags = Tag::all();
        $menuItem->load(['tags', 'images']);
        return view('admin.menu.items.edit', compact('menuItem', 'categories', 'subcategories', 'tags'));
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem)
    {
        $this->menuService->updateItem($menuItem->id, $request->validated());
        return redirect()->route('admin.menu-items.index')->with('success', 'Menu Item updated successfully.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $this->menuService->deleteItem($menuItem->id);
        return redirect()->route('admin.menu-items.index')->with('success', 'Menu Item deleted successfully.');
    }
}
