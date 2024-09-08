<head>
    @livewireStyles
    <link rel="stylesheet" href="{{asset('bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('1')}}">
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>