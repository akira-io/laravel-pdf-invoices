@if($page->continuation)
    <div class="at bold s9" style="left: 54pt; top: 118pt; width: 180pt;">{{ $document->label('continuation') }}</div>
    <div class="at wrap s8 muted" style="left: 54pt; top: 136pt; width: 210pt;">{{ $document->label('identifier') }}: {{ $document->identifier ?? '-' }}</div>
@else
    @foreach([$document->issuer, $document->customer] as $party)
        @php($partyY = 118 + $loop->index * 112)
        <div class="at bold s10" style="left: 54pt; top: {{ $partyY }}pt; width: 180pt;">{{ $party->title }}</div>
        <div class="at s10" style="left: 54pt; top: {{ $partyY + 20 }}pt; width: 190pt; height: 14pt;">{{ $party->name }}</div>
        <div class="at s10" style="left: 54pt; top: {{ $partyY + 38 }}pt; width: 190pt;">{{ $party->taxId }}</div>
        <div class="at s9 muted" style="left: 54pt; top: {{ $partyY + 55 }}pt; width: 190pt; height: 14pt;">{{ $party->address }}</div>
        <div class="at s9 muted" style="left: 54pt; top: {{ $partyY + 72 }}pt; width: 190pt; height: 14pt;">{{ $party->contact }}</div>
    @endforeach
    @if($document->contingency)
        <div class="at wrap bold s9" style="left: 306pt; top: 236pt; width: 235pt;">{{ $document->contingency }}</div>
    @endif
@endif
