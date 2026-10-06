<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CategoryType;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::query()->whereNull('user_id');

        if ($type = $request->input('type')) {
            if (in_array($type, array_column(CategoryType::cases(), 'value'), true)) {
                $query->where('type', $type);
            }
        }

        if ($q = trim((string) $request->input('q'))) {
            $query->where('name', 'like', "%{$q}%");
        }

        $categories = $query
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', [
            'categories' => $categories,
            'filters'    => ['type' => $request->input('type'), 'q' => $request->input('q')],
            'types'      => CategoryType::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category([
                'type'      => CategoryType::Expense->value,
                'is_system' => true,
            ]),
            'types' => CategoryType::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $data['user_id']   = null;
        $data['is_system'] = true;

        $category = Category::create($data);

        ActivityLogger::log('admin.category.created', $category, 'Created system category ' . $category->name);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'System category created.');
    }

    public function edit(Category $category): View
    {
        abort_if($category->user_id !== null, 404);

        return view('admin.categories.edit', [
            'category' => $category,
            'types'    => CategoryType::cases(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        abort_if($category->user_id !== null, 404);

        $data = $this->validateData($request, $category);

        $category->update($data);

        ActivityLogger::log('admin.category.updated', $category, 'Updated system category ' . $category->name);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'System category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        abort_if($category->user_id !== null, 404);

        // Safety: refuse to delete if in use
        if ($category->income()->exists() || $category->expenses()->exists()) {
            return back()->with('error', 'Cannot delete: this category is used by existing records.');
        }

        $name = $category->name;
        $category->delete();

        ActivityLogger::log('admin.category.deleted', null, "Deleted system category {$name}");

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'System category deleted.');
    }

    private function validateData(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name'  => [
                'required', 'string', 'max:100',
                Rule::unique('categories')->where(function ($q) use ($request, $category) {
                    $q->whereNull('user_id')
                      ->where('type', $request->input('type'));
                    if ($category) {
                        $q->where('id', '!=', $category->id);
                    }
                }),
            ],
            'type'  => ['required', Rule::in(array_column(CategoryType::cases(), 'value'))],
            'icon'  => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ], [
            'name.unique' => 'A system category with this name and type already exists.',
        ]);
    }
}