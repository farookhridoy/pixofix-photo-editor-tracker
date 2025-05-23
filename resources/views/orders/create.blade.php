<x-app-layout>
    <x-slot name="pageTitle">
        {{$pageTitle}}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Order') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('orders.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="category_id">
                            Category</label>
                        <select name="category_id"
                                class="select2 mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            @foreach($categoryOptions as $id => $label)
                                <option
                                    value="{{ $id }}"
                                    {{ old('category_id', $category->parent_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="order_number">
                            Order Number
                        </label>
                        <input id="order_number" name="order_number" type="text" value="{{ old('order_number',$sku) }}"
                               class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600"
                               required readonly>
                        @error('order_number')
                        <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="title">
                            Title
                        </label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}"
                               class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600"
                               required>
                        @error('title')
                        <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
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
                    <div class="mb-4" id="upload-container">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="folder">
                            Choose Images (You can choose multiple images)
                        </label>
                        <input name="folder[]"
                               id="folder"
                               multiple
                               type="file"
                               class="block mt-1 w-full"/>
                        @error('folder')
                        <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-200" for="status">
                            Status</label>
                        <select name="status"
                                class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            @foreach(orderStatus() as $key=> $status)
                                <option
                                    value="{{ $key }}"
                                    {{ old('status', $order->status ?? '') == $key ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <a href="{{ route('orders.index') }}"
                           class="text-sm text-gray-600 hover:underline dark:text-gray-300 mr-4">Cancel</a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-green-700 focus:outline-none focus:ring focus:ring-green-200 active:bg-green-800 transition">
                            <i class="fa fa-plus mr-2"></i> Create Orders
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
