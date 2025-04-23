<x-app-layout>
    <x-slot name="pageTitle">
        {{$pageTitle}}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($category) ? 'Edit Category' : 'Create Category' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

                <form method="POST"
                      action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}">
                    @csrf
                    @if(isset($category))
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="name">
                            Name
                        </label>
                        <input id="name" name="name" type="text" value="{{ old('name',$category->name??'') }}"
                               class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600"
                               required>
                        @error('name')
                        <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="parent_id">
                            Parent Category</label>
                        <select name="parent_id"
                                class="select2 mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            <option value="">-- None --</option>
                            @foreach($categoryOptions as $id => $label)
                                <option
                                    value="{{ $id }}"
                                    {{ old('parent_id', $category->parent_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="description">
                            Description
                        </label>
                        <textarea name="description"
                                  class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600"></textarea>
                        @error('description')
                        <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('categories.index') }}"
                           class="text-sm text-gray-600 hover:underline dark:text-gray-300 mr-4">Cancel</a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-green-700 focus:outline-none focus:ring focus:ring-green-200 active:bg-green-800 transition">
                            <i class="fa fa-plus mr-2"></i> {{ isset($category) ? 'Update' : 'Create' }} Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
