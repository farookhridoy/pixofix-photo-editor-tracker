<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Users Management') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    <i class="fa fa-plus mr-2"></i> Create User
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead>
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Email</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Role</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-4text-gray-600  py-2 whitespace-nowrap">{{ ($users->currentPage() - 1) *
                            $users->perPage() + $loop->iteration }}</td>
                            <td class="px-4 text-white  py-2 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-4 text-white  py-2 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">
                                @foreach($user->getRoleNames() as $role)
                                    <span
                                        class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                        <i class="fa fa-user-tag mr-1"></i> {{ $role }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <a href="{{ route('users.edit', $user) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-2" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block"
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

                    @if($users->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center px-4 py-6 text-gray-500">No users found.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
