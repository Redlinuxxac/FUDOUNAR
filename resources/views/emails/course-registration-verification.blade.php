<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activa tu reserva de cupo - FUDOUNAR</title>
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
        .greeting {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #111827;
        }
        .course-box {
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .course-box h2 {
            margin: 0 0 6px;
            font-size: 18px;
            color: #1e40af;
        }
        .course-box p {
            margin: 0;
            font-size: 13px;
            color: #4b5563;
        }
        .notice-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 28px;
        }
        .notice-box p {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            color: #1e3a8a;
        }
        .button-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            padding: 14px 32px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }
        .recaudos {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 24px;
        }
        .recaudos h3 {
            margin: 0 0 10px;
            font-size: 14px;
            font-weight: 700;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .recaudos ul {
            margin: 0;
            padding-left: 20px;
            font-size: 13px;
            color: #78350f;
            line-height: 1.6;
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
        .link-fallback {
            word-break: break-all;
            font-size: 12px;
            color: #6b7280;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        @include('emails.partials.header', ['headerBadge' => 'Formación Presencial'])

        <div class="content">
            <div class="greeting">¡Hola, {{ $registration->full_name }}!</div>
            <p style="font-size: 15px; line-height: 1.6; color: #374151;">
                Hemos recibido tu solicitud de inscripción presencial. Para asegurar tu puesto en el aula y generar tu comprobante digital, debes validar tu correo electrónico pulsando el botón a continuación:
            </p>

            <div class="course-box">
                <h2>{{ $course->title }}</h2>
                <p>Modalidad: <strong>Presencial</strong> | Duración: <strong>{{ $course->duration }} horas</strong></p>
            </div>

            <div class="button-wrapper">
                <a href="{{ $verificationUrl }}" class="btn" target="_blank">
                    Validar mi participación y reservar cupo
                </a>
            </div>

            <div class="notice-box">
                <p>
                    ⚠️ <strong>Plazo de reserva temporal:</strong> Una vez que valides tu correo, tendrás un plazo de <strong>{{ $reservationDays }} días continuos</strong> para presentarte en nuestra sede física y formalizar tu inscripción. Transcurrido ese tiempo, el cupo será liberado automáticamente para otro estudiante de la lista de espera.
                </p>
            </div>

            <div class="recaudos">
                <h3>Recaudos necesarios para formalizar en sede:</h3>
                <ul>
                    <li>Original y fotocopia del Documento de Identidad (Cédula / DNI).</li>
                    <li>Código de reserva que obtendrás al validar el correo.</li>
                    <li>Soporte de pago o arancel administrativo (en caso de aplicar).</li>
                </ul>
            </div>

            <p class="link-fallback">
                Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                <a href="{{ $verificationUrl }}" style="color: #2563eb;">{{ $verificationUrl }}</a>
            </p>
        </div>

        @include('emails.partials.footer')
    </div>
</body>
</html>
