
@csrf

<div class="mb-4">
    <label class="block text-sm font-medium">Role Name</label>
    <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}"
           class="w-full mt-1 p-2 border rounded" required>
    @error('name')
    <div class="text-red-500 text-sm">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium">Permissions</label>
    <div class="grid grid-cols-2 gap-2 mt-2">
        @foreach($permissions as $perm)
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="permissions[]"
                       value="{{ $perm->name }}"
                    {{ isset($rolePermissions) && in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>
                <span>{{ $perm->name }}</span>
            </label>
        @endforeach
    </div>
</div>

<div class="flex justify-end mt-4">
    <a href="{{ route('roles.index') }}" class="text-gray-500 hover:underline mr-4">Cancel</a>
    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        <i class="fa fa-save mr-1"></i> {{ isset($role) ? 'Update' : 'Create' }} Role
    </button>
</div>
