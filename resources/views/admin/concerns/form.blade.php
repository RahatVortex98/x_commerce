@extends('layouts.admin', ['title' => ($concern->exists ? 'Edit' : 'Add').' shop-by concern'])

@section('content')
    <div class="mb-7">
        <a href="{{ route('admin.concerns.index') }}" class="text-sm font-medium text-[#85464f] underline underline-offset-4">← Back to shop-by concerns</a>
        <p class="eyebrow mt-5">Product discovery</p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight">{{ $concern->exists ? 'Edit' : 'Add' }} a concern</h1>
        <p class="mt-2 text-sm text-[#82766d]">Examples include Acne, Hyperpigmentation and Sun Protection. You can match products to each concern while editing a product.</p>
    </div>

    <form method="POST" action="{{ $concern->exists ? route('admin.concerns.update', $concern) : route('admin.concerns.store') }}"
          class="max-w-2xl space-y-5 rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($concern->exists) @method('PUT') @endif

        <div>
            <label for="name" class="admin-label">Concern name <span class="text-[#a95f68]">*</span></label>
            <input id="name" name="name" value="{{ old('name', $concern->name) }}" required class="form-control" placeholder="e.g. Hyperpigmentation">
            @error('name')<p class="admin-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="type" class="admin-label">Show under <span class="text-[#a95f68]">*</span></label>
            <select id="type" name="type" required class="form-control">
                @foreach (['face' => 'Face', 'skin' => 'Skin', 'hair' => 'Hair'] as $type => $label)
                    <option value="{{ $type }}" @selected(old('type', $concern->type ?? 'face') === $type)>{{ $label }}</option>
                @endforeach
            </select>
            @error('type')<p class="admin-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="sort" class="admin-label">Display order <span class="text-[#a95f68]">*</span></label>
            <input id="sort" name="sort" type="number" min="0" value="{{ old('sort', $concern->sort ?? 0) }}" required class="form-control">
            @error('sort')<p class="admin-error">{{ $message }}</p>@enderror
        </div>
        <p class="rounded-xl bg-[#f8eeec] p-4 text-sm leading-6 text-[#655a52]">
            After saving, edit products and check this concern to recommend those products in the matching section on your home page.
        </p>
        <div class="flex flex-wrap gap-3 border-t border-[#eee7e0] pt-5">
            <button type="submit" class="button-primary">{{ $concern->exists ? 'Save changes' : 'Save concern' }}</button>
            <a href="{{ route('admin.concerns.index') }}" class="button-secondary">Cancel</a>
        </div>
    </form>
@endsection
