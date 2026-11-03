@use('Akira\PdfInvoices\Layout\FiscalLayout')
@php($document = new FiscalLayout($translator)->build($invoice))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $translator->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->title }} {{ $document->identifier }}</title>
    @include('pdf-invoices::pdf.efatura.styles')
</head>
<body>
@foreach($document->pages as $page)
    <div class="page{{ $loop->last ? '' : ' break' }}">
        @include('pdf-invoices::pdf.efatura.header')
        @include('pdf-invoices::pdf.efatura.parties')
        @include('pdf-invoices::pdf.efatura.table')
        @if($page->summary)
            @include('pdf-invoices::pdf.efatura.summary', ['summary' => $page->summary])
        @endif
    </div>
@endforeach
</body>
</html>
