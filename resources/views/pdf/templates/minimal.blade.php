<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoiceNumber }}</title>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        html { font-family: 'Inter', sans-serif; }
        @supports (font-variation-settings: normal) {
            html { font-family: 'Inter var', sans-serif; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/invoices-minimal.css') }}">
</head>
<body class="bg-gray-50">
    <div class="max-w-2xl mx-auto bg-white p-10 shadow-lg">
        <div class="flex justify-between items-start mb-10 pb-6 border-b-2 border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $invoice->seller->name }}</h1>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 uppercase">Invoice</p>
                <p class="text-3xl font-bold text-gray-900">#{{ $invoice->invoiceNumber }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-10">
            <div>
                <h3 class="text-xs uppercase font-semibold text-gray-500 mb-3">From</h3>
                <p class="font-semibold text-gray-900">{{ $invoice->seller->name }}</p>
                @if($invoice->seller->address)
                    <p class="text-sm text-gray-600">{{ $invoice->seller->address }}</p>
                @endif
                @if($invoice->seller->email)
                    <p class="text-sm text-gray-600">{{ $invoice->seller->email }}</p>
                @endif
                @if($invoice->seller->vatNumber)
                    <p class="text-sm text-gray-600">VAT: {{ $invoice->seller->vatNumber }}</p>
                @endif
            </div>

            <div>
                <h3 class="text-xs uppercase font-semibold text-gray-500 mb-3">To</h3>
                <p class="font-semibold text-gray-900">{{ $invoice->buyer->name }}</p>
                @if($invoice->buyer->address)
                    <p class="text-sm text-gray-600">{{ $invoice->buyer->address }}</p>
                @endif
                @if($invoice->buyer->email)
                    <p class="text-sm text-gray-600">{{ $invoice->buyer->email }}</p>
                @endif
                @if($invoice->buyer->vatNumber)
                    <p class="text-sm text-gray-600">VAT: {{ $invoice->buyer->vatNumber }}</p>
                @endif
            </div>
        </div>

        <table class="w-full mb-10 text-sm">
            <thead class="bg-gray-50 border-t border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 text-xs uppercase">Description</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs uppercase">Unit Price</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs uppercase">Qty</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs uppercase">Tax</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-xs uppercase">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-900">{{ $item->description }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($item->unitPrice, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($item->tax * 100, 0) }}%</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ number_format($item->getTotal(), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end mb-10">
            <div class="w-80">
                @if($invoice->getTotalDiscount() > 0)
                    <div class="flex justify-between py-2 px-4 border-b border-gray-200 text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="text-gray-900">{{ number_format($invoice->getSubtotal(), 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 px-4 border-b border-gray-200 text-sm">
                        <span class="text-gray-600">Discount:</span>
                        <span class="text-gray-900">-{{ number_format($invoice->getTotalDiscount(), 2) }}</span>
                    </div>
                @endif
                @if($invoice->getTotalTax() > 0)
                    <div class="flex justify-between py-2 px-4 border-b border-gray-200 text-sm">
                        <span class="text-gray-600">Tax:</span>
                        <span class="text-gray-900">{{ number_format($invoice->getTotalTax(), 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-3 px-4 border-t-2 border-b-2 border-gray-900 bg-gray-50">
                    <span class="font-bold text-gray-900">Total:</span>
                    <span class="font-bold text-lg text-gray-900">{{ number_format($invoice->getTotal(), 2) }} {{ $invoice->currency }}</span>
                </div>
            </div>
        </div>

        @if($invoice->notes)
            <div class="bg-gray-50 p-4 rounded border border-gray-200 mb-10 text-sm text-gray-700">
                <strong class="text-gray-900">Notes:</strong><br>
                {{ $invoice->notes }}
            </div>
        @endif

        <div class="text-center text-sm text-gray-500 pt-6 border-t border-gray-200">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>