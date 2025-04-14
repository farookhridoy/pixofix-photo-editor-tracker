<x-app-layout>
    <x-slot name="pageTitle">
        {{$pageTitle}}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Permission Management') }}
        </h2>
    </x-slot>

    <div class="p-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <button id="addNewBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    <i class="fa fa-plus"></i> Add Permission
                </button>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead>
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Name</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="permissionTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($permissions as $index => $permission)
                        <tr id="permRow{{ $permission->id }}">
                            <td class="px-4 text-white py-2 whitespace-nowrap">{{ ($permissions->currentPage() - 1) *
                            $permissions->perPage() + $loop->iteration }}</td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">{{ $permission->name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <button class="editBtn text-blue-600" data-id="{{ $permission->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="deleteBtn text-red-600" data-id="{{ $permission->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    @if($permissions->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center px-4 py-6 text-gray-500">No permissions found.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div id="permissionModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 w-full max-w-md rounded shadow">
            <h3 class="text-lg font-semibold mb-4" id="modalTitle">Add Permission</h3>
            <form id="permissionForm">
                @csrf
                <input type="hidden" id="permId" name="permId">
                <div class="mb-4">
                    <label class="block text-sm font-medium">Permission Name</label>
                    <input type="text" id="permName" name="permissions" class="w-full border mt-1 p-2 rounded"
                           placeholder="Use comma for multiple permissions">
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
                const permTable = $('#permissionTable');

                // Open modal to add new permission
                $('#addNewBtn').click(function () {
                    $('#permId').val('');
                    $('#permName').val('');
                    $('#modalTitle').text('Add Permission');
                    $('#nameError').text('');
                    modal.removeClass('hidden');
                });

                // Close modal
                $('#cancelModal').click(function () {
                    modal.addClass('hidden');
                });

                // Handle permission form submission (Create or Update)
                $('#permissionForm').submit(function (e) {
                    e.preventDefault();
                    let id = $('#permId').val();
                    let url = id ? `/permissions/${id}` : `/permissions`;
                    let type = id ? 'PUT' : 'POST';

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

                // Edit permission
                $('.editBtn').click(function () {
                    let id = $(this).data('id');
                    $.ajax({
                        url: `/permissions/${id}`,
                        type: 'GET',
                        success: function (res) {
                            $('#permId').val(res.id);
                            $('#permName').val(res.name);
                            $('#modalTitle').text('Edit Permission');
                            $('#nameError').text('');
                            modal.removeClass('hidden');
                        },
                        error: function (err) {
                            console.error('Error fetching permission:', err);
                        }
                    });
                });

                // Delete permission
                $('.deleteBtn').click(function () {
                    let id = $(this).data('id');
                    if (confirm('Delete this permission?')) {
                        $.ajax({
                            url: `/permissions/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function () {
                                $(`#permRow${id}`).remove(); // Remove the row from the table
                                notify(response.message, 'success');
                            },
                            error: function (err) {
                                alert('Error deleting permission!');
                            }
                        });
                    }
                });

                // Initialize Select2 for permission selection with search and filter
                // $('#permissionsSelect').select2({
                //     ajax: {
                //         url: '/permissions/search',
                //         dataType: 'json',
                //         processResults: function (data) {
                //             return {
                //                 results: data.map(function (perm) {
                //                     return {
                //                         id: perm.id,
                //                         text: perm.name
                //                     };
                //                 })
                //             };
                //         }
                //     },
                //     placeholder: 'Search for permissions',
                //     allowClear: true
                // });
            });
        </script>
    @endsection

</x-app-layout>
