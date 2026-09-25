<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        Child Guard Dashboard
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        :root {
            --indigo: #4f46e5;
            --indigo-dark: #4338ca;
            --red: #dc2626;
            --green: #16a34a;
            --amber: #d97706;
            --ink: #172033;
            --muted: #687086;
            --border: #e6e9ef;
            --bg: #f5f7fb;
        }

        body {
            margin: 0;
            background: var(--bg);
            font-family:
                -apple-system,
                Segoe UI,
                Arial,
                sans-serif;
            color: var(--ink);
        }

        .topbar {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 18px;
        }

        .brand .dot {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--indigo);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .topbar .user {
            font-size: 13px;
            color: var(--muted);
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            padding: 28px 20px 60px;
        }

        .stats-row {
            display: grid;
            grid-template-columns:
                repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat {
            background: white;
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow:
                0 6px 20px
                rgba(0,0,0,.04);
        }

        .stat .num {
            font-size: 28px;
            font-weight: 800;
        }

        .stat .label {
            font-size: 13px;
            color: var(--muted);
            margin-top: 2px;
        }

        .stat.locked .num {
            color: var(--red);
        }

        .stat.unlocked .num {
            color: var(--green);
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow:
                0 8px 30px
                rgba(0,0,0,.06);
        }

        .card h2 {
            margin-top: 0;
            font-size: 18px;
        }

        .card > p {
            color: var(--muted);
            font-size: 13px;
            margin-top: -6px;
        }

        .form-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        input {
            height: 48px;
            padding: 0 15px;
            border: 1px solid #dfe3eb;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
            flex: 1;
            min-width: 220px;
        }

        input:focus {
            border-color: var(--indigo);
        }

        button {
            border: none;
            cursor: pointer;
            border-radius: 10px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 600;
            transition: opacity .15s;
        }

        button:hover {
            opacity: .9;
        }

        .btn-primary {
            background: var(--indigo);
            color: white;
        }

        .btn-danger {
            background: var(--red);
            color: white;
        }

        .btn-success {
            background: var(--green);
            color: white;
        }

        .btn-secondary {
            background: #eef0f4;
            color: #111827;
        }

        .alert {
            padding: 15px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .device-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(320px, 1fr)
                );
            gap: 18px;
        }

        .device {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            transition:
                box-shadow .15s,
                border-color .15s;
        }

        .device:hover {
            box-shadow:
                0 10px 26px
                rgba(0,0,0,.06);
            border-color: #d8dcE6;
        }

        .device-header {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
        }

        .device-name {
            font-size: 18px;
            font-weight: 700;
        }

        .device-sub {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        .badge {
            padding: 5px 11px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .03em;
            white-space: nowrap;
        }

        .badge-locked {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-unlocked {
            background: #dcfce7;
            color: #166534;
        }

        .badge-offline {
            background: #f3f4f6;
            color: #6b7280;
        }

        .badge-not-paired {
            background: #fef3c7;
            color: #92400e;
        }

        .detail-list {
            margin-top: 16px;
            border-top: 1px solid var(--border);
            padding-top: 12px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: 13px;
            padding: 5px 0;
        }

        .detail-row .k {
            color: var(--muted);
        }

        .detail-row .v {
            font-weight: 600;
            text-align: right;
        }

        .detail-row .v.mono {
            font-family:
                ui-monospace,
                Consolas,
                monospace;
            letter-spacing: .05em;
        }

        .code-box {
            margin-top: 15px;
            padding: 15px;
            border-radius: 12px;
            background: #eef2ff;
        }

        .unlock-code {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 7px;
            margin-top: 5px;
            color: #3730a3;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .actions form {
            display: inline;
        }

        .actions button {
            width: 100%;
        }

        .download-link {
            display: block;
            margin-top: 15px;
            padding: 10px 12px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #334155;
            word-break: break-all;
            text-decoration: none;
            font-size: 12px;
        }

        .download-link:hover {
            background: #f1f5f9;
        }

        .empty {
            padding: 48px 20px;
            text-align: center;
            color: var(--muted);
        }

        .empty .big {
            font-size: 38px;
            margin-bottom: 8px;
        }

        @media(max-width: 700px) {

            .stats-row {
                grid-template-columns: 1fr;
            }

            .form-row {
                flex-direction: column;
            }

            input,
            .form-row button {
                width: 100%;
            }
        }

    </style>

</head>

<body>

<div class="topbar">

    <div class="brand">
        <div class="dot">🛡️</div>
        Child Guard
    </div>

    <div class="user">
        {{ auth()->user()->name ?? '' }}
    </div>

</div>

<div class="container">

    {{-- Stats --}}

    <div class="stats-row">

        <div class="stat">
            <div class="num">
                {{ $devices->count() }}
            </div>
            <div class="label">
                মোট Device
            </div>
        </div>

        <div class="stat locked">
            <div class="num">
                {{ $devices->where('is_locked', true)->count() }}
            </div>
            <div class="label">
                Locked
            </div>
        </div>

        <div class="stat unlocked">
            <div class="num">
                {{ $devices->where('is_locked', false)->count() }}
            </div>
            <div class="label">
                Unlocked
            </div>
        </div>

    </div>


    {{-- Success --}}

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif


    {{-- Error --}}

    @if(session('error'))

        <div class="alert alert-error">

            {{ session('error') }}

        </div>

    @endif


    {{-- Unlock Code --}}

    @if(session('unlock_code'))

        <div class="card">

            <h2>
                🔑 Unlock Code
            </h2>

            <p>
                এই code ফোনের locked screen-এ দিতে হবে।
            </p>

            <div class="code-box">

                <div>
                    6 Digit Unlock Code
                </div>

                <div class="unlock-code">

                    {{ session('unlock_code') }}

                </div>

                <div>
                    এই code 30 মিনিট valid।
                </div>

            </div>

        </div>

    @endif


    {{-- Create Device --}}

    <div class="card">

        <h2>
            ➕ নতুন Child Device যোগ করুন
        </h2>

        <p>
            Device তৈরি করলে একটি pairing code
            এবং APK download link তৈরি হবে।
        </p>

        <form
            method="POST"
            action="{{ route('devices.create-pairing') }}"
        >

            @csrf

            <div class="form-row">

                <input
                    type="text"
                    name="device_name"
                    placeholder="Device name, যেমন: Son's Phone"
                    maxlength="100"
                    required
                >

                <button
                    type="submit"
                    class="btn-primary"
                >
                    Create Device
                </button>

            </div>

        </form>

    </div>


    {{-- Device Created --}}

    @if(session('device_created'))

        @php

            $createdDevice =
                \App\Models\Device::find(
                    session('device_created')
                );

        @endphp

        @if($createdDevice)

            <div class="card">

                <h2>
                    ✅ Device তৈরি হয়েছে
                </h2>

                <p>
                    নিচের Pairing Code এবং Download Link
                    child-এর phone-এ ব্যবহার করুন।
                </p>

                <div class="code-box">

                    <strong>
                        Pairing Code
                    </strong>

                    <div class="unlock-code">

                        {{ $createdDevice->pairing_code }}

                    </div>

                </div>

                <a
                    href="{{ route(
                        'devices.download-page',
                        $createdDevice->pairing_code
                    ) }}"
                    target="_blank"
                    class="download-link"
                >

                    {{ route(
                        'devices.download-page',
                        $createdDevice->pairing_code
                    ) }}

                </a>

            </div>

        @endif

    @endif


    {{-- Devices --}}

    <div class="card">

        <h2>
            📱 আমার সব Device
        </h2>

        @if($devices->count() === 0)

            <div class="empty">

                <div class="big">📭</div>

                এখনো কোনো device তৈরি করা হয়নি।<br>
                উপরের ফর্ম থেকে প্রথম device যোগ করুন।

            </div>

        @else

            <div class="device-grid">

                @foreach($devices as $device)

                    @php

                        $isPaired =
                            !empty($device->device_token);

                        $isOnline =
                            $device->last_seen &&
                            $device->last_seen
                                ->gt(now()->subMinutes(2));

                    @endphp

                    <div
                        class="device"
                        id="device-{{ $device->id }}"
                        data-paired="{{ $isPaired ? '1' : '0' }}"
                    >

                        <div class="device-header">

                            <div>

                                <div class="device-name">
                                    {{ $device->device_name }}
                                </div>

                                <div class="device-sub">
                                    Code:
                                    {{ $device->pairing_code }}
                                </div>

                            </div>


                            @if(!$isPaired)

                                <span class="badge badge-not-paired">
                                    NOT PAIRED
                                </span>

                            @elseif($device->is_locked)

                                <span class="badge badge-locked">
                                    LOCKED
                                </span>

                            @elseif($isOnline)

                                <span class="badge badge-unlocked">
                                    UNLOCKED
                                </span>

                            @else

                                <span class="badge badge-offline">
                                    OFFLINE
                                </span>

                            @endif

                        </div>


                        <div class="detail-list">

                            <div class="detail-row">
                                <span class="k">Pairing Code</span>
                                <span class="v mono">{{ $device->pairing_code }}</span>
                            </div>

                            <div class="detail-row">
                                <span class="k">Paired</span>
                                <span class="v">
                                    {{ $isPaired ? 'হ্যাঁ' : 'না, এখনো APK connect করেনি' }}
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="k">Lock Status</span>
                                <span class="v">
                                    {{ $device->is_locked ? 'Locked' : 'Unlocked' }}
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="k">Last Seen</span>
                                <span class="v">
                                    @if($device->last_seen)
                                        {{ $device->last_seen->diffForHumans() }}
                                    @else
                                        কখনো না
                                    @endif
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="k">যোগ করা হয়েছে</span>
                                <span class="v">
                                    {{ $device->created_at->diffForHumans() }}
                                </span>
                            </div>

                        </div>


                        {{-- Download --}}

                        <a
                            href="{{ route(
                                'devices.download-page',
                                $device->pairing_code
                            ) }}"
                            target="_blank"
                            class="download-link"
                        >

                            ⬇️ Download / Pairing লিঙ্ক দেখুন

                        </a>


                        {{-- Actions --}}

                        <div class="actions">


                            {{-- LOCK --}}

                            @if(!$device->is_locked)

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'devices.lock',
                                        $device
                                    ) }}"
                                    style="flex:1"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn-danger"
                                        {{ !$isPaired ? 'disabled' : '' }}
                                    >
                                        🔒 Lock
                                    </button>

                                </form>

                            @else

                                {{-- UNLOCK DIRECTLY --}}

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'devices.unlock',
                                        $device
                                    ) }}"
                                    style="flex:1"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn-success"
                                    >
                                        🔓 Unlock
                                    </button>

                                </form>


                                {{-- Generate Code --}}

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'devices.generate-unlock-code',
                                        $device
                                    ) }}"
                                    style="flex:1"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn-primary"
                                    >
                                        🔑 Code দিন
                                    </button>

                                </form>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </div>

</div>


<script>

    /*
     * Refresh dashboard every 10 seconds
     * (badge + last-seen only, no full reload).
     */
    setInterval(function () {

        fetch(
            "{{ route('dashboard.status') }}",
            {
                headers: {
                    'Accept':
                        'application/json'
                }
            }
        )
        .then(response =>
            response.json()
        )
        .then(data => {

            if (!data.success) {
                return;
            }

            data.devices.forEach(
                function(device) {

                    const card =
                        document.getElementById(
                            'device-' +
                            device.id
                        );

                    if (!card) {
                        return;
                    }

                    const badge =
                        card.querySelector(
                            '.badge'
                        );

                    if (!badge) {
                        return;
                    }

                    if (!device.paired) {

                        badge.textContent =
                            'NOT PAIRED';

                        badge.className =
                            'badge badge-not-paired';

                    } else if (device.is_locked) {

                        badge.textContent =
                            'LOCKED';

                        badge.className =
                            'badge badge-locked';

                    } else {

                        badge.textContent =
                            'UNLOCKED';

                        badge.className =
                            'badge badge-unlocked';

                    }

                }
            );

        })
        .catch(function() {
            // Ignore polling errors.
        });

    }, 10000);

</script>

</body>

</html>