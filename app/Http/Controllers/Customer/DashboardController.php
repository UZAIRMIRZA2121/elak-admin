<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\BusinessSetting;
use App\CentralLogics\Helpers;

class DashboardController extends Controller
{
    public function index()
    {
        return view('customer.dashboard');
    }

    public function lang($local)
    {
        $direction = BusinessSetting::where('key', 'site_direction')->first();
        $direction = $direction->value ?? 'ltr';
        $language = BusinessSetting::where('key', 'system_language')->first();
        foreach (json_decode($language?->value, true) as $key => $data) {
            if ($data['code'] == $local) {
                $direction = isset($data['direction']) ? $data['direction'] : 'ltr';
            }
        }
        session()->forget('language_settings');
        Helpers::language_load();
        session()->put('local', $local);
        session()->put('site_direction', $direction);
        return redirect()->back();
    }

    public function settings()
    {
        return view('layouts.customer.settings');
    }

    public function settings_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:clients,email,' . auth('customer')->id(),
        ], [
            'name.required' => translate('messages.name_is_required'),
        ]);

        $client = \App\Models\Client::find(auth('customer')->id());

        if ($request->hasFile('logo')) {
            if ($client->logo && file_exists(public_path($client->logo))) {
                unlink(public_path($client->logo));
            }
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $imageName = time() . '_' . uniqid() . '.' . $extension;

            $destination = public_path('uploads/clients/logos');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $imageName);
            $image_name = 'uploads/clients/logos/' . $imageName;
        } else {
            $image_name = $client->logo;
        }

        if ($request->hasFile('cover')) {
            if ($client->cover && file_exists(public_path($client->cover))) {
                unlink(public_path($client->cover));
            }
            $file = $request->file('cover');
            $extension = $file->getClientOriginalExtension();
            $coverName = time() . '_' . uniqid() . '.' . $extension;

            $destination = public_path('uploads/clients/covers');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $coverName);
            $cover_name = 'uploads/clients/covers/' . $coverName;
        } else {
            $cover_name = $client->cover;
        }

        $client->name = $request->name;
        $client->email = $request->email;
        $client->logo = $image_name;
        $client->cover = $cover_name;
        $client->save();
        \Brian2694\Toastr\Facades\Toastr::success(translate('messages.profile_updated_successfully'));
        return back();
    }

    public function settings_password_update(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => ['required', 'same:confirm_password', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
            'confirm_password' => 'required',
        ]);

        $client = \App\Models\Client::find(auth('customer')->id());

        if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $client->password)) {
            \Brian2694\Toastr\Facades\Toastr::error(translate('messages.old_password_does_not_match'));
            return back();
        }

        $client->password = bcrypt($request->password);
        $client->save();
        \Brian2694\Toastr\Facades\Toastr::success(translate('messages.password_updated_successfully'));
        return back();
    }
}
