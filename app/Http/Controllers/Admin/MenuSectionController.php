<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuSection;
use App\Contracts\Services\MenuServiceInterface;
use App\Http\Requests\Menu\StoreMenuSectionRequest;
use App\Http\Requests\Menu\UpdateMenuSectionRequest;

class MenuSectionController extends Controller
{
    protected $menuService;

    public function __construct(MenuServiceInterface $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        $sections = $this->menuService->getSections();
        return view('admin.menu.sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.menu.sections.create');
    }

    public function store(StoreMenuSectionRequest $request)
    {
        $this->menuService->createSection($request->validated());
        return redirect()->route('admin.menu-sections.index')->with('success', 'Section created successfully.');
    }

    public function show(MenuSection $menuSection)
    {
        return view('admin.menu.sections.show', compact('menuSection'));
    }

    public function edit(MenuSection $menuSection)
    {
        return view('admin.menu.sections.edit', compact('menuSection'));
    }

    public function update(UpdateMenuSectionRequest $request, MenuSection $menuSection)
    {
        $this->menuService->updateSection($menuSection->id, $request->validated());
        return redirect()->route('admin.menu-sections.index')->with('success', 'Section updated successfully.');
    }

    public function destroy(MenuSection $menuSection)
    {
        $this->menuService->deleteSection($menuSection->id);
        return redirect()->route('admin.menu-sections.index')->with('success', 'Section deleted successfully.');
    }
}
