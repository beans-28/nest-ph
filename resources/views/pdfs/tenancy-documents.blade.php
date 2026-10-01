{{--
  The dorm's tenancy documents as one PDF, laid out like the original Word
  files (PSD_Contract / PSD_Rules & Regulations / PSD_Payments and Fees):
  US Letter, the original letterhead image behind every page, Arial-sized
  text (Arimo, the free metric twin of Arial, so lines break the same way),
  and the same spacing and heading rules. $documents lists which of
  agreement / rules / fees to include, in that order.
--}}
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  @font-face { font-family: 'Arimo'; font-style: normal; font-weight: normal; src: url('{{ resource_path('fonts/Arimo-Regular.ttf') }}') format('truetype'); }
  @font-face { font-family: 'Arimo'; font-style: normal; font-weight: bold; src: url('{{ resource_path('fonts/Arimo-Bold.ttf') }}') format('truetype'); }
  @font-face { font-family: 'Arimo'; font-style: italic; font-weight: normal; src: url('{{ resource_path('fonts/Arimo-Italic.ttf') }}') format('truetype'); }
  @font-face { font-family: 'Arimo'; font-style: italic; font-weight: bold; src: url('{{ resource_path('fonts/Arimo-BoldItalic.ttf') }}') format('truetype'); }

  /* Letter size. Top/bottom margins keep text clear of the letterhead's
     logo band and footer bar, same as the Word header/footer areas. */
  @page { size: 8.5in 11in; margin: 1.78in 1in 1.72in 1in; }
  /* Word: 10.5pt Arial at 1.1 line spacing = about 12.5pt per line. This PDF
     engine scales a plain line-height by its own font-height factor, so 0.93
     here is what measures 12.5pt (a "pt" value comes out larger, not exact). */
  body { font-family: 'Arimo', sans-serif; font-size: 10.5pt; color: #000; line-height: 0.93; margin: 0; }

  .letterhead { position: fixed; top: -1.78in; left: -1in; width: 8.5in; height: 11in; z-index: -1; }
  .page-break { page-break-before: always; }
  /* Page numbers are drawn by App\Services\TenancyDocuments, restarting at 1 for each document like the originals. */

  /* Characters Arial doesn't have (Word used Segoe UI Symbol for these). */
  .box { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; }

  .doc-title { text-align: center; font-size: 16pt; font-weight: bold; margin: 0 0 3pt 0; }
  .doc-title.spaced { margin-top: 12pt; }
  .doc-version { text-align: center; font-size: 9.5pt; color: #555; margin: 0 0 12pt 0; }

  p { margin: 0 0 5.5pt 0; text-align: justify; }
  .left { text-align: left; }
  .loose { margin-top: 12pt; }
  .indent-1 { margin-left: 0.25in; }
  .indent-2 { margin-left: 0.5in; }

  h2 { font-size: 11.5pt; font-weight: bold; margin: 13pt 0 5pt 0; padding-bottom: 2pt; border-bottom: 0.75pt solid #808080; page-break-after: avoid; }
  .agreement h2 { margin: 12pt 0 5.5pt 0; }
  h2.plain { border-bottom: none; margin-top: 24pt; }
  h3 { font-size: 10.5pt; font-weight: bold; margin: 12pt 0 3.5pt 0; page-break-after: avoid; }

  /* Numbered paragraphs: Word's hanging indent (0.375in, or 0.25in for (a) lists). */
  table.num { width: 100%; border-collapse: collapse; margin: 0 0 4.5pt 0; }
  table.num td { padding: 0; vertical-align: top; text-align: justify; }
  table.num td.n { width: 0.375in; text-align: left; }
  table.num.alpha { margin-left: 0.25in; width: 5.75in; }
  table.num.alpha td.n { width: 0.25in; }

  .fill { text-decoration: underline; }
  .checks { margin: 0 0 3pt 0.5in; }
  .checks div { margin-bottom: 2pt; }

  table.grid { width: 100%; border-collapse: collapse; margin: 4pt 0 9pt 0; }
  table.grid th, table.grid td { border: 0.75pt solid #9a9a9a; padding: 4pt 6pt; vertical-align: top; text-align: left; font-size: 10.5pt; }
  table.grid th { background: #e7e7e7; font-weight: bold; }

  /* Signature lines: the drawn signature sits on the line, the caption under it. */
  table.sig { width: 100%; border-collapse: collapse; margin-top: 16pt; page-break-inside: avoid; }
  table.sig td { vertical-align: bottom; padding: 0; }
  table.sig td.who { width: 4.1in; padding-right: 0.45in; }
  .sig-line { border-bottom: 0.75pt solid #000; height: 40pt; position: relative; text-align: center; }
  .sig-line img { max-height: 38pt; max-width: 2.6in; }
  .sig-name { position: absolute; bottom: 1pt; left: 0; right: 0; text-align: center; font-size: 9pt; }
  .date-line { border-bottom: 0.75pt solid #000; height: 14pt; text-align: left; }
  .caption { font-size: 10.5pt; margin-top: 2pt; }
  .consent { margin: 6pt 0 0 0; text-align: left; }

  table.admin { width: 100%; border-collapse: collapse; margin-top: 6pt; }
  table.admin td { border: 0.75pt solid #808080; padding: 9pt 8pt; width: 50%; vertical-align: top; }
  .keep { page-break-inside: avoid; }
</style>
</head>
<body>
  @php $letterhead = resource_path('documents/letterhead.jpg'); @endphp
  @if(is_file($letterhead))<img src="{{ $letterhead }}" class="letterhead">@endif

  @foreach($documents as $i => $document)
    @if($i > 0)<div class="page-break"></div>@endif
    @include('documents.' . $document)
  @endforeach
</body>
</html>
