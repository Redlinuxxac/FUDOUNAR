<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'FUDOUNAR'))</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 24px;
            color: #1f2937;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #ffffff;
            border-bottom: 3px solid #1e3a8a;
            padding: 28px 24px 22px;
            text-align: center;
        }
        .content {
            padding: 32px 24px;
        }
        .footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 24px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            line-height: 1.6;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container">
        @include('emails.partials.header')

        <div class="content">
            @yield('content')
        </div>

        @include('emails.partials.footer')
    </div>
</body>
</html>
