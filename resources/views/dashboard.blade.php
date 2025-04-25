<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <x-slot name="pageTitle">
        {{$pageTitle}}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-white dark:bg-gray-800 p-4 border rounded shadow">
                            <h3 class="text-lg font-semibold">Total Orders</h3>
                            <p class="text-3xl font-bold">{{ $totalOrders }}</p>
                        </div>

                        @foreach (['unclaimed', 'claimed', 'completed'] as $status)
                            <div class="bg-white dark:bg-gray-800 p-4 border rounded shadow">
                                <h3 class="text-lg font-semibold capitalize">{{ $status }} Files</h3>
                                <p class="text-3xl font-bold">{{ $fileStats[$status] ?? 0 }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-4 border rounded shadow mb-8">
                        @if (auth()->user()->hasRole('Admin'))
                            <h3 class="text-lg font-semibold mb-2">Employee Stats</h3>
                        @else
                            <h3 class="text-lg font-semibold mb-2">My Stats</h3>
                        @endif
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead>
                                <tr class="text-left border-b">
                                    <th class="px-4 py-2">Name</th>
                                    <th class="px-4 py-2">Claimed</th>
                                    <th class="px-4 py-2">In Progress</th>
                                    <th class="px-4 py-2">Completed</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($employeeStats as $employee)
                                    <tr class="border-b">
                                        <td class="px-4 py-2">{{ $employee['name'] }}</td>
                                        <td class="px-4 py-2">{{ $employee['claimed_count'] }}</td>
                                        <td class="px-4 py-2">{{ $employee['in_progress_count'] }}</td>
                                        <td class="px-4 py-2">{{ $employee['completed_count'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if (auth()->user()->hasRole('Admin'))
                        <div class="bg-white p-4 rounded shadow">
                            <h3 class="text-lg font-semibold mb-4 text-black">Files per Employee</h3>
                            <canvas id="employeeChart" height="120"></canvas>
                        </div>
                    @endif

                    @if (auth()->user()->hasRole('Admin'))
                        <div class="mt-10">
                            <h2 class="text-xl font-bold mb-4">Order Status Charts</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($orders as $order)
                                    @php
                                        $statusCounts = $order->files->groupBy('status')->map->count();
                                    @endphp
                                    <div class="bg-white rounded shadow p-4">
                                        <h3 class="text-md font-semibold mb-2 text-black">Order
                                                                                          #{{ $order->order_number }}</h3>
                                        <canvas id="orderChart{{ $order->id }}" height="200"></canvas>
                                        <script>
                                            const ctx{{ $order->id }} = document.getElementById('orderChart{{ $order->id }}').getContext('2d');
                                            new Chart(ctx{{ $order->id }}, {
                                                type: 'doughnut',
                                                data: {
                                                    labels: ['Unclaimed', 'Claimed', 'In Progress', 'Completed'],
                                                    datasets: [{
                                                        label: 'Status',
                                                        data: [
                                                            {{ $statusCounts['unclaimed'] ?? 0 }},
                                                            {{ $statusCounts['claimed'] ?? 0 }},
                                                            {{ $statusCounts['in_progress'] ?? 0 }},
                                                            {{ $statusCounts['completed'] ?? 0 }},
                                                        ],
                                                        backgroundColor: [
                                                            '#f59e0b', '#3b82f6', '#ec4899', '#10b981'
                                                        ]
                                                    }]
                                                },
                                                options: {
                                                    responsive: true,
                                                    plugins: {
                                                        legend: {
                                                            position: 'bottom'
                                                        }
                                                    }
                                                }
                                            });
                                        </script>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @section('javascript')
        <script>
            const ctx = document.getElementById('employeeChart').getContext('2d');

            const employeeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($employeeStats->pluck('name')) !!},
                    datasets: [
                        {
                            label: 'Claimed',
                            backgroundColor: '#3b82f6',
                            data: {!! json_encode($employeeStats->pluck('claimed_count')) !!}
                        },
                        {
                            label: 'Completed',
                            backgroundColor: '#10b981',
                            data: {!! json_encode($employeeStats->pluck('completed_count')) !!}
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                }
            });
        </script>

    @endsection
</x-app-layout>
