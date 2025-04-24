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
                <a href="{{ route('employee-orders.index') }}"
                   class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    <i class="fa fa-list mr-2"></i> Orders
                </a>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
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
                    @foreach($claimedFiles as $file)
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
                                <a
                                    class="text-indigo-600 hover:text-indigo-900 mr-2 editBtn" data-id="{{ $file->id }}"
                                    title="Update Status">
                                    <i class="fa fa-edit"></i> Update Status
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    @if($claimedFiles->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center px-4 py-6 text-gray-500">No orders found.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $claimedFiles->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div id="permissionModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 w-full max-w-md rounded shadow">
            <h3 class="text-lg font-semibold mb-4" id="modalTitle">Update File Status</h3>
            <form id="fileForm">
                @csrf
                <input type="hidden" id="permId" name="permId">
                <div class="mb-4">
                    <label class="block text-sm font-medium">Change Status</label>
                    <select id="permStatus" name="status" class="form-control">

                    </select>
                    <div id="nameError" class="text-red-500 text-sm mt-1"></div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelModal" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    @section('javascript')
        <script>
            $(document).ready(function () {
                const modal = $('#permissionModal');

                // Close modal
                $('#cancelModal').click(function () {
                    modal.addClass('hidden');
                });

                $('#fileForm').submit(function (e) {
                    e.preventDefault();
                    let id = $('#permId').val();
                    let url = `/employee-orders/${id}`;
                    let type = 'PUT';

                    $.ajax({
                        url: url,
                        type: type,
                        data: $(this).serialize(),
                    }).done(function (response) {
                        if (response.success) {
                            notify(response.message, 'success');
                            location.reload();
                        } else {
                            notify(response.message, 'danger');
                        }
                    }).fail(function (response) {
                        if (response.status === 500) {
                            notify('Internal Server Error', 'danger');
                            return
                        }
                        var errors = '<ul class="pl-3">';
                        $.each(response.responseJSON.errors, function (index, val) {
                            errors += '<li>' + val[0] + '</li>';
                        });
                        errors += '</ul>';
                        notify(errors, 'danger');
                    });
                });

                // Edit file status
                $('.editBtn').click(function () {
                    let id = $(this).data('id');
                    $.ajax({
                        url: `/employee-orders/${id}/edit`,
                        type: 'GET',
                        success: function (res) {
                            $('#permId').val(res.id);
                            $('#modalTitle').text('Edit File Status');
                            $('#nameError').text('');
                            const statuses = ['unclaimed', 'in_progress', 'completed'];
                            // Clear previous options
                            $('#permStatus').empty();
                            // Populate new options
                            statuses.forEach(status => {
                                $('#permStatus').append(
                                    `<option value="${status}" ${res.status === status ? 'selected' : ''}>${status.charAt(0).toUpperCase() + status.slice(1)}</option>`
                                );
                            });
                            modal.removeClass('hidden');
                        },
                        error: function (err) {
                            console.error('Error fetching permission:', err);
                        }
                    });
                });
            });
        </script>
    @endsection
</x-app-layout>

