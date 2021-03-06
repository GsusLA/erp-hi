<!DOCTYPE html>
<html lang="es">
<head>
    <link href="/img/company-icon.png" rel="shortcut icon" type="image/x-icon">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAO</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/floating.css') }}" rel="stylesheet"/>
</head>
<div id="app">
    <main-app/>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('assets/floating.js') }}"></script>
</html>
