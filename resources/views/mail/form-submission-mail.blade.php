<h2>{{ $data['title'] ?? 'Form Submission' }}</h2>

@foreach($data['fields'] as $key => $value)
@if(is_array($value))
{{ implode(', ', $value) }}
@else
{{ ucwords(str_replace('_', ' ', $key)) }}:{{ $value }}
@endif
@endforeach

Submitted at: {{ now()->toDayDateTimeString() }}

Thanks,<br>
{{ config('app.name') }}
