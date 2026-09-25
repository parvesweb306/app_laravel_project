<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Download Child Guard
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f7fb;
            font-family:
                Arial,
                Helvetica,
                sans-serif;
        }

        .box {
            width: 100%;
            max-width: 500px;
            margin: 20px;
            background: white;
            padding: 35px;
            border-radius: 22px;
            text-align: center;
            box-shadow:
                0 15px 50px
                rgba(0,0,0,.08);
        }

        .icon {
            width: 90px;
            height: 90px;
            margin: auto;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #4f46e5;
            color: white;
            font-size: 45px;
        }

        h1 {
            margin-top: 25px;
            margin-bottom: 10px;
        }

        p {
            color: #6b7280;
            line-height: 1.6;
        }

        .device {
            margin-top: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 12px;
        }

        .code {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: 7px;
            color: #3730a3;
            margin-top: 7px;
        }

        .download {
            display: block;
            margin-top: 25px;
            padding: 16px;
            border-radius: 12px;
            background: #4f46e5;
            color: white;
            text-decoration: none;
            font-size: 17px;
            font-weight: 700;
            border: none;
            width: 100%;
            font-family: inherit;
            cursor: pointer;
        }

        .download:hover {
            background: #4338ca;
        }

        .note {
            margin-top: 20px;
            font-size: 13px;
        }

        .copied {
            margin-top: 12px;
            font-size: 13px;
            color: #16a34a;
            font-weight: 700;
            display: none;
        }

    </style>

</head>

<body>

<div class="box">

    <div class="icon">
        🛡️
    </div>

    <h1>
        Child Guard
    </h1>

    <p>
        Download the Child Guard application
        for the child device.
    </p>


    <div class="device">

        <div>
            Device
        </div>

        <strong>
            {{ $device->device_name }}
        </strong>

        <br><br>

      

    </div>


    <button
        type="button"
        class="download"
        id="downloadBtn"
        data-code="{{ $device->pairing_code }}"
        data-url="{{ route(
            'devices.download-apk',
            $device->pairing_code
        ) }}"
    >

        ⬇️ Download APK

    </button>

    <div class="copied" id="copiedMsg">
        ✅ Pairing code auto-copied — install করার পর app নিজে থেকেই connect হয়ে যাবে
    </div>


    <p class="note">

        APK install করার পরে application open করুন —
        code নিজে থেকেই বসে যাবে। না বসলে উপরের
        Pairing Code ম্যানুয়ালি লিখুন।

    </p>

</div>

<script>

    document.getElementById('downloadBtn')
        .addEventListener('click', async function () {

            var code = this.dataset.code;
            var url = this.dataset.url;

            try {
                await navigator.clipboard.writeText(code);

                document.getElementById('copiedMsg')
                    .style.display = 'block';
            } catch (e) {
                // Clipboard permission না থাকলেও
                // download চলবে, শুধু auto-fill হবে না।
            }

            // ছোট delay দিয়ে তারপর download শুরু,
            // যাতে clipboard write আগে শেষ হয়।
            setTimeout(function () {
                window.location.href = url;
            }, 150);

        });

</script>

</body>

</html>