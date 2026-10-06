<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Category::class);

        $userId = $request->user()->id;

        $query = Category::query()
            ->availableTo($userId)
            ->withCount(['income', 'expenses']);

        // Filter by type
        if ($type = $request->input('type')) {
            if (in_array($type, array_column(CategoryType::cases(), 'value'), true)) {
                $query->where('type', $type);
            }
        }

        // Filter: all / mine / system
        if ($scope = $request->input('scope')) {
            match ($scope) {
                'mine'   => $query->where('user_id', $userId),
                'system' => $query->whereNull('user_id'),
                default  => null,
            };
        }

        // Search by name
        if ($search = trim((string) $request->input('q'))) {
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query
            ->orderByRaw('user_id IS NULL ASC')   // custom first
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('categories.index', [
            'categories' => $categories,
            'filters'    => [
                'type'  => $request->input('type'),
                'scope' => $request->input('scope'),
                'q'     => $request->input('q'),
            ],
            'types' => CategoryType::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Category::class);

        return view('categories.create', [
            'category' => new Category(['type' => CategoryType::Expense->value]),
            'types'    => CategoryType::cases(),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $data = $request->validated();
        $data['user_id']   = $request->user()->id;
        $data['is_system'] = false;

        Category::create($data);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('categories.edit', [
            'category' => $category,
            'types'    => CategoryType::cases(),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}