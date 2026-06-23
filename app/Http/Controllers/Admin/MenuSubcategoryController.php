<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuSubcategory;
use App\Models\MenuCategory;
use App\Contracts\Services\MenuServiceInterface;
use App\Http\Requests\Menu\StoreMenuSubcategoryRequest;
use App\Http\Requests\Menu\UpdateMenuSubcategoryRequest;

class MenuSubcategoryController extends Controller
{
    protected $menuService;

    public function __construct(MenuServiceInterface $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        $subcategories = $this->menuService->getSubcategories();
        return view('admin.menu.subcategories.index', compact('subcategories'));
    }

    public function create()
    {
        $categories = MenuCategory::where('is_active', true)->get();
        return view('admin.menu.subcategories.create', compact('categories'));
    }

    public function store(StoreMenuSubcategoryRequest $request)
    {
        $this->menuService->createSubcategory($request->validated());
        return redirect()->route('admin.menu-subcategories.index')->with('success', 'Subcategory created successfully.');
    }

    public function show(MenuSubcategory $menuSubcategory)
    {
        $menuSubcategory->load('category');
        return view('admin.menu.subcategories.show', compact('menuSubcategory'));
    }

    public function edit(MenuSubcategory $menuSubcategory)
    {
        $categories = MenuCategory::where('is_active', true)->get();
        return view('admin.menu.subcategories.edit', compact('menuSubcategory', 'categories'));
    }

    public function update(UpdateMenuSubcategoryRequest $request, MenuSubcategory $menuSubcategory)
    {
        $this->menuService->updateSubcategory($menuSubcategory->id, $request->validated());
        return redirect()->route('admin.menu-subcategories.index')->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(MenuSubcategory $menuSubcategory)
    {
        $this->menuService->deleteSubcategory($menuSubcategory->id);
        return redirect()->route('admin.menu-subcategories.index')->with('success', 'Subcategory deleted successfully.');
    }
}
