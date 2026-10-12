@foreach($summary->totals as $line)
    @php($final = $loop->last)
    @if($final)
        <div class="rule" style="left: 318pt; top: {{ $line->y - 5 }}pt; width: 222pt;"></div>
    @endif
    <div class="at {{ $final ? 'bold s17' : 's10' }}" style="left: 318pt; top: {{ $line->y }}pt; width: 96pt;">{{ $line->label }}</div>
    <div class="at {{ $final ? 'bold s17' : 's10' }} right" style="left: 410pt; top: {{ $line->y }}pt; width: 130pt;">{{ $line->value }}</div>
@endforeach
@if($summary->conversionY !== null)
    <div class="rule" style="left: 54pt; top: {{ $summary->conversionY - 6 }}pt; width: 487pt;"></div>
    <div class="at bold s10" style="left: 54pt; top: {{ $summary->conversionY }}pt; width: 487pt;">{{ $document->label('conversion') }}</div>
    @foreach($summary->conversion as $line)
        <div class="at bold s8 muted" style="left: 54pt; top: {{ $line->y }}pt; width: 94pt;">{{ $line->label }}</div>
        <div class="at wrap s8" style="left: 154pt; top: {{ $line->y }}pt; width: 387pt;">{{ $line->value }}</div>
    @endforeach
@endif
@if($summary->notesY !== null)
    <div class="at bold s10" style="left: 54pt; top: {{ $summary->notesY }}pt; width: 487pt;">{{ $document->label('notes') }}</div>
    <div class="at wrap s8" style="left: 54pt; top: {{ $summary->notesY + 20 }}pt; width: 487pt;">
        @foreach($summary->notes as $note)
            <div>{{ $note }}</div>
        @endforeach
    </div>
@endif
<div class="rule" style="left: 54pt; top: {{ $summary->verificationY }}pt; width: 487pt;"></div>
@if($summary->qrCode !== null)
    <img class="qr" src="{{ $summary->qrCode }}" style="left: 64pt; top: {{ $summary->verificationY + 18 }}pt;" alt="QR">
@endif
<div class="at bold s10" style="left: 156pt; top: {{ $summary->verificationY + 22 }}pt; width: 340pt;">{{ $document->label('verification') }}</div>
<div class="at s9" style="left: 156pt; top: {{ $summary->verificationY + 42 }}pt; width: 340pt;">{{ $document->label('verification_text') }}</div>
<div class="at s8 muted" style="left: 156pt; top: {{ $summary->verificationY + 62 }}pt; width: 340pt; height: 12pt;">{{ $document->label('identifier') }}: {{ $document->identifier ?? '-' }}</div>
<div class="at s8 muted" style="left: 156pt; top: {{ $summary->verificationY + 82 }}pt; width: 340pt;">{{ $document->label('platform') }}</div>
