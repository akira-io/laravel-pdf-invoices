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
    <link rel="stylesheet" href="{{ asset('css/invoices-modern.css') }}">
</head>
<body class="bg-gray-50">
    <div class="max-w-2xl mx-auto bg-white shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-10 py-12">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $invoice->seller->name }}</h1>
                    @if($invoice->seller->address || $invoice->seller->vatNumber)
                        <p class="text-sm text-indigo-100 leading-relaxed">
                            @if($invoice->seller->address)
                                {{ $invoice->seller->address }}<br>
                            @endif
                            @if($invoice->seller->vatNumber)
                                VAT: {{ $invoice->seller->vatNumber }}
                            @endif
                        </p>
                    @endif
                </div>

                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-indigo-100 mb-2">Invoice</p>
                    <p class="text-4xl font-bold mb-4">#{{ $invoice->invoiceNumber }}</p>
                    <div class="text-xs text-indigo-100 space-y-1">
                        @if($invoice->issuedAt)
                            <div>Issued: {{ $invoice->issuedAt->format('d M Y') }}</div>
                        @endif
                        @if($invoice->dueAt)
                            <div>Due: {{ $invoice->dueAt->format('d M Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="px-10 py-10">
            <div class="grid grid-cols-2 gap-10 mb-10">
                <div>
                    <h3 class="text-xs uppercase font-semibold text-gray-500 mb-3">Bill From</h3>
                    <p class="font-semibold text-gray-900">{{ $invoice->seller->name }}</p>
                    @if($invoice->seller->email)
                        <p class="text-sm text-gray-600">{{ $invoice->seller->email }}</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-xs uppercase font-semibold text-gray-500 mb-3">Bill To</h3>
                    <p class="font-semibold text-gray-900">{{ $invoice->buyer->name }}</p>
                    @if($invoice->buyer->address)
                        <p class="text-sm text-gray-600">{{ $invoice->buyer->address }}</p>
                    @endif
                    @if($invoice->buyer->email)
                        <p class="text-sm text-gray-600">{{ $invoice->buyer->email }}</p>
                    @endif
                </div>
            </div>

            <table class="w-full mb-8 text-sm">
                <thead class="bg-gray-50 border-b-2 border-indigo-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 text-xs uppercase">Description</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-900 text-xs uppercase">Unit Price</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-900 text-xs uppercase">Qty</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-900 text-xs uppercase">Tax</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-900 text-xs uppercase">Amount</th>
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

            <div class="flex justify-end mb-8">
                <div class="w-80">
                    @if($invoice->getTotalDiscount() > 0)
                        <div class="flex justify-between py-2 px-4 text-sm text-gray-600 border-b border-gray-200">
                            <span>Subtotal:</span>
                            <span>{{ number_format($invoice->getSubtotal(), 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 px-4 text-sm text-gray-600 border-b border-gray-200">
                            <span>Discount:</span>
                            <span>-{{ number_format($invoice->getTotalDiscount(), 2) }}</span>
                        </div>
                    @endif
                    @if($invoice->getTotalTax() > 0)
                        <div class="flex justify-between py-2 px-4 text-sm text-gray-600 border-b border-gray-200">
                            <span>Tax:</span>
                            <span>{{ number_format($invoice->getTotalTax(), 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between py-3 px-4 bg-gray-50 border-t-2 border-b-2 border-indigo-600">
                        <span class="font-bold text-gray-900">Total Due</span>
                        <span class="font-bold text-lg text-gray-900">{{ number_format($invoice->getTotal(), 2) }} {{ $invoice->currency }}</span>
                    </div>
                </div>
            </div>

            @if($invoice->notes)
                <div class="bg-gray-50 p-4 rounded border-l-4 border-indigo-600 mb-8 text-sm text-gray-700">
                    <strong class="text-gray-900 block mb-2">Notes & Terms</strong>
                    {{ $invoice->notes }}
                </div>
            @endif
        </div>

        <div class="bg-gray-50 px-10 py-6 border-t border-gray-200 text-center text-sm text-gray-600">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>