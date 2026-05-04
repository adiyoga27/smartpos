<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Thermal - {{ $transaction->invoice_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: 80mm auto; margin: 2mm; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            width: 76mm;
            color: #000;
            line-height: 1.3;
        }
        .center { text-align: center; }
        .text-xs { font-size: 9px; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 4px 0; }
        .divider-solid { border-top: 1px solid #000; margin: 4px 0; }
        .row { display: flex; justify-content: space-between; }
        .col-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 9px; border-bottom: 1px dashed #000; padding: 2px 0; }
        td { padding: 2px 0; font-size: 10px; vertical-align: top; }
        .qty { width: 30px; text-align: center; }
        .price { text-align: right; }
        .total { text-align: right; font-weight: bold; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="center bold">{{ $location->name ?? config('app.name') }}</div>
    <div class="center text-xs">{{ $location->address ?? '' }}</div>
    <div class="divider"></div>

    <div class="text-xs">
        <div class="row"><span>No:</span> <span class="bold">{{ $transaction->invoice_no }}</span></div>
        <div class="row"><span>Tgl:</span> <span>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</span></div>
        <div class="row"><span>Kasir:</span> <span>{{ $transaction->creator?->name ?? '-' }}</span></div>
        <div class="row"><span>Pelanggan:</span> <span>{{ $transaction->contact?->full_name ?? 'Umum' }}</span></div>
    </div>
    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="qty">Qty</th>
                <th class="price">Harga</th>
                <th class="price">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $item)
                <tr>
                    <td>{{ $item->product?->name ?? '-' }}{{ $item->variation?->name ? ' - '.$item->variation->name : '' }}</td>
                    <td class="qty">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                    <td class="price">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="price">{{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="divider"></div>

    <div class="text-xs">
        <div class="row">
            <span>Subtotal</span>
            <span>{{ number_format($transaction->items->sum(fn ($i) => $i->quantity * $i->unit_price), 0, ',', '.') }}</span>
        </div>
        @if($transaction->discount_amount > 0)
            <div class="row">
                <span>Diskon{{ $transaction->discount_type === 'percentage' ? ' ('.$transaction->discount_type.'%)' : '' }}</span>
                <span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($transaction->tax_amount > 0)
            <div class="row">
                <span>Pajak{{ $transaction->tax?->name ? ' ('.$transaction->tax->name.')' : '' }}</span>
                <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>
    <div class="divider-solid"></div>
    <div class="row bold" style="font-size: 12px;">
        <span>TOTAL</span>
        <span class="col-right">Rp {{ number_format($transaction->final_total, 0, ',', '.') }}</span>
    </div>

    @php $totalPaid = $transaction->payments->sum('amount'); @endphp
    <div class="row">
        <span>Dibayar</span>
        <span>Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
    </div>
    @php $change = $totalPaid - $transaction->final_total; @endphp
    @if($change > 0)
        <div class="row bold">
            <span>Kembalian</span>
            <span>Rp {{ number_format($change, 0, ',', '.') }}</span>
        </div>
    @endif

    @if($transaction->payments->isNotEmpty())
        <div class="divider"></div>
        <div class="text-xs">
            @foreach($transaction->payments as $payment)
                <div class="row">
                    <span>{{ match($payment->method) {'cash' => 'Tunai', 'card' => 'Kartu', 'bank_transfer' => 'Transfer', default => 'Lainnya'} }}</span>
                    <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="divider"></div>
    <div class="center text-xs">{{ $location->footer_text ?? 'Terima kasih telah berbelanja' }}</div>
    <div class="center text-xs">{{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>
