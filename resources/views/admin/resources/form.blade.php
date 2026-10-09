@extends('layouts.admin', ['title' => ($record->exists ? 'Edit ' : 'Add ').$title])

@section('content')
    @php
        $singular = ['products' => 'product', 'categories' => 'category', 'brands' => 'brand', 'banners' => 'homepage banner'][$resource];
        $summary = [
            'products' => 'Add the details customers need to understand and buy this item.',
            'categories' => 'Give shoppers a clear path to browse your products.',
            'brands' => 'Add a maker and choose where its logo appears in the store.',
            'banners' => 'Add a promotion to the home page. A wide image works best.',
        ][$resource];
    @endphp

    <div class="mb-7">
        <a href="{{ route("admin.$resource.index") }}" class="text-sm font-medium text-[#85464f] underline underline-offset-4">← Back to {{ strtolower($title) }}</a>
        <p class="eyebrow mt-5">{{ $record->exists ? 'Update store content' : 'Add to your store' }}</p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight">{{ $record->exists ? 'Edit ' : 'Add ' }}{{ $singular }}</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-[#82766d]">{{ $summary }} Fields marked <span class="font-semibold text-[#85464f]">*</span> are required.</p>
    </div>

    <form method="POST"
          action="{{ $record->exists ? route("admin.$resource.update", $record) : route("admin.$resource.store") }}"
          enctype="multipart/form-data"
          class="grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_18rem]">
        @csrf
        @if ($record->exists) @method('PUT') @endif

        <div class="space-y-5">
            @if ($resource === 'products')
                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold">Product details</h2>
                    <p class="mt-1 text-sm text-[#82766d]">The name, description and price customers see.</p>
                    <div class="mt-6 space-y-5">
                        <div>
                            <label for="name" class="admin-label">Product name <span class="text-[#a95f68]">*</span></label>
                            <input id="name" name="name" value="{{ old('name', $record->name) }}" required maxlength="255" placeholder="e.g. Daily Hydrating Face Cream" class="form-control">
                            @error('name')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="brand_id" class="admin-label">Brand <span class="font-normal text-[#82766d]">(optional)</span></label>
                            <select id="brand_id" name="brand_id" class="form-control">
                                <option value="">No brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected(old('brand_id', $record->brand_id) == $brand->id)>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            <p class="admin-help">Don’t see the brand? Save this product, then add it under Brands.</p>
                            @error('brand_id')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="description" class="admin-label">Description <span class="font-normal text-[#82766d]">(optional)</span></label>
                            <textarea id="description" name="description" rows="5" placeholder="What is it, and what should customers know about it?" class="form-control">{{ old('description', $record->description) }}</textarea>
                            @error('description')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label for="price" class="admin-label">Selling price (৳) <span class="text-[#a95f68]">*</span></label>
                                <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $record->price) }}" required placeholder="0.00" class="form-control">
                                @error('price')<p class="admin-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="compare_price" class="admin-label">Was price (৳)</label>
                                <input id="compare_price" name="compare_price" type="number" min="0" step="0.01" value="{{ old('compare_price', $record->compare_price) }}" placeholder="Optional" class="form-control">
                                <p class="admin-help">Shown crossed out when higher than the selling price.</p>
                                @error('compare_price')<p class="admin-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="stock" class="admin-label">Stock quantity <span class="text-[#a95f68]">*</span></label>
                                <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $record->stock ?? 0) }}" required class="form-control">
                                @error('stock')<p class="admin-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold">Photo and placement</h2>
                    <p class="mt-1 text-sm text-[#82766d]">Add a clear square product photo, then choose where it belongs.</p>
                    <div class="mt-5">
                        <label for="image" class="admin-label">Product photo</label>
                        <input id="image" name="image" type="file" accept="image/*" class="form-control file:mr-3 file:rounded-full file:border-0 file:bg-[#f8eeec] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#85464f]">
                        <p class="admin-help">Image file, up to 5 MB. Adding a new photo replaces the current one.</p>
                        @if ($record->exists && $record->getFirstMediaUrl('images'))
                            <img src="{{ $record->getFirstMediaUrl('images') }}" alt="Current product photo" class="mt-4 h-28 w-28 rounded-xl border border-[#eae2da] object-cover">
                        @endif
                        @error('image')<p class="admin-error">{{ $message }}</p>@enderror
                    </div>
                    <fieldset class="mt-6">
                        <legend class="admin-label">Categories <span class="font-normal text-[#82766d]">(choose all that apply)</span></legend>
                        @if ($categories->isNotEmpty())
                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach ($categories as $category)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-[#eee7e0] px-3 py-2.5 text-sm hover:bg-[#faf7f2]">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                               @checked(collect(old('categories', $record->exists ? $record->categories->pluck('id')->all() : []))->contains($category->id))
                                               class="rounded border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                                        <span>{{ $category->parent?->name ? $category->parent->name.' / ' : '' }}{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="admin-help">Categories appear in the store navigation. Create one first if this list is empty.</p>
                        @else
                            <div class="rounded-xl bg-[#faf7f2] p-4 text-sm text-[#655a52]">
                                <p>No categories yet. Add a category first so shoppers can find this product.</p>
                                <a href="{{ route('admin.categories.create') }}" class="mt-2 inline-block font-semibold text-[#85464f] underline underline-offset-4">Create a category</a>
                            </div>
                        @endif
                        @error('categories')<p class="admin-error">{{ $message }}</p>@enderror
                        @error('categories.*')<p class="admin-error">{{ $message }}</p>@enderror
                    </fieldset>
                </section>

                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold">Show this product in the store</h2>
                    <p class="mt-1 text-sm text-[#82766d]">You can change these settings later.</p>
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        @foreach (['is_active' => ['label' => 'Visible to customers', 'help' => 'Turn off to hide this product.'], 'is_new' => ['label' => 'Show as a new arrival', 'help' => 'Include it in the New Arrivals section.'], 'is_combo' => ['label' => 'Show in beauty sets', 'help' => 'Use for bundles and combo offers.'], 'is_recommended' => ['label' => 'Recommend on the home page', 'help' => 'Feature it in the Recommended for you section.']] as $field => $option)
                            <label class="flex flex-1 cursor-pointer items-start gap-3 rounded-xl border border-[#eee7e0] p-3">
                                <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $record->$field ?? ($field === 'is_active'))) class="mt-0.5 rounded border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                                <span><span class="block text-sm font-semibold">{{ $option['label'] }}</span><span class="mt-1 block text-xs leading-5 text-[#82766d]">{{ $option['help'] }}</span></span>
                            </label>
                        @endforeach
                    </div>
                </section>
                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold">Match to a concern</h2>
                    <p class="mt-1 text-sm text-[#82766d]">Choose the needs this product is relevant for. It will appear beneath the matching concern on your home page.</p>
                    @if ($concerns->isNotEmpty())
                        <div class="mt-5 grid gap-2 sm:grid-cols-2">
                            @foreach ($concerns as $concern)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-[#eee7e0] px-3 py-2.5 text-sm hover:bg-[#faf7f2]">
                                    <input type="checkbox" name="concerns[]" value="{{ $concern->id }}"
                                           @checked(collect(old('concerns', $record->exists ? $record->concerns->pluck('id')->all() : []))->contains($concern->id))
                                           class="rounded border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                                    <span><span class="font-medium">{{ $concern->name }}</span><span class="ml-2 text-xs text-[#82766d]">{{ ucfirst($concern->type) }}</span></span>
                                </label>
                            @endforeach
                        </div>
                        <p class="admin-help">Choose only concerns that genuinely fit this product.</p>
                    @else
                        <div class="mt-5 rounded-xl bg-[#faf7f2] p-4 text-sm text-[#655a52]">
                            <p>No concerns have been added yet.</p>
                            <a href="{{ route('admin.concerns.create') }}" class="mt-2 inline-block font-semibold text-[#85464f] underline underline-offset-4">Add Acne, Hyperpigmentation or another concern</a>
                        </div>
                    @endif
                    @error('concerns')<p class="admin-error">{{ $message }}</p>@enderror
                    @error('concerns.*')<p class="admin-error">{{ $message }}</p>@enderror
                </section>
            @elseif ($resource === 'categories')
                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold">Category details</h2>
                    <p class="mt-1 text-sm text-[#82766d]">Categories organize your shop menu. For example, make “Moisturizers” a child of “Skincare”.</p>
                    <div class="mt-6 space-y-5">
                        <div>
                            <label for="name" class="admin-label">Category name <span class="text-[#a95f68]">*</span></label>
                            <input id="name" name="name" value="{{ old('name', $record->name) }}" required maxlength="255" placeholder="e.g. Skincare" class="form-control">
                            @error('name')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="parent_id" class="admin-label">Put inside another category <span class="font-normal text-[#82766d]">(optional)</span></label>
                            <select id="parent_id" name="parent_id" class="form-control">
                                <option value="">No parent — show in the main menu</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('parent_id', $record->parent_id) == $category->id)>{{ $category->parent?->name ? $category->parent->name.' / ' : '' }}{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <p class="admin-help">Leave this empty for a main menu category, such as Makeup or Skincare.</p>
                            @error('parent_id')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="sort" class="admin-label">Menu order <span class="text-[#a95f68]">*</span></label>
                            <input id="sort" name="sort" type="number" min="0" value="{{ old('sort', $record->sort ?? 0) }}" required class="form-control">
                            <p class="admin-help">Use a smaller number to show this category earlier. Items with the same number are ordered by name.</p>
                            @error('sort')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>
                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold">Visibility</h2>
                    <label class="mt-4 flex cursor-pointer items-center gap-3 rounded-xl border border-[#eee7e0] p-4">
                        <input type="checkbox" name="is_trending" value="1" @checked(old('is_trending', $record->is_trending ?? false)) class="rounded border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                        <span><span class="block text-sm font-semibold">Feature in Trending Categories</span><span class="mt-1 block text-xs text-[#82766d]">Show this category in the trending section on the home page.</span></span>
                    </label>
                    <label class="mt-4 flex cursor-pointer items-center gap-3 rounded-xl border border-[#eee7e0] p-4">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $record->is_active ?? true)) class="rounded border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                        <span><span class="block text-sm font-semibold">Show this category in the store</span><span class="mt-1 block text-xs text-[#82766d]">Turn off to hide it from customers.</span></span>
                    </label>
                </section>
            @elseif ($resource === 'brands')
                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold">Brand details</h2>
                    <p class="mt-1 text-sm text-[#82766d]">Brand names and logos help customers recognize what they’re shopping for.</p>
                    <div class="mt-6 space-y-5">
                        <div>
                            <label for="name" class="admin-label">Brand name <span class="text-[#a95f68]">*</span></label>
                            <input id="name" name="name" value="{{ old('name', $record->name) }}" required maxlength="255" placeholder="e.g. Your favourite beauty brand" class="form-control">
                            @error('name')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="offer_label" class="admin-label">Offer text <span class="font-normal text-[#82766d]">(optional)</span></label>
                            <input id="offer_label" name="offer_label" value="{{ old('offer_label', $record->offer_label) }}" placeholder="e.g. UP TO 20% OFF" class="form-control">
                            <p class="admin-help">This is a short promotional label shown below the brand.</p>
                            @error('offer_label')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="sort" class="admin-label">Display order <span class="text-[#a95f68]">*</span></label>
                            <input id="sort" name="sort" type="number" min="0" value="{{ old('sort', $record->sort ?? 0) }}" required class="form-control">
                            @error('sort')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>
                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold">Brand logo</h2>
                    <label for="image" class="admin-label mt-4">Choose an image</label>
                    <input id="image" name="image" type="file" accept="image/*" class="form-control file:mr-3 file:rounded-full file:border-0 file:bg-[#f8eeec] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#85464f]">
                    <p class="admin-help">Image file, up to 5 MB. A square image works best. A new upload replaces the current logo.</p>
                    @if ($record->exists && $record->getFirstMediaUrl('tile'))
                        <img src="{{ $record->getFirstMediaUrl('tile') }}" alt="Current brand logo" class="mt-4 h-24 w-24 rounded-xl border border-[#eae2da] object-cover">
                    @endif
                    @error('image')<p class="admin-error">{{ $message }}</p>@enderror
                    <div class="mt-6 space-y-3">
                        @foreach (['is_top' => ['Featured brand', 'Show this brand in the Loved Brands section.'], 'is_handpicked' => ['Editor’s pick', 'Show this brand in the A Few Favourites section.']] as $field => $option)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#eee7e0] p-3">
                                <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $record->$field ?? false)) class="mt-0.5 rounded border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                                <span><span class="block text-sm font-semibold">{{ $option[0] }}</span><span class="mt-1 block text-xs text-[#82766d]">{{ $option[1] }}</span></span>
                            </label>
                        @endforeach
                    </div>
                </section>
            @else
                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold">Promotion details</h2>
                    <p class="mt-1 text-sm text-[#82766d]">Your banner appears on the home page. Use a short title customers can understand.</p>
                    <div class="mt-6 space-y-5">
                        <div>
                            <label for="title" class="admin-label">Banner title <span class="text-[#a95f68]">*</span></label>
                            <input id="title" name="title" value="{{ old('title', $record->title) }}" required maxlength="255" placeholder="e.g. A fresh start for your skin" class="form-control">
                            @error('title')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="link" class="admin-label">Where should the banner go? <span class="font-normal text-[#82766d]">(optional)</span></label>
                            <input id="link" name="link" value="{{ old('link', $record->link) }}" maxlength="255" placeholder="/collections/skincare" class="form-control">
                            <p class="admin-help">Paste an internal path such as /collections/skincare. Leave blank to send visitors to search.</p>
                            @error('link')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="sort" class="admin-label">Display order <span class="text-[#a95f68]">*</span></label>
                            <input id="sort" name="sort" type="number" min="0" value="{{ old('sort', $record->sort ?? 0) }}" required class="form-control">
                            <p class="admin-help">Lower numbers appear first when there is more than one banner.</p>
                            @error('sort')<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>
                <section class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-lg font-semibold">Banner images</h2>
                    <p class="mt-1 text-sm text-[#82766d]">Upload a wide desktop image. A separate mobile image is optional.</p>
                    @foreach (['desktop_image' => ['Desktop image', 'Recommended: wide landscape image'], 'mobile_image' => ['Mobile image', 'Optional: tall image for phone screens']] as $field => $info)
                        @php($collection = str_replace('_image', '', $field))
                        <div class="mt-5">
                            <label for="{{ $field }}" class="admin-label">{{ $info[0] }} @if ($field === 'desktop_image')<span class="text-[#a95f68]">*</span>@else<span class="font-normal text-[#82766d]">(optional)</span>@endif</label>
                            <input id="{{ $field }}" name="{{ $field }}" type="file" accept="image/*" @required($field === 'desktop_image' && ! $record->exists) class="form-control file:mr-3 file:rounded-full file:border-0 file:bg-[#f8eeec] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#85464f]">
                            <p class="admin-help">{{ $info[1] }}. Image file up to 5 MB. A new upload replaces the current image.</p>
                            @if ($record->exists && $record->getFirstMediaUrl($collection))
                                <img src="{{ $record->getFirstMediaUrl($collection) }}" alt="Current {{ strtolower($info[0]) }}" class="mt-4 max-h-32 rounded-xl border border-[#eae2da] object-cover">
                            @endif
                            @error($field)<p class="admin-error">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <label class="mt-6 flex cursor-pointer items-center gap-3 rounded-xl border border-[#eee7e0] p-4">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $record->is_active ?? true)) class="rounded border-[#d9cfc7] text-[#85464f] focus:ring-[#c87983]">
                        <span><span class="block text-sm font-semibold">Show this banner on the home page</span><span class="mt-1 block text-xs text-[#82766d]">Turn off to hide it without deleting it.</span></span>
                    </label>
                </section>
            @endif

            <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-[#eae2da] bg-white p-4 shadow-sm">
                <button type="submit" class="button-primary">{{ $record->exists ? 'Save changes' : 'Save '.$singular }}</button>
                <a href="{{ route("admin.$resource.index") }}" class="button-secondary">Cancel</a>
                <span class="text-xs text-[#82766d]">You can come back and edit this later.</span>
            </div>
        </div>

        <aside class="rounded-2xl border border-[#eae2da] bg-white p-5 shadow-sm">
            <p class="eyebrow">Need a hand?</p>
            <h2 class="mt-2 font-semibold">What happens when I save?</h2>
            <p class="mt-2 text-sm leading-6 text-[#82766d]">
                @if ($resource === 'products')
                    Your product is saved to the catalogue. If “Visible to customers” is checked, shoppers can see it in its selected categories.
                @elseif ($resource === 'categories')
                    The category appears in the store menu when it is visible. Choose a parent to place it inside another category.
                @elseif ($resource === 'brands')
                    The brand can be linked to products. Choose “Featured brand” or “Editor’s pick” to show it on the home page.
                @else
                    The banner appears on the home page when it is visible. Add an image to make the promotion stand out.
                @endif
            </p>
            <div class="mt-5 border-t border-[#eee7e0] pt-4">
                <p class="text-sm font-semibold">Quick tips</p>
                <ul class="mt-2 space-y-2 text-xs leading-5 text-[#82766d]">
                    <li>• Check names and prices before saving.</li>
                    <li>• Clear, bright photos work best.</li>
                    <li>• You can change or hide content later.</li>
                </ul>
            </div>
            @if ($resource === 'products' && $brands->isEmpty())
                <div class="mt-5 rounded-xl bg-[#f8eeec] p-4">
                    <p class="text-sm font-semibold">Adding a brand?</p>
                    <p class="mt-1 text-xs leading-5 text-[#75645d]">You can add it now or continue without a brand.</p>
                    <a href="{{ route('admin.brands.create') }}" class="mt-3 inline-block text-sm font-semibold text-[#85464f] underline underline-offset-4">Add a brand</a>
                </div>
            @endif
        </aside>
    </form>
@endsection
