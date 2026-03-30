<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Időpont visszaigazolás</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 40px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08);">
                    
                    <tr>
                        <td height="6" style="background: linear-gradient(to right, #4c1d95, #7c3aed);"></td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 40px 30px 20px 30px;">
                            <h1 style="margin: 0; color: #1f2937; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; border-bottom: 2px solid #f3f4f6; display: inline-block; padding-bottom: 10px;">
                                {{ config('app.name') }}
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 40px 10px 40px; text-align: center;">
                            <h2 style="color: #111827; font-size: 22px; margin: 0; font-weight: 600;">Szia, {{ $details['name'] ?? 'Vendégünk' }}!</h2>
                            <p style="color: #6b7280; font-size: 16px; line-height: 1.6; margin-top: 15px;">
                                Köszönjük a bizalmadat! Az időpontodat sikeresen lefoglaltuk. Már nagyon várjuk a közös munkát!
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 40px 30px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #f3f4f6; border-radius: 12px; padding: 25px;">
                                <tr>
                                    <td style="padding-bottom: 15px;">
                                        <span style="color: #9ca3af; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Foglalási adatok</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" style="padding: 10px 0;"><span style="font-size: 20px;">✨</span></td>
                                                <td style="color: #374151; font-size: 15px;"><strong>Szolgáltatás:</strong> {{ $details['service'] ?? 'Nincs megadva' }}</td>
                                            </tr>
                                            <tr>
                                                <td width="40" style="padding: 10px 0;"><span style="font-size: 20px;">📅</span></td>
                                                <td style="color: #374151; font-size: 15px;"><strong>Időpont:</strong> {{ $details['start'] ?? 'Nincs megadva' }}</td>
                                            </tr>
                                            <tr>
                                                <td width="40" style="padding: 10px 0;"><span style="font-size: 20px;">👤</span></td>
                                                <td style="color: #374151; font-size: 15px;"><strong>Szakember:</strong> {{ $details['worker_name'] ?? 'Munkatársunk' }}</td>
                                            </tr>
                                            <tr>
                                                <td width="40" style="padding: 10px 0;"><span style="font-size: 20px;">📍</span></td>
                                                <td style="color: #374151; font-size: 15px;"><strong>Helyszín:</strong> Budapest, Magyarország</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 0 40px 40px 40px;">
                            <a href="{{ config('app.url') }}" style="background-color: #4c1d95; color: #ffffff; padding: 16px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 16px; display: inline-block; transition: background 0.3s ease;">
                                Weboldal felkeresése
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px 40px 40px; text-align: center;">
                            <p style="color: #9ca3af; font-size: 13px; margin: 0; line-height: 1.5;">
                                Kérdésed van? Válaszolj erre az emailre vagy hívj minket.<br>
                                <span style="color: #ef4444; font-weight: 600;">Lemondás:</span> legkésőbb 24 órával az időpont előtt.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #111827; padding: 30px 40px; text-align: center;">
                            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. Minden jog fenntartva.
                            </p>
                            <p style="color: #4b5563; font-size: 11px; margin-top: 10px;">
                                Ezt az üzenetet azért kaptad, mert időpontot foglaltál a rendszerünkben.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>