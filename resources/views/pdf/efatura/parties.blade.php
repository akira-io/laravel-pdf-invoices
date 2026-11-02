@if($page->continuation)
    <div class="at bold s9" style="left: 54pt; top: 118pt; width: 180pt;">{{ $document->label('continuation') }}</div>
    <div class="at wrap s8 muted" style="left: 54pt; top: 136pt; width: 250pt;">{{ $document->label('identifier') }}: {{ $document->identifier ?? '-' }}</div>
@else
    @foreach([$document->issuer, $document->customer] as $party)
        @php($partyY = 118 + $loop->index * 112)
        <div class="at bold s10" style="left: 54pt; top: {{ $partyY }}pt; width: 180pt;">{{ $party->title }}</div>
        <div class="at wrap tight" style="left: 54pt; top: {{ $partyY + 20 }}pt; width: 240pt;">
            <div class="s10" style="margin-bottom: 6pt;">{{ $party->name }}</div>
            <div class="s10" style="margin-bottom: 5pt;">{{ $party->taxId }}</div>
            <div class="s9 muted" style="margin-bottom: 5pt;">{{ $party->address }}</div>
            <div class="s9 muted">{{ $party->contact }}</div>
        </div>
    @endforeach
    @if($document->contingency)
        <div class="at wrap bold s9" style="left: 306pt; top: 236pt; width: 235pt;">{{ $document->contingency }}</div>
    @endif
@endif
