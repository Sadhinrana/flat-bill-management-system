@extends('layouts.app')

@section('title', 'Edit Flat - ' . $flat->flat_number)

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <svg class="w-8 h-8 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
            </svg>
            Edit Flat
        </h1>
        <p class="mt-2 text-sm text-gray-700">Update flat information.</p>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Flat Information</h3>
            </div>
            
            <form action="{{ route('flats.update', $flat) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="flat_number" class="block text-sm font-medium text-gray-700">Flat Number *</label>
                        <input type="text" 
                               id="flat_number" 
                               name="flat_number" 
                               value="{{ old('flat_number', $flat->flat_number) }}" 
                               required
                               placeholder="e.g., A101, B202"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('flat_number') border-red-300 @enderror">
                        @error('flat_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="building_id" class="block text-sm font-medium text-gray-700">Building *</label>
                        <select id="building_id" 
                                name="building_id" 
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('building_id') border-red-300 @enderror">
                            <option value="">Select Building</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}" {{ old('building_id', $flat->building_id) == $building->id ? 'selected' : '' }}>
                                    {{ $building->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('building_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="owner_name" class="block text-sm font-medium text-gray-700">Owner Name *</label>
                    <input type="text" 
                           id="owner_name" 
                           name="owner_name" 
                           value="{{ old('owner_name', $flat->owner_name) }}" 
                           required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('owner_name') border-red-300 @enderror">
                    @error('owner_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="owner_contact" class="block text-sm font-medium text-gray-700">Contact Number *</label>
                        <input type="tel" 
                               id="owner_contact" 
                               name="owner_contact" 
                               value="{{ old('owner_contact', $flat->owner_contact) }}" 
                               required
                               placeholder="+1234567890"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('owner_contact') border-red-300 @enderror">
                        @error('owner_contact')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="owner_email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                        <input type="email" 
                               id="owner_email" 
                               name="owner_email" 
                               value="{{ old('owner_email', $flat->owner_email) }}" 
                               required
                               placeholder="owner@example.com"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('owner_email') border-red-300 @enderror">
                        @error('owner_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('flats.show', $flat) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Update Flat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



