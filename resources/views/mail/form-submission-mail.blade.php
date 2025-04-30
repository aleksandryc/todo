
<h2>{{$data['title'] ?? 'Form Submission' }}</h2>

<ul>
    @foreach($data['fields'] as $key => $value)
    <li><strong>{{ucwords(str_replace('_', ' ', $key)) }}:</strong>{{$value}}</li>
    @endforeach
</ul>
<p>Submitted at: {{now()->toDayDateTimeString() }}</p>
Thanks,<br>
{{ config('app.name') }}

