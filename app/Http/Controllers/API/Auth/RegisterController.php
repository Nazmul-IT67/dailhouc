<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistationOtp;
use App\Mail\RegistrationOtp;
use App\Models\EmailOtp;
use App\Models\ProfileOption;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{

    use ApiResponse;
    /**
     * Send a Register (OTP) to the user via email.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    private function sendOtp($user)
    {

        $code = rand(1000, 9999);

        // Store verification code in the database
        $verification = EmailOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'verification_code' => $code,
                'expires_at'        => Carbon::now()->addMinutes(15),
            ]
        );

        Mail::to($user->email)->send(new RegistrationOtp($user, $code));
    }
    /**
     * Register User
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request with the register query.
     * @return \Illuminate\Http\JsonResponse  JSON response with success or error.
     */

    public function userRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'avatar'         => 'nullable|image|mimes:jpeg,png,jpg,svg|max:20480',
            'password'       => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'agree_to_terms' => 'required|boolean',
            'code'           => 'nullable|string|max:10',
        ], [
            'password.min' => 'The password must be at least 8 characters long.',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        DB::beginTransaction();
        try {
            // upload avatar if exists
            $avatarPath = $request->hasFile('avatar') ? uploadImage($request->file('avatar'), 'User/Avatar') : null;

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->avatar = $avatarPath;
            $user->password = Hash::make($request->input('password'));
            $user->agree_to_terms = $request->input('agree_to_terms');

            // Dailhouc fields
            $user->code = $request->input('code');
            $user->phone = $request->input('phone');
            $user->whatsapp = $request->input('whatsapp');
            $user->country_id = $request->input('country_id');
            $user->city_id = $request->input('city_id');
            $user->postal_code = $request->input('postal_code');
            $user->street_address = $request->input('street_address');

            $user->save();
            $this->sendOtp($user);

            if ($request->has('device_token')) {
                $user->device_token = $request->input('device_token');
                $user->save();
                NotificationService::sendWelcomeNotification($user);
            }

            DB::commit();
            return $this->success($user, 'User registered successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 500);
        }
    }
    /**
     * Verify the OTP sent to the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function otpVerify(Request $request)
    {

        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric|digits:4',
            // 'device_token' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        try {
            // Retrieve the user by email
            $user = User::where('email', $request->input('email'))->first();

            $verification = EmailOtp::where('user_id', $user->id)
                ->where('verification_code', $request->input('otp'))
                ->where('expires_at', '>', Carbon::now())
                ->first();


            if ($verification) {
                // Update device token if provided
                if ($request->has('device_token')) {
                    $user->device_token = $request->input('device_token');
                    $user->save();
                }

                $user->email_verified_at = Carbon::now();
                $user->save();

                $verification->delete();

                $token =  $user->createToken('auth_token')->plainTextToken;

                $user->setAttribute('token', $token);


                return $this->success($user, 'OTP verified successfully', 200);
            } else {

                return $this->error([], 'Invalid or expired OTP', 400);
            }
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    /**
     * Resend an OTP to the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function otpResend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        try {
            // Retrieve the user by email
            $user = User::where('email', $request->input('email'))->first();

            $this->sendOtp($user);

            return $this->success($user, 'OTP has been sent successfully.', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }
    function emailExists()
    {
        // check the email exists or not
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email|unique:users,email',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }
        // If the email does not exist, return a success response
        return $this->success([], 'Email does not exist', 200);
    }
}
