<?php

namespace App\Services;

use App\Contracts\Services\MenuServiceInterface;
use App\Models\MenuSection;
use App\Models\MenuCategory;
use App\Models\MenuSubcategory;
use App\Models\MenuItem;
use App\Models\MenuItemImage;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class MenuService implements MenuServiceInterface
{
    public function getItems(array $filters = [])
    {
        return MenuItem::with(['category', 'subcategory', 'tags', 'images'])
            ->when(isset($filters['category_id']), function ($query) use ($filters) {
                return $query->where('category_id', $filters['category_id']);
            })
            ->latest()
            ->paginate(15);
    }

    public function createItem(array $data)
    {
        $itemData = collect($data)->except(['images', 'tags'])->toArray();
        $item = MenuItem::create($itemData);

        if (isset($data['tags'])) {
            $item->tags()->sync($data['tags']);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $imageFile) {
                $this->processAndStoreImage($item, $imageFile);
            }
        }

        return $item;
    }

    public function updateItem(int $id, array $data)
    {
        $item = MenuItem::findOrFail($id);
        $itemData = collect($data)->except(['images', 'tags'])->toArray();
        $item->update($itemData);

        if (isset($data['tags'])) {
            $item->tags()->sync($data['tags']);
        }

        if (isset($data['images']) && is_array($data['images'])) {
            // Option to delete old images or keep them. Let's delete old ones for simplicity unless told otherwise
            foreach ($item->images as $oldImage) {
                Storage::disk('public')->delete($oldImage->image_path);
                $oldImage->delete();
            }

            foreach ($data['images'] as $imageFile) {
                $this->processAndStoreImage($item, $imageFile);
            }
        }

        return $item;
    }

    public function deleteItem(int $id)
    {
        $item = MenuItem::findOrFail($id);
        // Images are kept physically if using soft deletes, but let's assume we keep them.
        $item->delete();
    }

    private function processAndStoreImage(MenuItem $item, $file)
    {
        $filename = Str::random(40) . '.jpg';
        $path = 'menu_items/' . $filename;

        // Resize and optimize image
        $image = Image::read($file);
        $image->scale(width: 800);
        
        Storage::disk('public')->put($path, $image->toJpeg(80)->toString());

        MenuItemImage::create([
            'menu_item_id' => $item->id,
            'image_path' => $path,
            'is_primary' => $item->images()->count() === 0,
        ]);
    }

    // Sections
    public function getSections()
    {
        return MenuSection::latest()->paginate(15);
    }

    public function createSection(array $data)
    {
        return MenuSection::create($data);
    }

    public function updateSection(int $id, array $data)
    {
        $section = MenuSection::findOrFail($id);
        $section->update($data);
        return $section;
    }

    public function deleteSection(int $id)
    {
        $section = MenuSection::findOrFail($id);
        $section->delete();
    }

    // Categories
    public function getCategories()
    {
        return MenuCategory::with('section')->latest()->paginate(15);
    }

    public function createCategory(array $data)
    {
        return MenuCategory::create($data);
    }

    public function updateCategory(int $id, array $data)
    {
        $category = MenuCategory::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function deleteCategory(int $id)
    {
        $category = MenuCategory::findOrFail($id);
        $category->delete();
    }

    // Subcategories
    public function getSubcategories()
    {
        return MenuSubcategory::with('category')->latest()->paginate(15);
    }

    public function createSubcategory(array $data)
    {
        return MenuSubcategory::create($data);
    }

    public function updateSubcategory(int $id, array $data)
    {
        $subcategory = MenuSubcategory::findOrFail($id);
        $subcategory->update($data);
        return $subcategory;
    }

    public function deleteSubcategory(int $id)
    {
        $subcategory = MenuSubcategory::findOrFail($id);
        $subcategory->delete();
    }

    // Tags
    public function getTags()
    {
        return Tag::latest()->paginate(15);
    }

    public function createTag(array $data)
    {
        return Tag::create($data);
    }

    public function deleteTag(int $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();
    }
}
