<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    use ImageUploadTrait;

    public function index()
    {
        $menus = Menu::all();
        return response()->json($menus);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $this->uploadImage($request, 'image', 'menus');
        }

        $menu = Menu::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image_url' => $imageUrl,
        ]);

        return response()->json([
            'message' => 'Menu created successfully',
            'menu' => $menu
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($menu->image_url) {
                // Remove '/storage/' from the start to get the relative path
                $oldPath = str_replace('/storage/', '', $menu->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $menu->image_url = $this->uploadImage($request, 'image', 'menus');
        }

        if ($request->has('name')) $menu->name = $request->name;
        if ($request->has('description')) $menu->description = $request->description;
        if ($request->has('price')) $menu->price = $request->price;

        $menu->save();

        return response()->json([
            'message' => 'Menu updated successfully',
            'menu' => $menu
        ]);
    }

    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        // Delete associated image
        if ($menu->image_url) {
            $oldPath = str_replace('/storage/', '', $menu->image_url);
            Storage::disk('public')->delete($oldPath);
        }

        $menu->delete();

        return response()->json(['message' => 'Menu deleted successfully']);
    }
}
