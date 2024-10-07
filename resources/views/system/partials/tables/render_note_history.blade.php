@php
    $html = '';
@endphp
@if ($noteHistories) 
    @php
        $indNoteId = 0;
    @endphp
    @foreach ($noteHistories as $note)
        @php
            if (isset($note['ind_note_id'])) {z
                $indNoteId = $note['ind_note_id'];
            } else {
                $indNoteId++;
            }
        @endphp
        <tr>
            <td class='text-left'>{{ $note['author'] }}</td>
            <td class='text-left'>{{ date('Y-m-d H:i:s', $note['date_added']) }}</td>
            <td class='text-left'>{{ htmlspecialchars($note['note'], ENT_QUOTES, 'UTF-8') }}</td>
            <td class='text-right'></td>
        </tr>
    @endforeach
@endif
