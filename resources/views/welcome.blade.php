<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"/>
    <title>{{ config('app.name', 'Twitter reach calculator') }}</title>
    @include('components.meta.head')
</head>
<body class="js--waiting">
@include('components.wizard.base')
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
