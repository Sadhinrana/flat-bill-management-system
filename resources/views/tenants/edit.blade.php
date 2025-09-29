@extends('layouts.app')

@section('title', 'Edit Tenant - ' . $tenant->name)

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <svg class="w-8 h-8 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
            </svg>
            Edit Tenant
        </h1>
        <p class="mt-2 text-sm text-gray-700">Update tenant information.</p>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Tenant Information</h3>
            </div>

            <form action="{{ route('tenants.update', $tenant) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $tenant->name) }}"
                               required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact" class="block text-sm font-medium text-gray-700">Contact Number *</label>
                        <input type="tel"
                               id="contact"
                               name="contact"
                               value="{{ old('contact', $tenant->contact) }}"
                               required
                               placeholder="+1234567890"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('contact') border-red-300 @enderror">
                        @error('contact')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', $tenant->email) }}"
                           required
                           placeholder="tenant@example.com"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="building_id" class="block text-sm font-medium text-gray-700">Building *</label>
                        <select id="building_id"
                                name="building_id"
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('building_id') border-red-300 @enderror">
                            <option value="">Select Building</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}" {{ old('building_id', $tenant->building_id) == $building->id ? 'selected' : '' }}>
                                    {{ $building->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('building_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="flat_id" class="block text-sm font-medium text-gray-700">Flat Assignment</label>
                        <select id="flat_id"
                                name="flat_id"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('flat_id') border-red-300 @enderror">
                            <option value="">Select Flat (Optional)</option>
                            @foreach($flats as $flat)
                                <option value="{{ $flat->id }}" {{ old('flat_id', $tenant->flat_id) == $flat->id ? 'selected' : '' }}>
                                    {{ $flat->flat_number }} - {{ $flat->building->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('flat_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">You can assign the tenant to a flat later if not selected now.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('tenants.show', $tenant) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Update Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        document.getElementById('building_id').addEventListener('change', function() {
            const buildingId = this.value;
            const flatSelect = document.getElementById('flat_id');

            // Clear existing options except the first one
            flatSelect.innerHTML = '<option value="">Select Flat (Optional)</option>';

            if (buildingId) {
                // Fetch flats for the selected building
                fetch(`/buildings/${buildingId}/flats`)
                .then(response => response.json())
                .then(flats => {
                    flats.forEach(flat => {
                        const option = document.createElement('option');
                        option.value = flat.id;
                        option.textContent = `${flat.flat_number} - ${flat.building.name}`;
                        flatSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching flats:', error));
            }
        });
    </script>
@endsection



