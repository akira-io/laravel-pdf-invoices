<div class="at s8 muted right" style="left: 54pt; top: 34pt; width: 487pt;">{{ $document->pageLabel($page) }}</div>
<div class="mark" style="left: 54pt; top: 68pt; border-radius: 2pt;"></div>
<div class="mark" style="left: 49pt; top: 75pt;"></div>
<div class="at bold s10" style="left: 74pt; top: 72pt; width: 180pt; height: 14pt;">{{ $document->brand }}</div>
<div class="at bold s13 right" style="left: 320pt; top: 72pt; width: 220pt;">{{ $document->title }}</div>
<div class="at s9 muted right" style="left: 320pt; top: 92pt; width: 220pt;">{{ $document->subtitle }}</div>
@foreach($document->facts as $fact)
    <div class="at bold s10" style="left: 306pt; top: {{ 118 + $loop->index * 22 }}pt; width: 84pt;">{{ $fact->label }}</div>
    <div class="at s10 right" style="left: 392pt; top: {{ 118 + $loop->index * 22 }}pt; width: 150pt; height: 14pt;">{{ $fact->value }}</div>
@endforeach
