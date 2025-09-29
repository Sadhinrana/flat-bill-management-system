<?php

namespace App\Http\Controllers;

use App\Http\Requests\HouseOwnerRequest;
use App\Models\User;

class HouseOwnerController extends Controller
{
    public function index()
    {
        $owners = User::with('ownedBuilding')
            ->where('role', 'house_owner')
            ->paginate(10);

        return view('house-owners.index', compact('owners'));
    }

    public function create()
    {
        return view('house-owners.create');
    }

    public function store(HouseOwnerRequest $request)
    {
        $data = $request->validated();
        $data['role'] = 'house_owner';
        User::create($data);
        return redirect()->route('house-owners.index')->with('success', 'House owner created successfully.');
    }

    public function show(User $house_owner)
    {
        return view('house-owners.show', ['owner' => $house_owner]);
    }

    public function edit(User $house_owner)
    {
        return view('house-owners.edit', ['owner' => $house_owner]);
    }

    public function update(HouseOwnerRequest $request, User $house_owner)
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['role'] = 'house_owner';
        $house_owner->update($data);
        return redirect()->route('house-owners.index')->with('success', 'House owner updated successfully.');
    }

    public function destroy(User $house_owner)
    {
        $house_owner->delete();
        return redirect()->route('house-owners.index')->with('success', 'House owner deleted successfully.');
    }
}
