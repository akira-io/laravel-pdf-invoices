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
<div class="w-full bg-white overflow-hidden">
    <div class="border-b-2 border-gray-100 px-12 py-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $invoice->seller->name }}</h1>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-600 uppercase">{{ $translator->__('invoice') }}</p>
                <p class="text-2xl font-bold text-gray-900">#{{ $invoice->invoiceNumber }}</p>
            </div>
        </div>
    </div>
    <div class="px-12 py-8">
        <div class="grid grid-cols-2 gap-8 mb-6">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 mb-2">{{ $translator->__('from') }}</p>
                <p class="font-semibold text-sm text-gray-900">{{ $invoice->seller->name }}</p>
                <p class="text-xs text-gray-600">
                    @if($invoice->seller->address)
                        {{ $invoice->seller->address }}<br>
                    @endif
                    @if($invoice->seller->email)
                        {{ $invoice->seller->email }}<br>
                    @endif
                    @forelse($invoice->seller->attributes() as $value)
                        @if(!empty($value))
                            {{ $value }}<br>
                        @endif
                    @empty
                    @endforelse
                    @if($invoice->seller->vatNumber)
                        {{ $translator->__('vat') }}: {{ $invoice->seller->vatNumber }}
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 mb-2">{{ $translator->__('to') }}</p>
                <p class="font-semibold text-sm text-gray-900">{{ $invoice->buyer->name }}</p>
                <p class="text-xs text-gray-600">
                    @if($invoice->buyer->address)
                        {{ $invoice->buyer->address }}<br>
                    @endif
                    @if($invoice->buyer->email)
                        {{ $invoice->buyer->email }}<br>
                    @endif
                    @forelse($invoice->buyer->attributes() as $value)
                        @if(!empty($value))
                            {{ $value }}<br>
                        @endif
                    @empty
                    @endforelse
                    @if($invoice->buyer->vatNumber)
                        {{ $translator->__('vat') }}: {{ $invoice->buyer->vatNumber }}
                    @endif
                </p>
            </div>
        </div>
        <table class="w-full mb-6 text-xs">
            <thead class="bg-gray-50 border-t border-b border-gray-200">
            <tr>
                <th class="px-4 py-2 text-left font-semibold text-gray-900 uppercase">{{ $translator->__('description') }}</th>
                <th class="px-4 py-2 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('unit_price') }}</th>
                <th class="px-4 py-2 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('qty') }}</th>
                <th class="px-4 py-2 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('tax') }}</th>
                <th class="px-4 py-2 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('subtotal') }}</th>
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
        <div class="flex justify-end mb-4">
            <div class="w-72">
                @if($invoice->getTotalDiscount() > 0)
                    <div class="flex justify-between py-1 px-4 text-xs text-gray-600 border-b border-gray-200">
                        <span>{{ $translator->__('subtotal') }}:</span>
                        <span>{{ number_format($invoice->getSubtotal(), 2) }}</span>
                    </div>
                    <div class="flex justify-between py-1 px-4 text-xs text-gray-600 border-b border-gray-200">
                        <span>{{ $translator->__('discount') }}:</span>
                        <span>-{{ number_format($invoice->getTotalDiscount(), 2) }}</span>
                    </div>
                @endif
                @if($invoice->getTotalTax() > 0)
                    <div class="flex justify-between py-1 px-4 text-xs text-gray-600 border-b border-gray-200">
                        <span>{{ $translator->__('tax') }}:</span>
                        <span>{{ number_format($invoice->getTotalTax(), 2) }}</span>
                    </div>
                @endif
                <div class="flex flex-col py-4 px-4 bg-gray-50 border-t border-b border-gray-900 gap-2">
                    <span class="font-bold text-sm text-gray-900">{{ $translator->__('total') }}</span>
                    <span class="font-bold text-2xl text-gray-900">{{ number_format($invoice->getTotal(), 2) }} {{ $invoice->currency }}</span>
                </div>
            </div>
        </div>
        @if($invoice->notes)
            <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 mb-4 text-xs text-gray-700">
                <strong class="text-gray-900 block mb-1">{{ $translator->__('notes') }}</strong>
                {{ $invoice->notes }}
            </div>
        @endif
    </div>
    <div class="bg-gray-50 border-t border-gray-200 px-12 py-6 text-center text-xs text-gray-600">
        <p>{{ $translator->__('thank_you') }}</p>
    </div>
</div>
</body>
</html>