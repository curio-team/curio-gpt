<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Gebruiksfactuur</title>
    <style>
        body {
            font-family: Georgia, 'Times New Roman', serif;
            color: #111827;
            font-size: 11px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .page {
            padding: 28px 34px;
            max-width: 820px;
            margin: 0 auto;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .brand {
            font-size: 23px;
            font-weight: 700;
            letter-spacing: 0.4px;
            color: #111827;
        }

        .brand-sub {
            font-size: 10px;
            color: #6b7280;
            margin-top: 1px;
            font-style: italic;
        }

        .logo svg {
            width: 46px;
            height: 46px;
            opacity: 0.85;
        }

        .invoice-title-block {
            text-align: right;
            margin-top: 4px;
        }

        .invoice-title {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.8px;
        }

        .invoice-meta {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.5;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .meta-table td {
            width: 50%;
            border: 1px solid #e5e7eb;
            padding: 10px 12px;
            vertical-align: top;
        }

        .section-title {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .meta-name {
            font-size: 13px;
            font-weight: 700;
        }

        .meta-muted,
        .meta-easter {
            font-size: 10px;
            line-height: 1.45;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            table-layout: fixed;
        }

        .items thead tr {
            border-top: 2px solid #111827;
            border-bottom: 1px solid #d1d5db;
        }

        .items th {
            padding: 6px 7px;
            text-align: left;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #6b7280;
        }

        .items td {
            padding: 6px 7px;
            vertical-align: top;
            font-size: 10px;
            line-height: 1.25;
            word-wrap: break-word;
        }

        .items td.excuse {
            font-style: italic;
            color: #9ca3af;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .unpriced-note {
            margin-top: 4px;
            margin-bottom: 10px;
            font-size: 9px;
            color: #9ca3af;
            font-style: italic;
        }

        /* ── Summary table ── */
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .summary td {
            padding: 8px 10px;
            border-bottom: 1px solid #f3f4f6;
        }

        .summary .label {
            color: #6b7280;
            font-size: 11px;
            width: 72%;
        }

        .summary .value {
            text-align: right;
            font-size: 12px;
        }

        .summary .total-row td {
            border-top: 2px solid #111827;
            border-bottom: 2px solid #111827;
            font-weight: 700;
            font-size: 14px;
            color: #111827;
        }

        /* ── Stamp ── */
        .stamp-wrap { margin-top: 10px; }

        .stamp {
            display: inline-block;
            border: 2.5px solid #b91c1c;
            color: #b91c1c;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 6px 14px;
            transform: rotate(-7deg);
            opacity: 0.8;
        }

        /* ── Payment terms ── */
        .payment-section {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }

        .payment-left, .payment-right {
            display: table-cell;
            vertical-align: top;
        }

        .payment-left { width: 58%; padding-right: 24px; }
        .payment-right { width: 42%; text-align: right; }

        .payment-note {
            font-size: 11px;
            color: #374151;
            line-height: 1.8;
            margin-top: 10px;
        }

        .payment-note em { color: #6b7280; }

        .sig-title {
            font-family: Georgia, serif;
            font-size: 22px;
            font-style: italic;
            letter-spacing: 1px;
            margin: 4px 0;
        }

        .sig-sub { font-size: 10px; color: #9ca3af; line-height: 1.8; }

        /* ── Divider ── */
        hr { border: none; border-top: 1px solid #e5e7eb; margin: 18px 0; }
        hr.thick { border-top: 1px solid #d1d5db; }

        /* ── Fine print ── */
        .footnotes-grid {
            display: table;
            width: 100%;
            margin-bottom: 14px;
        }

        .fn-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .fn-col:last-child { padding-right: 0; }

        .fine-print {
            font-size: 10px;
            color: #9ca3af;
            line-height: 1.8;
        }

        .fine-print strong { color: #6b7280; }

        /* ── Footer bar ── */
        .footer-bar {
            display: table;
            width: 100%;
        }

        .footer-left, .footer-right {
            display: table-cell;
            vertical-align: middle;
        }

        .footer-right { text-align: right; }

        .status-pills { font-size: 9px; color: #9ca3af; letter-spacing: 0.3px; }
        .footer-brand { font-size: 10px; color: #9ca3af; line-height: 1.8; }
        .footer-brand em { font-style: italic; }

        /* ── Unpriced notice ── */
        .unpriced-note {
            margin-top: 6px;
            font-size: 10px;
            color: #9ca3af;
            font-style: italic;
        }

        /* ── Print optimisation ── */
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { padding: 28px 36px; }
        }
    </style>
</head>
<body>
<div class="page">

    <!-- ══ HEADER ══ -->
    <table class="header">
        <tr>
            <td>
                <div class="brand">{{ config('app.name', 'CurioGPT') }}</div>
                <div class="brand-sub">Afdeling AI-Gebruiksfacturatie &mdash; Opgericht op het moment dat jij hulp nodig had</div>
            </td>
            <td class="logo-cell">
                <!-- Brain logo -->
                <div class="logo">
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="30" cy="30" r="27" stroke="#111827" stroke-width="1.5" fill="none"/>
                        <ellipse cx="23" cy="26" rx="9.5" ry="12" stroke="#111827" stroke-width="1.3" fill="none"/>
                        <ellipse cx="37" cy="26" rx="9.5" ry="12" stroke="#111827" stroke-width="1.3" fill="none"/>
                        <path d="M23 14 C25 11, 30 11, 30 14" stroke="#111827" stroke-width="1" fill="none"/>
                        <path d="M30 14 C30 11, 35 11, 37 14" stroke="#111827" stroke-width="1" fill="none"/>
                        <line x1="30" y1="14" x2="30" y2="38" stroke="#111827" stroke-width="1" stroke-dasharray="2,2"/>
                        <path d="M13.5 26 C11 28, 12 33, 16 33" stroke="#111827" stroke-width="1" fill="none"/>
                        <path d="M46.5 26 C49 28, 48 33, 44 33" stroke="#111827" stroke-width="1" fill="none"/>
                        <path d="M23 38 C23 42, 27 44, 30 44 C33 44, 37 42, 37 38" stroke="#111827" stroke-width="1.2" fill="none"/>
                        <circle cx="21" cy="22" r="1.8" fill="#111827" opacity="0.35"/>
                        <circle cx="39" cy="22" r="1.8" fill="#111827" opacity="0.35"/>
                    </svg>
                </div>
                <div class="invoice-title-block">
                    <div class="invoice-title">Factuur</div>
                    <div class="invoice-meta">
                        <strong>#</strong> {{ $invoiceNumber }}<br>
                        <strong>Datum:</strong> {{ $invoiceDate->format('d-m-Y') }}<br>
                        <em>(Verdacht dicht bij de toetsdatum)</em>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- ══ META BOXES ══ -->
    <table class="meta-table">
        <tr>
            <td>
                <div class="section-title">Factureren aan</div>
                <div class="meta-name">{{ $user->name ?? __('app.common.unknown_student') }}</div>
                @if (!empty($user->email))
                    <div class="meta-muted">{{ $user->email }}</div>
                @endif
                <div class="meta-muted">Gebruikers-ID: {{ $user->id }}</div>
                <div class="meta-easter">Bekende alias: &ldquo;Heeft echt gestudeerd&rdquo;</div>
            </td>
            <td>
                <div class="section-title">Gebruiksperiode</div>
                @if ($periodStart && $periodEnd)
                    <div>{{ $periodStart->format('d-m-Y H:i') }} &ndash; {{ $periodEnd->format('d-m-Y H:i') }}</div>
                @else
                    <div>{{ __('app.teacher.usage.no_usage_yet') }}</div>
                @endif
                <div class="meta-muted">Gegenereerd door {{ config('app.name', 'CurioGPT') }}</div>
                <div class="meta-easter">Eindtijd: 17:00 uur &mdash; want dan gaat de bel&sup3;</div>
            </td>
        </tr>
    </table>

    <!-- ══ LINE ITEMS ══ -->
    <table class="items">
        <thead>
        <tr>
            <th style="width: 20%">Model</th>
            <th>Opgegeven reden</th>
            <th class="text-right">Totaal tokens</th>
            <th class="text-right">Gesch. kosten (USD)</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($lineItems as $item)
            <tr>
                <td>{{ $item['model'] }}</td>
                <td class="excuse">
                    @php
                        $excuses = [
                            'gpt-5'      => '&ldquo;Ik ging voor de premium twijfel&rdquo;',
                            'gpt-5-mini' => '&ldquo;Budgetcontrole, je kent het wel&rdquo;',
                            'gpt-4o'     => '&ldquo;Die gaf een ander antwoord&rdquo; &sup1;',
                            'gpt-4o-mini'=> '&ldquo;Voor een mini-vraagje moest ik gewoon even vreemdgaan&rdquo;',
                        ];
                                                $excuse = $excuses[$item['model']] ?? '&ldquo;Geen commentaar&rdquo;';
                    @endphp
                    {!! $excuse !!}
                </td>
                <td class="text-right">{{ number_format($item['total_tokens'], 0, ',', '.') }}</td>
                <td class="text-right">
                    @if (is_null($item['estimated_cost_usd']))
                        {{ __('app.common.price_missing') }}
                    @else
                        ${{ number_format($item['estimated_cost_usd'], 4, ',', '.') }}
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">{{ __('app.teacher.usage.no_usage_yet') }}</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    @if ($hasUnpricedItems)
        <div class="unpriced-note">
            &sup2; Voor een of meer modellen ontbreekt de prijsconfiguratie. Het totaal omvat alleen de modellen met bekende prijs.
        </div>
    @endif

    <!-- ══ SUMMARY ══ -->
    <table class="summary" style="margin-top: 14px;">
        <tr>
            <td class="label">Totaal tokens</td>
            <td class="value">{{ number_format($usage['total_tokens'], 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td class="label">Geschat factuurbedrag</td>
            <td class="value">${{ number_format($subtotalUsd, 4, ',', '.') }}</td>
        </tr>
    </table>

    <!-- ══ PAYMENT + SIGNATURE ══ -->
    <div class="payment-section">
        <div class="payment-left">
            <div class="stamp-wrap">
                <span class="stamp">Betaling niet vereist</span>
            </div>
            <div class="payment-note">
                Wij accepteren geen contant geld, pinpas, crypto of tranen.<br><br>
                Je kunt deze schuld vereffenen door ons een bezoekje te brengen
                als je straks in de &lsquo;echte wereld&rsquo; rondloopt &mdash;
                een kopje koffie, een kaartje, of gewoon even laten weten hoe het met je gaat
                is meer dan genoeg.<br><br>
                <em>We duimen voor je. Dat deden we altijd al.</em>
            </div>
        </div>
        <div class="payment-right">
            <div class="section-title" style="text-align:right; margin-top:10px;">Geautoriseerd door</div>
            <div class="sig-title">De AI</div>
            <div class="sig-sub">
                Chief Token Officer<br>
                {{ config('app.name', 'CurioGPT') }} B.V.<br>
                <em>Elektronisch ondertekend, uiteraard</em>
            </div>
        </div>
    </div>

    <hr class="thick">

    <!-- ══ FINE PRINT ══ -->
    <div class="footnotes-grid">
        <div class="fn-col">
            <div class="section-title">Opmerkingen &amp; disclaimer</div>
            <div class="fine-print">
                <strong>&middot;</strong> We zien je wel, ChatGPT-gebruiker. We oordelen niet &mdash; zo heel erg.<br>
                <strong>&middot;</strong> Tokenaantal exclusief de tokens voor driemaal hetzelfde vragen: &ldquo;Weet je het zeker?&rdquo;<br>
                <strong>&middot;</strong> Deze factuur is een aandenken, geen aanmaning. Juridische stappen worden niet ondernomen. Waarschijnlijk.<br>
                <strong>&middot;</strong> De AI heeft je toets technisch gezien niet gemaakt. Hij heeft slechts&hellip; je denkproces begeleid. Intensief.
            </div>
        </div>
        <div class="fn-col">
            <div class="section-title">Diplomavoorwaarden &#10022;</div>
            <div class="fine-print">
                Door je diploma in ontvangst te nemen ga je akkoord met: (a)&nbsp;af en toe afvragen of je eigenlijk wel iets hebt geleerd,
                (b)&nbsp;{{ config('app.name', 'CurioGPT') }} aanbevelen aan precies &eacute;&eacute;n jongere medestudent,
                (c)&nbsp;je docent nooit over deze factuur vertellen.<br><br>
                <span style="font-size:9px; opacity:0.7; font-style:italic;">
                    Dit document is gegenereerd met dezelfde AI die jou heeft geholpen slagen. Ja, echt.
                </span>
            </div>
        </div>
    </div>

    <hr>

    <!-- ══ FOOTER BAR ══ -->
    <div class="footer-bar">
        <div class="footer-left">
            <div class="status-pills">
                Effici&euml;ntiescore: 1 brein + 1 AI = 1,3 brein
                &nbsp;&bull;&nbsp; Nieuwsgierigheidsindex: HOOG
                &nbsp;&bull;&nbsp; Spijt-index: IN BEHANDELING
                &nbsp;&bull;&nbsp; Bekendheid bij toekomstige werkgever: VERTROUWELIJK
            </div>
        </div>
        <div class="footer-right">
            <div class="footer-brand">
                {{ config('app.name', 'CurioGPT') }} &middot; AI-Gebruiksfacturatie<br>
                <em>Bedankt voor uw gebruik. Nu op naar grote dingen.</em>
            </div>
        </div>
    </div>

</div>
</body>
</html>
