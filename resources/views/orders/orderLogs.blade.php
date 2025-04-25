<x-app-layout>
    <x-slot name="pageTitle">
        {{$pageTitle}}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Logs') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('orders.index') }}"
                   class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    <i class="fa fa-list mr-2"></i> Orders
                </a>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead>
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Order</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">File</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Action</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">User</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Notes</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($fieLogs as $log)
                        <tr class="divide-y divide-gray-200 dark:divide-gray-700">
                            <td class="px-4 text-white py-2 whitespace-nowrap">{{$loop->iteration }}</td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">{{ optional($log->file->order)->order_number ?? '' }}</td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">
                                @if(isset($log->file->filepath))
                                    <img height="60" width="60" src="{{ asset('/storage/'.$log->file->filepath) }}"/>
                                @endif
                            </td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">
                                {{ ucfirst($log->action) }}
                            </td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">
                                {{  optional($log->user)->name }}
                            </td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">{{ $log->notes }}</td>
                        </tr>
                    @endforeach @if($fieLogs->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center px-4 py-6 text-gray-500">No logs found.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {!! $fieLogs->links() !!}
            </div>
        </div>
    </div>
</x-app-layout>

