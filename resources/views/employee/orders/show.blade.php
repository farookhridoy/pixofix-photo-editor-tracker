<x-app-layout>
    <x-slot name="pageTitle">
        {{$pageTitle}}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Order: {{ $order->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('employee-orders.index') }}"
                   class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    <i class="fa fa-list mr-2"></i> Orders
                </a>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                @php
                    $unclaimed = $order->files->where('status', 'unclaimed')->sortBy('filename');
                    $claimed = $order->files->where('status', 'claimed');
                @endphp
                <form action="{{ route('employee-orders.claim.batch', $order->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-4 gap-6">
                        @foreach ($unclaimed->merge($claimed) as $file)
                            <div class="border rounded-xl shadow-sm overflow-hidden" id="file-{{$file->id}}">
                                <img src="{{ asset('/storage/'.$file->filepath) }}"
                                     alt="Image"
                                >

                                <div class="p-4">
                                    @if ($file->status === 'claimed' && $file->claimed_by === auth()->id())
                                        <span class="text-green-600 font-semibold">Claimed</span>
                                    @elseif ($file->status === 'unclaimed')
                                        <label class="inline-flex items-center space-x-2">
                                            <input type="checkbox" name="file_id[]" value="{{ $file->id }}"
                                                   class="form-checkbox claim-checkbox text-blue-500">
                                            <span class="text-sm text-white">Select</span>
                                        </label>
                                    @else
                                        <span class="text-gray-500 italic text-sm">Unavailable</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div
                        class="sticky bottom-0 bg-white dark:bg-gray-800 py-4 border-t border-gray-200 dark:border-gray-700 -mx-6 px-6 mt-6">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">

                            </span>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center gap-2"
                                    :class="{ 'opacity-50 cursor-not-allowed': selectedFiles.length === 0 }"
                                    :disabled="selectedFiles.length === 0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                Claim Selected
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let orderId = '{{$order->id}}'
        document.querySelectorAll('.claim-checkbox').forEach(cb => {
            cb.addEventListener('change', function () {
                if (this.checked) {
                    fetch(`/employee-orders/${orderId}/lock-file`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({file_id: this.value})
                    })
                        .then(res => {
                            if (!res.ok) {
                                this.checked = false;
                                notify('File is locked by another user', 'error');
                            }
                        })
                        .catch(() => {
                            this.checked = false;
                            notify('Something went wrong', 'error');
                        });
                }
            });
        });
    </script>
</x-app-layout>

