<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $devices = Device::where(
            'user_id',
            $request->user()->id
        )
        ->latest()
        ->get();

        return view(
            'dashboard',
            compact('devices')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create New Device
    |--------------------------------------------------------------------------
    */

    public function createPairing(Request $request)
    {
        $request->validate([
            'device_name' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        do {
            $pairingCode = strtoupper(
                Str::random(6)
            );
        } while (
            Device::where(
                'pairing_code',
                $pairingCode
            )->exists()
        );

        $device = Device::create([
            'user_id' => $request->user()->id,

            'device_name' =>
                $request->device_name,

            'pairing_code' =>
                $pairingCode,

            'device_token' =>
                null,

            'is_locked' =>
                false,

            'unlock_code_hash' =>
                null,

            'unlock_code_expires_at' =>
                null,

            'last_seen' =>
                null,
        ]);

        return back()->with(
            'device_created',
            $device->id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Lock Device
    |--------------------------------------------------------------------------
    */

    public function lock(
        Request $request,
        Device $device
    ) {
        $this->authorizeDevice(
            $request,
            $device
        );

        if (
            empty($device->device_token)
        ) {
            return back()->with(
                'error',
                'এই ডিভাইস এখনো APK থেকে paired হয়নি।'
            );
        }

        $device->update([
            'is_locked' => true,

            /*
             * পুরনো unlock code invalid
             * করে দেওয়া হচ্ছে।
             */
            'unlock_code_hash' => null,

            'unlock_code_expires_at' => null,
        ]);

        return back()->with(
            'success',
            $device->device_name .
            ' এখন LOCK করা হয়েছে।'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Unlock Device Directly From Dashboard
    |--------------------------------------------------------------------------
    */

    public function unlock(
        Request $request,
        Device $device
    ) {
        $this->authorizeDevice(
            $request,
            $device
        );

        $device->update([
            'is_locked' => false,

            'unlock_code_hash' => null,

            'unlock_code_expires_at' => null,
        ]);

        return back()->with(
            'success',
            $device->device_name .
            ' UNLOCK করা হয়েছে।'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Generate 6 Digit Unlock Code
    |--------------------------------------------------------------------------
    */

    public function generateUnlockCode(
        Request $request,
        Device $device
    ) {
        $this->authorizeDevice(
            $request,
            $device
        );

        if (
            empty($device->device_token)
        ) {
            return back()->with(
                'error',
                'ডিভাইস এখনো paired হয়নি।'
            );
        }

        if (!$device->is_locked) {
            return back()->with(
                'error',
                'ডিভাইস বর্তমানে locked নয়।'
            );
        }

        /*
         * Secure random 6 digit code.
         */
        $code = (string) random_int(
            100000,
            999999
        );

        $device->update([
            'unlock_code_hash' =>
                Hash::make($code),

            /*
             * Code 30 minutes valid.
             */
            'unlock_code_expires_at' =>
                now()->addMinutes(30),
        ]);

        return back()->with(
            'unlock_code',
            $code
        );
    }


    /*
    |--------------------------------------------------------------------------
    | API - Pair Device
    |--------------------------------------------------------------------------
    */

    public function pair(
        Request $request
    ) {
        $request->validate([
            'pairing_code' => [
                'required',
                'string',
                'size:6',
            ],
        ]);

        $code = strtoupper(
            trim(
                $request->pairing_code
            )
        );

        $device = Device::where(
            'pairing_code',
            $code
        )->first();

        if (!$device) {
            return response()->json([
                'success' => false,

                'error' =>
                    'Invalid pairing code.',
            ], 404);
        }

        /*
         * Generate permanent device token.
         */
        $token = Str::random(80);

        $device->update([
            'device_token' => $token,

            'last_seen' => now(),
        ]);

        return response()->json([
            'success' => true,

            'message' =>
                'Device paired successfully.',

            'device_token' =>
                $token,

            'device_id' =>
                $device->id,

            'device_name' =>
                $device->device_name,

            'is_locked' =>
                $device->is_locked,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | API - Device Status
    |--------------------------------------------------------------------------
    */

    public function status(
        string $token
    ) {
        $device = Device::where(
            'device_token',
            $token
        )->first();

        if (!$device) {
            return response()->json([
                'success' => false,

                'error' =>
                    'Device not found.',
            ], 404);
        }

        /*
         * Update heartbeat.
         */
        $device->update([
            'last_seen' => now(),
        ]);

        return response()->json([
            'success' => true,

            'device_id' =>
                $device->id,

            'device_name' =>
                $device->device_name,

            'is_locked' =>
                (bool) $device->is_locked,

            'server_time' =>
                now()->toIso8601String(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | API - Unlock With Code
    |--------------------------------------------------------------------------
    */

    public function unlockWithCode(
        Request $request
    ) {
        $request->validate([
            'device_token' => [
                'required',
                'string',
            ],

            'code' => [
                'required',
                'digits:6',
            ],
        ]);

        $device = Device::where(
            'device_token',
            $request->device_token
        )->first();

        if (!$device) {
            return response()->json([
                'success' => false,

                'error' =>
                    'Device not found.',
            ], 404);
        }

        /*
         * Check code exists.
         */
        if (
            empty(
                $device->unlock_code_hash
            )
        ) {
            return response()->json([
                'success' => false,

                'error' =>
                    'No unlock code is active.',
            ], 403);
        }

        /*
         * Check expiry.
         */
        if (
            empty(
                $device->unlock_code_expires_at
            ) ||
            now()->greaterThan(
                $device->unlock_code_expires_at
            )
        ) {
            return response()->json([
                'success' => false,

                'error' =>
                    'Unlock code expired.',
            ], 403);
        }

        /*
         * Verify hash.
         */
        if (
            !Hash::check(
                $request->code,
                $device->unlock_code_hash
            )
        ) {
            return response()->json([
                'success' => false,

                'error' =>
                    'Invalid unlock code.',
            ], 403);
        }

        /*
         * Successful unlock.
         *
         * One-time code is destroyed.
         */
        $device->update([
            'is_locked' => false,

            'unlock_code_hash' => null,

            'unlock_code_expires_at' => null,

            'last_seen' => now(),
        ]);

        return response()->json([
            'success' => true,

            'message' =>
                'Device unlocked successfully.',

            'is_locked' => false,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Download Page
    |--------------------------------------------------------------------------
    */

    public function downloadPage(
        string $code
    ) {
        $code = strtoupper(
            trim($code)
        );

        $device = Device::where(
            'pairing_code',
            $code
        )->first();

        if (!$device) {
            abort(
                404,
                'Invalid download link.'
            );
        }

        return view(
            'device-download',
            [
                'device' => $device,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Download APK
    |--------------------------------------------------------------------------
    */

    public function downloadApk(
        string $code
    ) {
        $code = strtoupper(
            trim($code)
        );

        $device = Device::where(
            'pairing_code',
            $code
        )->first();

        if (!$device) {
            abort(
                404,
                'Invalid download link.'
            );
        }

        $apkPath =
            'parental-app.apk';

        if (
            !Storage::disk('public')
                ->exists($apkPath)
        ) {
            abort(
                404,
                'APK file is not available.'
            );
        }

        return Storage::disk('public')
            ->download(
                $apkPath,
                'Child-Guard.apk',
                [
                    'Content-Type' =>
                        'application/vnd.android.package-archive',
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Dashboard Status API
    |--------------------------------------------------------------------------
    */

    public function dashboardStatus(
        Request $request
    ) {
        $devices = Device::where(
            'user_id',
            $request->user()->id
        )
        ->latest()
        ->get();

        return response()->json([
            'success' => true,

            'devices' =>
                $devices->map(
                    function ($device) {

                        return [
                            'id' =>
                                $device->id,

                            'device_name' =>
                                $device->device_name,

                            'is_locked' =>
                                (bool) $device->is_locked,

                            'paired' =>
                                !empty(
                                    $device->device_token
                                ),

                            'last_seen' =>
                                $device->last_seen
                                    ? $device->last_seen
                                        ->toIso8601String()
                                    : null,
                        ];
                    }
                ),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Security Helper
    |--------------------------------------------------------------------------
    */

    private function authorizeDevice(
        Request $request,
        Device $device
    ): void {
        abort_unless(
            $device->user_id ===
                $request->user()->id,
            403
        );
    }
}