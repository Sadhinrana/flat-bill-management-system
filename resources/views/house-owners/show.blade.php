@extends('layouts.app')

@section('title', 'House Owner Details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">House Owner Details</h1>
        <div class="space-y-2">
            <p><span class="font-semibold">Name:</span> {{ $owner->name }}</p>
            <p><span class="font-semibold">Email:</span> {{ $owner->email }}</p>
            <p><span class="font-semibold">Role:</span> {{ ucfirst(str_replace('_', ' ', $owner->role)) }}</p>
            <p><span class="font-semibold">Buildings:</span> {{ $owner->ownedBuilding->name ?? '' }}</p>
        </div>
        <div class="mt-6 flex space-x-3">
            <a href="{{ route('house-owners.edit', $owner) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">Edit</a>
            <a href="{{ route('house-owners.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm">Back</a>
        </div>
    </div>
</div>
@endsection
