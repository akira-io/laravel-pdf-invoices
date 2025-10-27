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
    <link rel="stylesheet" href="{{ asset('css/invoices-branded.css') }}">
</head>
<body class="bg-gray-100">
    <div class="max-w-3xl mx-auto bg-white shadow-2xl overflow-hidden">
        <div class="border-b-4 border-blue-600 px-12 py-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $invoice->seller->name }}</h1>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        @if($invoice->seller->address)
                            {{ $invoice->seller->address }}<br>
                        @endif
                        @if($invoice->seller->vatNumber)
                            VAT ID: {{ $invoice->seller->vatNumber }}<br>
                        @endif
                        @if($invoice->seller->email)
                            {{ $invoice->seller->email }}
                        @endif
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-6xl font-light text-blue-600 mb-2 tracking-wider">INVOICE</p>
                    <p class="text-sm text-gray-600">
                        <span class="text-3xl font-bold text-gray-900 block">#{{ $invoice->invoiceNumber }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="px-12 py-10">
            @if($invoice->issuedAt || $invoice->dueAt)
                <div class="flex gap-10 mb-10">
                    @if($invoice->issuedAt)
                        <div class="flex-1">
                            <p class="text-xs uppercase font-semibold text-gray-500 mb-1">Invoice Date</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $invoice->issuedAt->format('d F Y') }}</p>
                        </div>
                    @endif
                    @if($invoice->dueAt)
                        <div class="flex-1">
                            <p class="text-xs uppercase font-semibold text-gray-500 mb-1">Due Date</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $invoice->dueAt->format('d F Y') }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-2 gap-10 mb-10">
                <div>
                    <p class="text-xs uppercase font-semibold text-gray-500 mb-3">Invoice From</p>
                    <p class="font-bold text-gray-900">{{ $invoice->seller->name }}</p>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        @if($invoice->seller->address)
                            {{ $invoice->seller->address }}<br>
                        @endif
                        @if($invoice->seller->email)
                            {{ $invoice->seller->email }}
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-xs uppercase font-semibold text-gray-500 mb-3">Invoice To</p>
                    <p class="font-bold text-gray-900">{{ $invoice->buyer->name }}</p>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        @if($invoice->buyer->address)
                            {{ $invoice->buyer->address }}<br>
                        @endif
                        @if($invoice->buyer->email)
                            {{ $invoice->buyer->email }}<br>
                        @endif
                        @if($invoice->buyer->vatNumber)
                            VAT ID: {{ $invoice->buyer->vatNumber }}
                        @endif
                    </p>
                </div>
            </div>

            <table class="w-full mb-10 text-sm">
                <thead class="bg-gray-100 border-t-2 border-b-2 border-blue-600">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-900 text-xs uppercase">Item Description</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-900 text-xs uppercase">Unit Price</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-900 text-xs uppercase">Quantity</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-900 text-xs uppercase">Tax Rate</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-900 text-xs uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-900">{{ $item->description }}</td>
                            <td class="px-6 py-4 text-right text-gray-600">{{ number_format($item->unitPrice, 2) }}</td>
                            <td class="px-6 py-4 text-right text-gray-600">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right text-gray-600">{{ number_format($item->tax * 100, 0) }}%</td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">{{ number_format($item->getTotal(), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end mb-8">
                <div class="w-96">
                    @if($invoice->getTotalDiscount() > 0)
                        <div class="flex justify-between py-2 px-6 text-sm text-gray-600 border-b border-gray-200">
                            <span>Subtotal:</span>
                            <span>{{ number_format($invoice->getSubtotal(), 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 px-6 text-sm text-gray-600 border-b border-gray-200">
                            <span>Discount:</span>
                            <span>-{{ number_format($invoice->getTotalDiscount(), 2) }}</span>
                        </div>
                    @endif
                    @if($invoice->getTotalTax() > 0)
                        <div class="flex justify-between py-2 px-6 text-sm text-gray-600 border-b border-gray-200">
                            <span>Tax:</span>
                            <span>{{ number_format($invoice->getTotalTax(), 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between py-4 px-6 bg-gray-100 border-t-2 border-b-2 border-blue-600">
                        <span class="font-bold text-gray-900 text-lg">Total:</span>
                        <span class="font-bold text-2xl text-gray-900">{{ number_format($invoice->getTotal(), 2) }} {{ $invoice->currency }}</span>
                    </div>
                </div>
            </div>

            @if($invoice->notes)
                <div class="bg-blue-50 border-l-4 border-blue-600 px-6 py-4 mb-8 text-sm text-gray-700">
                    <strong class="text-gray-900 block mb-2">Terms & Notes</strong>
                    {{ $invoice->notes }}
                </div>
            @endif
        </div>

        <div class="bg-gray-100 border-t border-gray-200 px-12 py-6 flex justify-between items-center">
            <p class="text-sm font-semibold text-gray-900">Thank you for your business!</p>
            <p class="text-xs text-gray-600">{{ config('app.name') }} - All rights reserved</p>
        </div>
    </div>
</body>
</html>