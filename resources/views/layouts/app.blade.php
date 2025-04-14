<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{isset($pageTitle)?$pageTitle:'Login'}} | {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('assets/jquery-confirm/jquery-confirm.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/wnoty/wnoty.css') }}">
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <main>
        {{ $slot }}
    </main>
</div>

<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/jquery-confirm/jquery-confirm.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/wnoty/wnoty.js') }}"></script>
<script src="{{ asset('assets/bootstrap-datetimepicker/moment.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>

@if(session()->has('message'))
    <script type="text/javascript">
        $(document).ready(function () {
            notify('{{ session()->get('message') }}', '{{ session()->get('alert-type') }}');
        });
    </script>
@endif

@if($errors->any())
    <script type="text/javascript">
        $(document).ready(function () {
            var errors =<?php echo json_encode($errors->all()); ?>;
            $.each(errors, function (index, val) {
                notify(val, 'danger');
            });
        });
    </script>
@endif

<script type="text/javascript">
    $(".select2").each(function () {
        $(this).select2({
            dropdownParent: $(this).parent()
        });
    });

    $(".select2bs4").each(function () {
        $(this).select2({
            theme: "bootstrap4",
            dropdownParent: $(this).parent()
        });
    });

    $(".select2bs4-tags").each(function () {
        $(this).select2({
            tags: true,
            theme: "bootstrap4",
            dropdownParent: $(this).parent()
        });
    });

    $('.datetimepicker').datetimepicker();

    $('.datepicker').datetimepicker({
        format: 'YYYY-MM-DD',
    });

    $('.timepicker').datetimepicker({
        format: 'LT'
    });

    function notify(message, type) {
        $.wnoty({
            message: '<strong class="text-' + (type) + '">' + (message) + '</strong>',
            type: type,
            autohideDelay: 3000
        });
    }
</script>
@yield('javascript')
</body>
</html>
