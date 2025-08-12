<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Print QR Code - {{ $room->name }}</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
        }

        @font-face {
            font-family: 'Anton';
            src: url('{{ asset('font/Anton-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }


        body {
            font-family: 'Anton', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 60px 20px;
            background: linear-gradient(135deg, #e6f0ff, #ffffff);
            text-align: center;
            -webkit-print-color-adjust: exact;
            user-select: none;
        }

        ::selection {
            background-color: #157ee1;
            color: white;
        }

        .print-container {
            max-width: 440px;
            margin: 0 auto;
            background: white;
            padding: 50px 35px 60px;
            border-radius: 28px;
            box-shadow: 0 14px 40px rgba(21, 126, 225, 0.3);
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            gap: 36px;
            position: relative;
        }

        /* Print button style */
        .print-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #157ee1;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 5px 14px rgba(21, 126, 225, 0.6);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            user-select: none;
            z-index: 10;
            letter-spacing: 1.2px;
        }

        .print-btn:hover {
            background-color: #0f5ac1;
            box-shadow: 0 7px 18px rgba(15, 90, 193, 0.85);
        }

        /* Top title */
        .top-text {
            font-family: 'Anton', sans-serif;
            font-weight: 900;
            font-size: clamp(1rem, 5vw, 3.2rem);
            color: #157ee1;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            padding-bottom: 6px;
            text-shadow: 0 2px 6px rgba(21, 126, 225, 0.4);
            user-select: none;
            white-space: nowrap;
            /* keep it on one line */
            overflow: hidden;
            /* hide anything too long */
            text-overflow: ellipsis;
            /* show "..." if too long */
        }

        /* QR container */
        .qr-code {
            width: 330px;
            height: 330px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 28px;
            border: 7px solid #157ee1;
            box-shadow:
                0 0 25px 4px rgba(21, 126, 225, 0.35),
                inset 0 0 14px 2px rgba(21, 126, 225, 0.2);
            transition: box-shadow 0.3s ease;
        }

        .qr-code:hover {
            box-shadow:
                0 0 40px 6px rgba(21, 126, 225, 0.55),
                inset 0 0 18px 4px rgba(21, 126, 225, 0.3);
        }

        .qr-code img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 16px;
            display: block;
            margin: 0 auto;
            user-select: none;
        }

        /* Phrase text */
        .scan-phrase {
            font-family: 'Anton', sans-serif;
            font-size: 1.4rem;
            margin-top: 36px;
            letter-spacing: 1.1px;
            user-select: text;
            line-height: 1.5;
            max-width: 400px;
        }

        .scan-phrase a {
            color: #157ee1;
            text-decoration: none;
            font-weight: 600;
        }

        /* Print adjustments */
        @media print {
            body {
                background: white;
                padding: 0;
                user-select: none;
            }

            .print-container {
                box-shadow: none;
                border: 3px solid #157ee1;
                margin: 0 auto;
                padding: 20px 20px 30px;
                position: static;
            }

            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="print-container" role="main" aria-label="QR Code for {{ $room->name }}">
        <button class="print-btn" onclick="window.print()" aria-label="Print QR Code">Print</button>

        <div class="top-text" aria-hidden="true">{{ $room->name }}</div>

        <div class="qr-code" aria-label="QR code image">
            <img src="{{ asset('storage/' . $room->qr_code_path) }}" alt="QR Code for {{ $room->name }}">
        </div>

        <div class="scan-phrase p-4 bg-gray-50 rounded border border-gray-300 max-w-md mx-auto text-center"
            aria-label="Scan instructions">
            Visit
            <a href="{{ config('app.url') }}" target="_blank">
                {{ config('app.url') }}
            </a>
            and scan the QR Code above to know more about this office.
        </div>

    </div>
</body>

</html>
