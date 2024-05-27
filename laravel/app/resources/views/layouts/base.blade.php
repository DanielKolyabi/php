<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.min.js" defer></script>
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
