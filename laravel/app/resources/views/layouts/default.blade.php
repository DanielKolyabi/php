<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.head')
    <title>@yield('page.title', config('app.name'))</title>
</head>
<body>

<div class="d-flex flex-column justify-content-between min-vh-100">
    <header class="flex bg-light">
        <div class="container py-3">
            @include('includes.header')
        </div>
    </header>
    <main class="flex-grow-1">
        <div class="container py-3">
            @yield('content')
        </div>
    </main>
    <footer class="flex bg-light">
        <div class="container py-3">
            @include('includes.footer')
        </div>
    </footer>
</div>

</body>
</html>
