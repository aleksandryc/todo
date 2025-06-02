<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Document' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
            text-align: justify;
        }

        #content {
            margin-top: 62px;
        }

        #footer {
            display: block;
            position: running(footer);
            width: 100%;
            text-align: right;
            padding-top: 10px;
            border-top: 1px solid #000;
        }

        .hidden-fields-table {
            width: 100%;
            margin-top: 10px;
        }

        .hidden-fields-table td.label-cell {
            width: 37%;
            background-color: #f9f9f9;
            vertical-align: top;
        }

        .hidden-fields-table td.value-cell {
            width: 63%;
            min-height: 2.5em;
            padding: 12px 8px;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 5px;
            margin: 2px;
            border: 1px solid black;
        }

        td {
            border: 1px solid grey;
            padding: 12px 8px;
            line-height: 1.2;
        }

        td:first-child {
            width: 37%;
            background-color: #f9f9f9;
            vertical-align: top;
        }

        td:last-child {
            width: 63%;
            min-height: 2.5em;
        }

        @page {
            size: A4;
            margin: 120px 50px 80px 50px;

            @top-center {
                content: element(header);
            }

            @bottom-center {
                content: element(footer);
            }
        }

        img {
            height: 100px;
            display: inline;
            vertical-align: bottom;
            margin-bottom: 4px;
        }

        header {
            position: fixed;
            top: -95px;
            left: 0px;
            right: 0px;
            height: 50px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
        }

        .page-counter:before {
            content: counter(page);
        }

    </style>
</head>
<body>
    <header>
        <div>
            <img style="float: right" src="{{ $logo }}">
            <h1>{{ $title ?? 'Document' }}</h1>
        </div>
    </header>

    <div id="content">
        <table border="1" cellpadding="8" cellspacing="0" width="95%">
            @forelse($fields as $key => $value)
            <tr>
                <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                <td>
                    @php
                    $embeddedData = $embeddedImages[$key] ?? null;
                    @endphp

                    @if($embeddedData)
                    <a href="{{ asset('storage/' . (is_string($value) ? $value : '')) }}">
                        <img src="{{ $embeddedData }}" alt="Embedded image">
                    </a>
                    @elseif(is_string($value) && str_starts_with($value, 'attachments/'))
                    <a href="{{ asset('storage/' . $value) }}">{{ asset('storage/' . $value) }}</a>
                    @elseif(is_array($value))
                    {{ implode(', ', array_map('strval', $value)) }}
                    @elseif(is_scalar($value))
                    {{ $value }}
                    @else

                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2">No data available</td>
            </tr>
            @endforelse
        </table>

        <!-- Hidden fields section at the bottom -->
        @if(!empty($hiddenFields))
        <div style="margin-top: 20px;">For office use</div>
        <div>
            <table border="1" cellpadding="8" cellspacing="0" width="95%" class="hidden-fields-table">
                @foreach($hiddenFields as $key => $field)
                <tr>
                    <td class="label-cell"><strong>{{ ucwords(str_replace('_', ' ', $field['label'])) }}</strong></td>
                    <td class="value-cell">{{ $field['value'] }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif
    </div>
    <div id="footer">
        Submitted at: {{ now()->toDateTimeString() }}
    </div>
    <footer>Page <span class="page-counter"></span></footer>
</body>
</html>
