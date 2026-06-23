<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuSection;
use App\Contracts\Services\MenuServiceInterface;
use App\Http\Requests\Menu\StoreMenuCategoryRequest;
use App\Http\Requests\Menu\UpdateMenuCategoryRequest;

class MenuCategoryController extends Controller
{
    protected $menuService;

    public function __construct(MenuServiceInterface $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        $categories = $this->menuService->getCategories();
        return view('admin.menu.categories.index', compact('categories'));
    }

    public function create()
    {
        $sections = MenuSection::where('is_active', true)->get();
        return view('admin.menu.categories.create', compact('sections'));
    }

    public function store(StoreMenuCategoryRequest $request)
    {
        $this->menuService->createCategory($request->validated());
        return redirect()->route('admin.menu-categories.index')->with('success', 'Category created successfully.');
    }

    public function show(MenuCategory $menuCategory)
    {
        $menuCategory->load('section');
        return view('admin.menu.categories.show', compact('menuCategory'));
    }

    public function edit(MenuCategory $menuCategory)
    {
        $sections = MenuSection::where('is_active', true)->get();
        return view('admin.menu.categories.edit', compact('menuCategory', 'sections'));
    }

    public function update(UpdateMenuCategoryRequest $request, MenuCategory $menuCategory)
    {
        $this->menuService->updateCategory($menuCategory->id, $request->validated());
        return redirect()->route('admin.menu-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(MenuCategory $menuCategory)
    {
        $this->menuService->deleteCategory($menuCategory->id);
        return redirect()->route('admin.menu-categories.index')->with('success', 'Category deleted successfully.');
    }
}
