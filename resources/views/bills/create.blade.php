@extends('layouts.app')

@section('title', 'Create Bill')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <svg class="w-8 h-8 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
            </svg>
            Create New Bill
        </h1>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Bill Information</h3>
            </div>
            
            <form action="{{ route('bills.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700">Month *</label>
                        <input type="month" 
                               id="month" 
                               name="month" 
                               value="{{ old('month', date('Y-m')) }}" 
                               required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('month') border-red-300 @enderror">
                        @error('month')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                        <select id="status" 
                                name="status" 
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 @enderror">
                            <option value="">Select Status</option>
                            <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ old('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                        <input type="number" 
                               id="amount" 
                               name="amount" 
                               value="{{ old('amount') }}" 
                               required
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('amount') border-red-300 @enderror">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="due_amount" class="block text-sm font-medium text-gray-700">Due Amount *</label>
                        <input type="number" 
                               id="due_amount" 
                               name="due_amount" 
                               value="{{ old('due_amount') }}" 
                               required
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('due_amount') border-red-300 @enderror">
                        @error('due_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="flat_id" class="block text-sm font-medium text-gray-700">Flat *</label>
                        <select id="flat_id" 
                                name="flat_id" 
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('flat_id') border-red-300 @enderror">
                            <option value="">Select Flat</option>
                            @foreach($flats as $flat)
                                <option value="{{ $flat->id }}" {{ old('flat_id', request('flat_id')) == $flat->id ? 'selected' : '' }}>
                                    {{ $flat->flat_number }} - {{ $flat->building->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('flat_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bill_category_id" class="block text-sm font-medium text-gray-700">Bill Category *</label>
                        <select id="bill_category_id" 
                                name="bill_category_id" 
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('bill_category_id') border-red-300 @enderror">
                            <option value="">Select Category</option>
                            @foreach($billCategories as $category)
                                <option value="{{ $category->id }}" {{ old('bill_category_id', request('bill_category_id')) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} - {{ $category->building->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('bill_category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3"
                              placeholder="Additional notes about this bill"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('bills.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Create Bill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('status').addEventListener('change', function() {
    const status = this.value;
    const amountInput = document.getElementById('amount');
    const dueAmountInput = document.getElementById('due_amount');
    
    if (status === 'paid') {
        dueAmountInput.value = '0.00';
    } else if (status === 'unpaid') {
        dueAmountInput.value = amountInput.value;
    }
});

document.getElementById('amount').addEventListener('input', function() {
    const status = document.getElementById('status').value;
    const dueAmountInput = document.getElementById('due_amount');
    
    if (status === 'unpaid') {
        dueAmountInput.value = this.value;
    }
});
</script>
@endsection



