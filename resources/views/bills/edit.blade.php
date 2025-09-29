@extends('layouts.app')

@section('title', 'Edit Bill - ' . $bill->month . ' ' . $bill->billCategory->name)

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <svg class="w-8 h-8 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
            </svg>
            Edit Bill
        </h1>
        <p class="mt-2 text-sm text-gray-700">Update bill information.</p>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Bill Information</h3>
            </div>
            
            <form action="{{ route('bills.update', $bill) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700">Month *</label>
                        <input type="month" 
                               id="month" 
                               name="month" 
                               value="{{ old('month', $bill->month) }}" 
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
                            <option value="paid" {{ old('status', $bill->status) === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ old('status', $bill->status) === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
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
                               value="{{ old('amount', $bill->amount) }}" 
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
                               value="{{ old('due_amount', $bill->due_amount) }}" 
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
                                <option value="{{ $flat->id }}" {{ old('flat_id', $bill->flat_id) == $flat->id ? 'selected' : '' }}>
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
                                <option value="{{ $category->id }}" {{ old('bill_category_id', $bill->bill_category_id) == $category->id ? 'selected' : '' }}>
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
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror">{{ old('notes', $bill->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('bills.show', $bill) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Update Bill
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



