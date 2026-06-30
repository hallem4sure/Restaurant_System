<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Contracts\Services\TableServiceInterface;
use App\Http\Requests\Table\StoreTableRequest;
use App\Http\Requests\Table\UpdateTableRequest;

class TableController extends Controller
{
    protected $tableService;

    public function __construct(TableServiceInterface $tableService)
    {
        $this->tableService = $tableService;
    }

    public function index()
    {
        $this->authorize('viewAny', RestaurantTable::class);
        $tables = $this->tableService->getAllTables();
        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        $this->authorize('create', RestaurantTable::class);
        return view('admin.tables.create');
    }

    public function store(StoreTableRequest $request)
    {
        $this->authorize('create', RestaurantTable::class);
        $this->tableService->createTable($request->validated());
        return redirect()->route('admin.tables.index')->with('success', 'Table created successfully.');
    }

    public function show(RestaurantTable $table)
    {
        $this->authorize('view', $table);
        return view('admin.tables.show', compact('table'));
    }

    public function edit(RestaurantTable $table)
    {
        $this->authorize('update', $table);
        return view('admin.tables.edit', compact('table'));
    }

    public function update(UpdateTableRequest $request, RestaurantTable $table)
    {
        $this->authorize('update', $table);
        $this->tableService->updateTable($table->id, $request->validated());
        return redirect()->route('admin.tables.index')->with('success', 'Table updated successfully.');
    }

    public function destroy(RestaurantTable $table)
    {
        $this->authorize('delete', $table);
        $this->tableService->deleteTable($table->id);
        return redirect()->route('admin.tables.index')->with('success', 'Table deleted successfully.');
    }
}
