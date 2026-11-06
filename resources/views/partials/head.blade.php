<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="iClix: nonton film dan serial favorit kapan saja, di perangkat apa saja.">

<title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name') }}</title>

@fonts

<script>
    window.iClix = @js([
        'baseUrl' => rtrim(url('/'), '/'),
        'watchlistIds' => $watchlistIds ?? [],
    ]);
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
