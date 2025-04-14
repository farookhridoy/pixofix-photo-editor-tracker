<x-app-layout>
    <x-slot name="pageTitle">
        {{$pageTitle}}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Roles
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 text-right">
                <a href="{{ route('roles.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    <i class="fa fa-plus"></i> Create Role
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead>
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Permissions</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $role)
                        <tr class="divide-y divide-gray-200 dark:divide-gray-700">
                            <td class="px-4 text-white py-2 whitespace-nowrap">{{$loop->iteration }}</td>
                            <td class="px-4 text-white py-2 whitespace-nowrap">{{ $role->name }}</td>
                            <td class="text-white py-2" style="width: 70% !important;">{{ implode(', ',
                            $role->permissions->pluck('name')->toArray()) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <a href="{{ route('roles.edit', $role) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-2">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline">
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
                <div class="mt-4">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
