<!DOCTYPE html>
<html lang="es">
<head>
    <link href="/img/company-icon.png" rel="shortcut icon" type="image/x-icon">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>SAO</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<?php echo e(mix('css/app.css')); ?>" rel="stylesheet"/>
</head>
<div id="app">
    <main-app/>
</div>
<script src="<?php echo e(mix('js/app.js')); ?>"></script>
</html>
