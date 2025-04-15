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
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead>
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Order No</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Title</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Stats</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">No.Of Files</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Created By</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($orders as $order)
                        <tr>
                            <td class="px-4 text-white py-2 whitespace-nowrap">{{ ($orders->currentPage() - 1) *
                            $orders->perPage() + $loop->iteration }}</td>
                            <td class="px-4 text-white  py-2 whitespace-nowrap">{{ $order->order_number }}</td>
                            <td class="px-4 text-white  py-2 whitespace-nowrap">{{ $order->title }}</td>
                            <td class="px-4 text-white  py-2 whitespace-nowrap">{{ ucfirst($order->status) }}</td>
                            <td class="px-4 text-white  py-2 whitespace-nowrap">{{ $order->files? $order->files->count():0 }}</td>
                            <td class="px-4 text-white  py-2 whitespace-nowrap">{{ $order->admin?$oder->admin->name:'' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <a href="{{ route('orders.edit', $order) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-2" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="{{ route('orders.file.upload', $order) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-2" title="Edit">
                                    <i class="fa fa-cloud-upload"></i>
                                </a>
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline-block"
                                      onsubmit="return confirm('Are you sure to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if($orders->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center px-4 py-6 text-gray-500">No orders found.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
