<h2>{{$data['title'] ?? 'Form Submission' }}</h2>

<ul>
    @foreach($data['fields'] as $key => $value)
    <li><strong>{{ucwords(str_replace('_', ' ', $key)) }}: </strong>{{$value}}</li>
    @endforeach
</ul>
@if(!empty($data['embeddedImages']))
<h3>Attached images</h3>
@foreach($data['embeddedImages'] as $name => $base64)

<img src="{{ $base64 }}" alt="{{ $name }}" style="max-width: 248px;">

@endforeach
@endif
<p>Submitted at: {{now()->toDayDateTimeString() }}</p>

Thanks,<br>
{{ config('app.name') }}
