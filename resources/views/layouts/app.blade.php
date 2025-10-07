<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Estacionamento Inteligente')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-pQdfkl+3rMkp6e8t+OpQJj6M0e4e0DdZVY1r8p6+Y5J9Q0lD0Fv1cVdF+yK0JYF1" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
   
    <header class="bg-light py-3 mb-4">
        <div class="container">
            <h1 class="h3">Estacionamento Inteligente</h1>
        </div>
    </header>

    <main class="container mb-5">
        @yield('content') <!--Não Alterar essa linha;-->
    </main>

    <footer class="bg-light py-3 mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Estacionamento Inteligente. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-PtN+2iA0wNf7G2/3DqL6L2L2K5y6t0k9jF8y1tXk0K9tX0k9F8y2L6k9J2L8K0j9" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}"></script>

</body>
</html>