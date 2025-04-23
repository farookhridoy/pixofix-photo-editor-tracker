<x-app-layout>
    <x-slot name="pageTitle">
        {{$pageTitle}}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Show') }}
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
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden relative">
                    <div class="h-full bg-green-500 transition-all duration-300 ease-in-out"
                         style="width: {{ $progress }}%"></div>
                    <span class="absolute inset-0 text-xs text-center text-danger font-semibold leading-4">{{ $progress }}%</span>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead>
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">File Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">File</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->files as $file)
                        <tr class="divide-y divide-gray-200 dark:divide-gray-700">
                            <td class="px-4 text-white py-2 whitespace-nowrap">{{$loop->iteration }}</td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">{{ $file->filename }}</td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">
                                <img height="60" width="60" src="{{ asset('/storage/'.$file->filepath) }}"/>
                            </td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">
                                {{ ucfirst($file->status) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <form action="{{ route('order.file.destroy', $file->id) }}" method="POST"
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800"
                                            onclick="return confirm('Are you sure?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
{{--<script>--}}
{{--    window.Echo.channel('order-progress')--}}
{{--        .listen('FileCompleted', (data) => {--}}
{{--            if (data.order_id === {{ $order->id }}) {--}}
{{--                window.dispatchEvent(new CustomEvent('progress-update', {--}}
{{--                    detail: {progress: data.progress}--}}
{{--                }));--}}
{{--            }--}}
{{--        });--}}
{{--</script>--}}
