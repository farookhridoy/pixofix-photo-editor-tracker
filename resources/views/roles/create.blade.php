<x-app-layout>
    <x-slot name="pageTitle">
        {{$pageTitle}}
    </x-slot>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Create Role</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-4xl mx-auto bg-white shadow p-6 rounded">
            <form method="POST" action="{{ route('roles.store') }}">
                @include('roles._form')
            </form>
        </div>
    </div>
</x-app-layout>
