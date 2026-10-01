{{-- One signature line from the original documents: the drawn signature and
     printed name on the line, the caption under it, and a Date line beside it. --}}
<table class="sig">
  <tr>
    <td class="who">
      <div class="sig-line">
        @if(!empty($image))<img src="{{ $image }}" alt="">@endif
        @if(!empty($image) && !empty($name))<div class="sig-name">{{ $name }}</div>@endif
      </div>
      <div class="caption">{{ $caption }}</div>
    </td>
    <td>
      <div class="date-line">{{ !empty($image) && $date ? $date->format('F j, Y') : '' }}</div>
      <div class="caption">Date</div>
    </td>
  </tr>
</table>
