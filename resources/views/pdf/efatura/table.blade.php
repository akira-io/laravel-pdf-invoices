@if($page->tableY !== null)
    @php($columns = [[66, 70, ''], [136, 150, ''], [286, 50, 'right'], [336, 85, 'right'], [421, 50, 'right'], [471, 78, 'right']])
    @php($headers = ['code', 'description', 'quantity', 'price', 'tax_column', 'total'])
    <div class="rule" style="left: 54pt; top: {{ $page->tableY + 17 }}pt; width: 487pt;"></div>
    @foreach($columns as [$x, $width, $align])
        <div class="at bold s11 {{ $align }}" style="left: {{ $x }}pt; top: {{ $page->tableY + 26 }}pt; width: {{ $width - 8 }}pt; height: 16pt;">{{ $document->label($headers[$loop->index]) }}</div>
    @endforeach
    <div class="rule" style="left: 54pt; top: {{ $page->tableY + 44 }}pt; width: 487pt;"></div>
    @foreach($page->rows as $row)
        @foreach([$row->code, $row->description, $row->quantity, $row->unitPrice, $row->tax, $row->total] as $value)
            <div class="at s9 {{ $columns[$loop->index][2] }}" style="left: {{ $columns[$loop->index][0] }}pt; top: {{ $row->y }}pt; width: {{ $columns[$loop->index][1] - 8 }}pt; height: 14pt;">{{ $value }}</div>
        @endforeach
        <div class="rule rule-soft" style="left: 54pt; top: {{ $row->y + 18 }}pt; width: 487pt;"></div>
    @endforeach
    @if($page->carryForward !== null)
        @php($carryY = \Akira\PdfInvoices\Layout\FiscalPaginator::TABLE_BOTTOM_Y)
        <div class="rule" style="left: 318pt; top: {{ $carryY + 6 }}pt; width: 223pt;"></div>
        <div class="at bold s10" style="left: 318pt; top: {{ $carryY + 15 }}pt; width: 96pt;">{{ $document->label('carry_forward') }}</div>
        <div class="at bold s10 right" style="left: 410pt; top: {{ $carryY + 15 }}pt; width: 130pt;">{{ $page->carryForward }}</div>
    @endif
@endif
