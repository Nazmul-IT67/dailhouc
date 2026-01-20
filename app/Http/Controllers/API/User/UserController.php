<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponse;
    public function userDetails(Request $request)
    {
        $user = Auth::user();
        $user->load('country', 'city');
        if (!$user) {
            return $this->error([], 'Unauthenticate User', 200);
        }
        return $this->success($user, 'User Data fetch Successful!', 200);
    }

    public function updateUser(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error([], 'Unauthenticated', 401);
        }

        $validator = Validator::make($request->all(), [
            'name'           => 'sometimes|string|max:255',
            'avatar'         => 'nullable|image|mimes:jpeg,png,jpg,svg|max:20480',
            'phone'          => 'nullable|string|max:20',
            'whatsapp'       => 'nullable|string|max:20',
            'country_id'     => 'nullable|exists:countries,id',
            'city_id'        => 'nullable|exists:cities,id',
            'postal_code'    => 'nullable|string|max:20',
            'street_address' => 'nullable|string|max:255',
            'code'           => 'nullable|string|max:10',
            // 'device_token'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        DB::beginTransaction();
        try {
            if ($request->hasFile('avatar')) {
                $user->avatar = uploadImage($request->file('avatar'), 'User/Avatar');
            }

            $user->name           = $request->input('name', $user->name);
            $user->code = $request->input('code');
            $user->phone          = $request->input('phone', $user->phone);
            $user->whatsapp       = $request->input('whatsapp', $user->whatsapp);
            $user->country_id     = $request->input('country_id', $user->country_id);
            $user->city_id        = $request->input('city_id', $user->city_id);
            $user->postal_code    = $request->input('postal_code', $user->postal_code);
            $user->street_address = $request->input('street_address', $user->street_address);
            // $user->device_token   = $request->input('device_token', $user->device_token);

            $user->save();

            DB::commit();
            return $this->success($user, 'User updated successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return $this->error([], 'Something went wrong', 500);
        }
    }
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error([], 'Unauthenticated', 401);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'old_password'      => 'required|string|min:8',
            'new_password'      => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->error([], 'Old password is incorrect', 400);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->success([], 'Password updated successfully', 200);
    }
    public function logoutUser(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error([], 'Unauthenticated', 401);
        }

        try {
            // 🧩 Logout only from current device/token
            $request->user()->currentAccessToken()->delete();

            // 🔁 Or, if you want to logout from ALL devices/tokens:
            // $request->user()->tokens()->delete();

            return $this->success([], 'User logged out successfully', 200);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->error([], 'Something went wrong while logging out', 500);
        }
    }
    public function deleteAccount(Request $request)
    {
        if (!Auth::user()) {
            return $this->error([], 'Unauthenticated', 401);
        }
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->error([], 'Password is incorrect', 400);
        }

        try {
            $user->delete();
            return $this->success([], 'Your account has been deleted successfully', 200);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->error([], 'Something went wrong while deleting your account', 500);
        }
    }
}
