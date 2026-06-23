<?php

namespace App\Contracts\Services;

interface MenuServiceInterface
{
    // Items
    public function getItems(array $filters = []);
    public function createItem(array $data);
    public function updateItem(int $id, array $data);
    public function deleteItem(int $id);

    // Sections
    public function getSections();
    public function createSection(array $data);
    public function updateSection(int $id, array $data);
    public function deleteSection(int $id);

    // Categories
    public function getCategories();
    public function createCategory(array $data);
    public function updateCategory(int $id, array $data);
    public function deleteCategory(int $id);

    // Subcategories
    public function getSubcategories();
    public function createSubcategory(array $data);
    public function updateSubcategory(int $id, array $data);
    public function deleteSubcategory(int $id);

    // Tags
    public function getTags();
    public function createTag(array $data);
    public function deleteTag(int $id);
}
