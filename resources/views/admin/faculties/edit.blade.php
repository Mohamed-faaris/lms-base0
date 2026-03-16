@extends('layouts.admin')

@section('page-title', 'Edit Faculty')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.faculties.index') }}" class="text-blue-600 hover:text-blue-900">
        &larr; Back to Faculties
    </a>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Edit Faculty</h2>

    <form action="{{ route('admin.faculties.update', $faculty->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- College -->
            <div>
                <label for="college_id" class="block text-sm font-medium text-gray-700 mb-2">College</label>
                <select name="college_id" id="college_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('college_id') border-red-500 @enderror">
                    <option value="">Select College</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" {{ old('college_id', $faculty->college_id) == $college->id ? 'selected' : '' }}>
                            {{ $college->college_name }}
                        </option>
                    @endforeach
                </select>
                @error('college_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Department -->
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select name="department_id" id="department_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('department_id') border-red-500 @enderror">
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id', $faculty->department_id) == $department->id ? 'selected' : '' }}>
                            {{ $department->college->college_name ?? '' }} - {{ $department->department_name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Faculty Name -->
            <div>
                <label for="faculty_name" class="block text-sm font-medium text-gray-700 mb-2">Faculty Name</label>
                <input type="text" name="faculty_name" id="faculty_name" value="{{ old('faculty_name', $faculty->faculty_name) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('faculty_name') border-red-500 @enderror">
                @error('faculty_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Faculty Email -->
            <div>
                <label for="faculty_email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="faculty_email" id="faculty_email" value="{{ old('faculty_email', $faculty->faculty_email) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('faculty_email') border-red-500 @enderror">
                @error('faculty_email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $faculty->phone) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Designation -->
            <div>
                <label for="designation" class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
                <input type="text" name="designation" id="designation" value="{{ old('designation', $faculty->designation) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('designation') border-red-500 @enderror">
                @error('designation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                    <option value="active" {{ old('status', $faculty->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $faculty->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update Faculty
            </button>
        </div>
    </form>
</div>
@endsection
