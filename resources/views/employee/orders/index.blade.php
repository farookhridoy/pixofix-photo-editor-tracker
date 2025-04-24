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
                            <td class="px-4 text-white  py-2 whitespace-nowrap">{{ $order->admin?$order->admin->name:'' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <a href="{{ route('employee-orders.show', $order) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-2" title="Show Orders">
                                    <i class="fa fa-eye"></i>
                                </a>

                                <a href="{{ route('employee-orders.my.batch', $order) }}"
                                   class="text-green-600 hover:text-green-900 mr-2" title="My Orders">
                                    <i class="fa fa-list"></i>
                                </a>
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
