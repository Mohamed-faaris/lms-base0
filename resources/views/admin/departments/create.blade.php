@extends('layouts.admin')

@section('page-title', 'Create Department')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.departments.index') }}" class="text-blue-600 hover:text-blue-900">
        &larr; Back to Departments
    </a>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Create New Department</h2>

    <form action="{{ route('admin.departments.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- College -->
            <div>
                <label for="college_id" class="block text-sm font-medium text-gray-700 mb-2">College</label>
                <select name="college_id" id="college_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('college_id') border-red-500 @enderror">
                    <option value="">Select College</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" {{ old('college_id') == $college->id ? 'selected' : '' }}>
                            {{ $college->college_name }}
                        </option>
                    @endforeach
                </select>
                @error('college_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Department Name -->
            <div>
                <label for="department_name" class="block text-sm font-medium text-gray-700 mb-2">Department Name</label>
                <input type="text" name="department_name" id="department_name" value="{{ old('department_name') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('department_name') border-red-500 @enderror">
                @error('department_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Department Code -->
            <div>
                <label for="department_code" class="block text-sm font-medium text-gray-700 mb-2">Department Code</label>
                <input type="text" name="department_code" id="department_code" value="{{ old('department_code') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('department_code') border-red-500 @enderror">
                @error('department_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- HOD Name -->
            <div>
                <label for="hod_name" class="block text-sm font-medium text-gray-700 mb-2">HOD Name</label>
                <input type="text" name="hod_name" id="hod_name" value="{{ old('hod_name') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('hod_name') border-red-500 @enderror">
                @error('hod_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Department
            </button>
        </div>
    </form>
</div>
@endsection
