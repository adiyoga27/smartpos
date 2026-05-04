<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice - {{ $transaction->invoice_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4; margin: 15mm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1f2937;
            line-height: 1.5;
        }
        .header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px; }
        .company h2 { font-size: 20px; color: #2563eb; margin-bottom: 4px; }
        .company p { font-size: 11px; color: #6b7280; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 28px; color: #1f2937; font-weight: 700; }
        .invoice-title .no { font-size: 14px; color: #6b7280; margin-top: 4px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; padding: 16px; background: #f9fafb; border-radius: 8px; }
        .info-grid label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
        .info-grid .value { font-weight: 600; font-size: 13px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead th { background: #f3f4f6; padding: 10px 12px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; border-bottom: 2px solid #d1d5db; }
        thead th.right { text-align: right; }
        thead th.center { text-align: center; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        tbody td.right { text-align: right; }
        tbody td.center { text-align: center; }

        .totals { margin-left: auto; width: 320px; }
        .totals .row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
        .totals .row.grand { border-top: 2px solid #1f2937; padding-top: 10px; margin-top: 4px; font-weight: 700; font-size: 16px; }

        .payment-section { margin-top: 24px; padding: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; }
        .payment-section h3 { font-size: 14px; color: #16a34a; margin-bottom: 8px; }

        .footer { margin-top: 32px; text-align: center; font-size: 11px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 16px; }

        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .status-badge.paid { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .status-badge.due { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="header">
        <div class="company">
            <h2>{{ $location->name ?? config('app.name') }}</h2>
            <p>{{ $location->landmark ?? '' }}</p>
            <p>{{ $location->city ?? '' }}{{ $location->state ? ', '.$location->state : '' }}</p>
            <p>{{ $location->mobile ?? '' }}</p>
        </div>
        <div class="invoice-title">
            <h1>INVOICE</h1>
            <div class="no">{{ $transaction->invoice_no }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div>
            <label>Tanggal</label>
            <div class="value">{{ $transaction->transaction_date->format('d/m/Y H:i') }}</div>
        </div>
        <div>
            <label>Status</label>
            <div><span class="status-badge {{ $transaction->payment_status }}">{{ $transaction->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas' }}</span></div>
        </div>
        <div>
            <label>Pelanggan</label>
            <div class="value">{{ $transaction->contact?->full_name ?? 'Umum / Walk-in' }}</div>
        </div>
        <div>
            <label>Kasir</label>
            <div class="value">{{ $transaction->creator?->name ?? '-' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th class="center">Qty</th>
                <th class="right">Harga Satuan</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product?->name ?? '-' }}</strong>
                        @if($item->variation?->name)
                            <br><span style="color: #6b7280; font-size: 11px;">{{ $item->variation->name }}</span>
                        @endif
                    </td>
                    <td class="center">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                    <td class="right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($transaction->items->sum(fn ($i) => $i->quantity * $i->unit_price), 0, ',', '.') }}</span>
        </div>
        @if($transaction->discount_amount > 0)
            <div class="row">
                <span>Diskon</span>
                <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($transaction->tax_amount > 0)
            <div class="row">
                <span>Pajak{{ $transaction->tax?->name ? ' ('.$transaction->tax->name.')' : '' }}</span>
                <span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="row grand">
            <span>Grand Total</span>
            <span>Rp {{ number_format($transaction->final_total, 0, ',', '.') }}</span>
        </div>
    </div>

    @if($transaction->payments->isNotEmpty())
        <div class="payment-section">
            <h3>Informasi Pembayaran</h3>
            @foreach($transaction->payments as $payment)
                <div class="row" style="display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0;">
                    <span>{{ match($payment->method) {'cash' => 'Tunai', 'card' => 'Kartu', 'bank_transfer' => 'Transfer Bank', default => 'Lainnya'} }}</span>
                    <span>{{ $payment->paid_on }}</span>
                    <span style="font-weight: 600;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            @endforeach
            @php
                $totalPaid = $transaction->payments->sum('amount');
                $change = $totalPaid - $transaction->final_total;
            @endphp
            @if($change > 0)
                <div class="row" style="display: flex; justify-content: space-between; padding: 4px 0; color: #16a34a; font-weight: 600;">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($change, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
    @endif

    <div class="footer">
        {{ $location->footer_text ?? 'Terima kasih telah berbelanja - '.config('app.name') }}
        <br>Dicetak: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
