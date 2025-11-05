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
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-12 py-10">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold mb-2">{{ $invoice->seller->name }}</h1>
                    @if($invoice->seller->address || $invoice->seller->vatNumber)
                        <p class="text-xs text-indigo-100 leading-tight">
                            @if($invoice->seller->address)
                                {{ $invoice->seller->address }}<br>
                            @endif
                            @if($invoice->seller->vatNumber)
                                {{ $translator->__('vat') }}: {{ $invoice->seller->vatNumber }}
                            @endif
                        </p>
                    @endif
                </div>

                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-indigo-100 mb-1">{{ $translator->__('invoice') }}</p>
                    <p class="text-3xl font-bold mb-2">#{{ $invoice->invoiceNumber }}</p>
                    <div class="text-xs text-indigo-100 space-y-0">
                        @if($invoice->issuedAt)
                            <div>{{ $translator->__('issued') }}: {{ $invoice->issuedAt->format('d M Y') }}</div>
                        @endif
                        @if($invoice->dueAt)
                            <div>{{ $translator->__('due') }}: {{ $invoice->dueAt->format('d M Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="px-12 py-8">
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-xs uppercase font-semibold text-gray-500 mb-2">{{ $translator->__('bill_from') }}</h3>
                    <p class="font-semibold text-sm text-gray-900">{{ $invoice->seller->name }}</p>
                    <p class="text-xs text-gray-600">
                        @forelse($invoice->seller->getAttributes() as $value)
                            @if(!empty($value))
                                {{ $value }}<br>
                            @endif
                        @empty
                        @endforelse
                    </p>
                </div>

                <div>
                    <h3 class="text-xs uppercase font-semibold text-gray-500 mb-2">{{ $translator->__('bill_to') }}</h3>
                    <p class="font-semibold text-sm text-gray-900">{{ $invoice->buyer->name }}</p>
                    <p class="text-xs text-gray-600">
                        @forelse($invoice->buyer->getAttributes() as $value)
                            @if(!empty($value))
                                {{ $value }}<br>
                            @endif
                        @empty
                        @endforelse
                    </p>
                </div>
            </div>

            <table class="w-full mb-6 text-xs">
                <thead class="bg-gray-50 border-b border-indigo-600">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-900 uppercase">{{ $translator->__('description') }}</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('unit_price') }}</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('qty') }}</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('tax') }}</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-900 uppercase">{{ $translator->__('amount') }}</th>
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
                    <div class="flex flex-col py-4 px-4 bg-gray-50 border-t border-b border-indigo-600 gap-2">
                        <span class="font-bold text-sm text-gray-900">{{ $translator->__('total') }}</span>
                        <span class="font-bold text-2xl text-gray-900">{{ number_format($invoice->getTotal(), 2) }} {{ $invoice->currency }}</span>
                    </div>
                </div>
            </div>

            @if($invoice->notes)
                <div class="bg-gray-50 p-6 rounded border-l-4 border-indigo-600 mb-12 text-xs text-gray-700">
                    <strong class="text-gray-900 block mb-2">{{ $translator->__('notes') }}</strong>
                    {{ $invoice->notes }}
                </div>
            @endif
        </div>

        <div class="bg-gray-50 px-12 py-6 border-t border-gray-200 text-center text-xs text-gray-600">
            <p>{{ $translator->__('thank_you') }}</p>
        </div>
    </div>
</body>
</html>