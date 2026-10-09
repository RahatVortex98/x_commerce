<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concern;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConcernController extends Controller
{
    public function index(): View
    {
        return view('admin.resources.index', [
            'resource' => 'concerns',
            'title' => 'Shop by concern',
            'items' => Concern::withCount('products')->orderBy('type')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Concern());
    }

    public function store(Request $request): RedirectResponse
    {
        Concern::create($this->validated($request));

        return redirect()->route('admin.concerns.index')->with('ok', 'Concern created.');
    }

    public function edit(Concern $concern): View
    {
        return $this->form($concern);
    }

    public function update(Request $request, Concern $concern): RedirectResponse
    {
        $concern->update($this->validated($request, $concern));

        return redirect()->route('admin.concerns.index')->with('ok', 'Concern updated.');
    }

    public function destroy(Concern $concern): RedirectResponse
    {
        $concern->delete();

        return redirect()->route('admin.concerns.index')->with('ok', 'Concern deleted.');
    }

    private function form(Concern $concern): View
    {
        return view('admin.concerns.form', compact('concern'));
    }

    private function validated(Request $request, ?Concern $concern = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('concerns', 'name')->ignore($concern?->id),
            ],
            'type' => ['required', Rule::in(['face', 'skin', 'hair'])],
            'sort' => ['required', 'integer', 'min:0'],
        ]);
    }
}
