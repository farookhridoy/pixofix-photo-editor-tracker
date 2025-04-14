<x-app-layout>
    <x-slot name="pageTitle">
        {{$pageTitle}}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Orders Management') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('orders.create') }}"
                   class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    <i class="fa fa-plus mr-2"></i> Create Order
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 overflow-x-auto">


            </div>
        </div>
    </div>
</x-app-layout>
