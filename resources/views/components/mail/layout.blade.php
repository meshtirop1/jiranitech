{{--
    Branded shell for every message the site sends.

    Written to the constraints of mail clients rather than of browsers: tables
    for layout, every rule inline, no webfont (Outlook and Gmail both ignore
    @font-face, so PT Sans is asked for and Arial catches it), and a 600px
    measure that survives every client at every width.

    The header is type, not an image. Gmail, Outlook and Apple Mail all block
    remote images by default on first receipt, and a logo that renders as a grey
    box is worse than a wordmark that always draws.
--}}
@props([
    'preheader' => null,
])

<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ config('company.legal_name') }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f8f8f8; -webkit-font-smoothing:antialiased;">

    {{-- The line the inbox shows beside the subject. Hidden in the message body,
         then padded so the client cannot pull the following copy up into it. --}}
    @if ($preheader)
        <div style="display:none; font-size:1px; color:#f8f8f8; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden;">
            {{ $preheader }}
            {!! str_repeat('&#8199;&#65279;&#847; ', 30) !!}
        </div>
    @endif

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f8f8f8;">
        <tr>
            <td align="center" style="padding:24px 12px;">

                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="width:600px; max-width:600px; background-color:#ffffff;">

                    {{-- Masthead: the same cyan bar and wordmark the site header uses. --}}
                    <tr>
                        <td style="background-color:#0c6e92; padding:20px 32px;">
                            <a href="{{ url('/') }}" style="text-decoration:none;">
                                <span style="font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:20px; font-weight:bold; color:#ffffff;">Jiranisoko</span><span style="font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:20px; color:#ffffff;">&nbsp;Tech</span>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    {{-- Footer carries the same registered particulars as the site.
                         A recipient checking a supplier finds them here. --}}
                    <tr>
                        <td style="border-top:1px solid #e5e5e5; padding:24px 32px 28px;">
                            <p style="margin:0 0 8px; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:13px; line-height:19px; color:#6e6e6e;">
                                <strong style="color:#4e4e4e;">{{ config('company.legal_name') }}</strong><br>
                                The enterprise technology division of {{ config('company.parent.name') }}.
                            </p>
                            <p style="margin:0 0 8px; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:13px; line-height:19px; color:#6e6e6e;">
                                Registered as {{ config('company.parent.name') }}, company no. {{ config('company.parent.registration_number') }}@if (config('company.parent.tax_pin')) &middot; KRA PIN {{ config('company.parent.tax_pin') }}@endif<br>
                                @if (config('company.registered_address'))
                                    {{ collect([config('company.registered_address'), config('company.city'), config('company.country')])->filter()->implode(', ') }}
                                @endif
                            </p>
                            <p style="margin:0; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:13px; line-height:19px; color:#6e6e6e;">
                                <a href="{{ url('/') }}" style="color:#0c6e92; text-decoration:none;">{{ parse_url(config('app.url'), PHP_URL_HOST) }}</a>
                                @if (config('company.email.enquiries'))
                                    &middot; <a href="mailto:{{ config('company.email.enquiries') }}" style="color:#0c6e92; text-decoration:none;">{{ config('company.email.enquiries') }}</a>
                                @endif
                            </p>
                        </td>
                    </tr>
                </table>

                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="width:600px; max-width:600px;">
                    <tr>
                        <td style="padding:16px 32px 0; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:12px; line-height:18px; color:#8a8a8a;">
                            {{ $footnote ?? '' }}
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
