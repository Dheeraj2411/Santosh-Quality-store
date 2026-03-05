<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'label'   => 'required|string|max:50',
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'line1'   => 'required|string|max:255',
            'line2'   => 'nullable|string|max:255',
            'city'    => 'required|string|max:100',
            'state'   => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
        ]);

        $data['user_id'] = auth()->id();

        if ($request->is_default || auth()->user()->addresses()->count() === 0) {
            auth()->user()->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        Address::create($data);

        return back()->with('success', 'Address added successfully.');
    }

    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $data = $request->validate([
            'label'   => 'required|string|max:50',
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'line1'   => 'required|string|max:255',
            'line2'   => 'nullable|string|max:255',
            'city'    => 'required|string|max:100',
            'state'   => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
        ]);

        $address->update($data);

        return back()->with('success', 'Address updated.');
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);
        $address->delete();
        return back()->with('success', 'Address removed.');
    }

    public function setDefault(Address $address)
    {
        $this->authorize('update', $address);
        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return back()->with('success', 'Default address updated.');
    }
}
