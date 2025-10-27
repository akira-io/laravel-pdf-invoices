<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoiceNumber }}</title>
    <style>
        {!! $compiledCss !!}
    </style>
</head>
<body class="bg-white">
    <div class="w-full bg-white p-12">
        <div class="flex justify-between items-start mb-8 pb-6 border-b-2 border-gray-100">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $invoice->seller->name }}</h1>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-600 uppercase">{{ $translator->__('invoice') }}</p>
                <p class="text-2xl font-bold text-gray-900">#{{ $invoice->invoiceNumber }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-xs uppercase font-semibold text-gray-500 mb-2">{{ $translator->__('from') }}</h3>
                <p class="font-semibold text-sm text-gray-900">{{ $invoice->seller->name }}</p>
                @if($invoice->seller->address)
                    <p class="text-xs text-gray-600">{{ $invoice->seller->address }}</p>
                @endif
                @if($invoice->seller->email)
                    <p class="text-xs text-gray-600">{{ $invoice->seller->email }}</p>
                @endif
                @if($invoice->seller->vatNumber)
                    <p class="text-xs text-gray-600">{{ $translator->__('vat') }}: {{ $invoice->seller->vatNumber }}</p>
                @endif
            </div>

            <div>
                <h3 class="text-xs uppercase font-semibold text-gray-500 mb-2">{{ $translator->__('to') }}</h3>
                <p class="font-semibold text-sm text-gray-900">{{ $invoice->buyer->name }}</p>
                @if($invoice->buyer->address)
                    <p class="text-xs text-gray-600">{{ $invoice->buyer->address }}</p>
                @endif
                @if($invoice->buyer->email)
                    <p class="text-xs text-gray-600">{{ $invoice->buyer->email }}</p>
                @endif
                @if($invoice->buyer->vatNumber)
                    <p class="text-xs text-gray-600">{{ $translator->__('vat') }}: {{ $invoice->buyer->vatNumber }}</p>
                @endif
            </div>
        </div>

        <table class="w-full mb-8 text-xs">
            <thead class="bg-gray-50 border-t border-b border-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700 uppercase">{{ $translator->__('description') }}</th>
                    <th class="px-4 py-2 text-right font-semibold text-gray-700 uppercase">{{ $translator->__('unit_price') }}</th>
                    <th class="px-4 py-2 text-right font-semibold text-gray-700 uppercase">{{ $translator->__('qty') }}</th>
                    <th class="px-4 py-2 text-right font-semibold text-gray-700 uppercase">{{ $translator->__('tax') }}</th>
                    <th class="px-4 py-2 text-right font-semibold text-gray-700 uppercase">{{ $translator->__('subtotal') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-2 text-gray-900">{{ $item->description }}</td>
                        <td class="px-4 py-2 text-right text-gray-600">{{ number_format($item->unitPrice, 2) }}</td>
                        <td class="px-4 py-2 text-right text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-4 py-2 text-right text-gray-600">{{ number_format($item->tax * 100, 0) }}%</td>
                        <td class="px-4 py-2 text-right font-semibold text-gray-900">{{ number_format($item->getTotal(), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end mb-8">
            <div class="w-72">
                @if($invoice->getTotalDiscount() > 0)
                    <div class="flex justify-between py-2 px-4 border-b border-gray-200 text-xs">
                        <span class="text-gray-600">{{ $translator->__('subtotal') }}:</span>
                        <span class="text-gray-900">{{ number_format($invoice->getSubtotal(), 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 px-4 border-b border-gray-200 text-xs">
                        <span class="text-gray-600">{{ $translator->__('discount') }}:</span>
                        <span class="text-gray-900">-{{ number_format($invoice->getTotalDiscount(), 2) }}</span>
                    </div>
                @endif
                @if($invoice->getTotalTax() > 0)
                    <div class="flex justify-between py-2 px-4 border-b border-gray-200 text-xs">
                        <span class="text-gray-600">{{ $translator->__('tax') }}:</span>
                        <span class="text-gray-900">{{ number_format($invoice->getTotalTax(), 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-3 px-4 border-t border-b border-gray-900 bg-gray-50">
                    <span class="font-bold text-sm text-gray-900">{{ $translator->__('total') }}:</span>
                    <span class="font-bold text-lg text-gray-900">{{ number_format($invoice->getTotal(), 2) }} {{ $invoice->currency }}</span>
                </div>
            </div>
        </div>

        @if($invoice->notes)
            <div class="bg-gray-50 p-4 rounded border border-gray-200 mb-8 text-xs text-gray-700">
                <strong class="text-gray-900 block mb-2">{{ $translator->__('notes') }}:</strong>
                {{ $invoice->notes }}
            </div>
        @endif

        <div class="text-center text-xs text-gray-500 pt-8 border-t border-gray-200">
            <p>{{ $translator->__('thank_you') }}</p>
        </div>
    </div>
</body>
</html>