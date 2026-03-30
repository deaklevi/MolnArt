<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Időpont emlékeztető</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 40px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08);">
                    
                    <tr>
                        <td height="6" style="background: linear-gradient(to right, #7c3aed, #db2777);"></td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 40px 30px 10px 30px;">
                            <h1 style="margin: 0; color: #1f2937; font-size: 24px; font-weight: 800; text-transform: uppercase;">
                                Hamarosan találkozunk!
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 40px 30px 40px; text-align: center;">
                            <h2 style="color: #111827; font-size: 20px; margin: 0;">Kedves {{ $details['name'] ?? 'Vendégünk' }}!</h2>
                            <p style="color: #6b7280; font-size: 16px; line-height: 1.6; margin-top: 15px;">
                                Szeretnénk emlékeztetni, hogy a **holnapi napon** várunk téged a lefoglalt időpontodra. Kérjük, érkezz pár perccel korábban!
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px dashed #d1d5db; border-radius: 12px; padding: 20px; background-color: #fafafa;">
                                <tr>
                                    <td style="color: #374151; font-size: 15px; padding: 5px 0;">
                                        <strong>Mikor:</strong> Holnap, {{ $details['start'] ?? 'Nincs megadva' }} órakor
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #374151; font-size: 15px; padding: 5px 0;">
                                        <strong>Szolgáltatás:</strong> {{ $details['service'] ?? 'Nincs megadva' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #374151; font-size: 15px; padding: 5px 0;">
                                        <strong>Szakember:</strong> {{ $details['worker_name'] ?? 'Munkatársunk' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px 40px 40px; text-align: center;">
                            <p style="color: #9ca3af; font-size: 13px; margin-bottom: 20px;">
                                Amennyiben nem tudsz megjelenni, kérjük, minél előbb jelezd telefonon!
                            </p>
                            <a href="{{ config('app.url') }}" style="background-color: #111827; color: #ffffff; padding: 14px 28px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-block;">
                                Irány a weboldal
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #eeeeee;">
                            <p style="color: #9ca3af; font-size: 11px; margin: 0;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. Ez egy automatikus emlékeztető.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>