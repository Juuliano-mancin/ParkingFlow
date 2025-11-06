<!DOCTYPE html>
<html lang="pt-br" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Estacionamento Inteligente')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Lobster&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Satisfy&family=Shadows+Into+Light&family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Lobster&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Satisfy&family=Shadows+Into+Light&family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Dosis:wght@200..800&family=Lobster&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Satisfy&family=Shadows+Into+Light&family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        h1, small {
            font-family: 'Noto Sans', sans-serif;
            color: #002B5B;
        } 
        footer {
            font-family: 'Doses', sans-serif;
            color: #4B4B4B;
        }
        a, h1 {
            text-shadow: 1px 4px 5px rgba(51, 51, 51, 0.3);
        }
        @media (max-width: 515px) {
            .title-container {
                justify-content: center !important; /* centraliza no flex */
                text-align: center;
                width: 100%;
            }
        }
    </style>
</head>

<body class="d-flex flex-column h-100">
   
    <header class="bg-light py-3 mb-4 shadow">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Botão temporario de voltar -->
                @if (!Request::is('dashboard'))
                <div class="mt-0 d-flex shadow">
                    <a href="{{ route('dashboard') }}" class="btn btn-dark">&larr; Voltar</a>
                </div>
                @endif

                <!-- Título à esquerda -->
                <div class="d-flex align-items-center title-container">
                    <h1 class="h5 m-0">Estacionamento Inteligente</h1>
                </div>

                <!-- Mensagem de boas-vindas centralizada (apenas no dashboard) -->
                @if (Request::is('dashboard'))
                    <div class="position-absolute start-50 translate-middle-x d-none d-lg-block">
                        @auth 
                        <h1 class="h6 m-0">Bem-vindo(a), {{ Auth::user()->name }}!</h1>
                        @endauth
                    </div>
                    <div class="d-flex align-items-center gap-3 ms-auto me-4">
                        <a href="{{ route('painel.disponibilidade') }}"
                        class="d-flex gap-2 align-items-center text-decoration-none text-dark">
                            <h6 class="m-0">Painel</h6>
                            <img src="{{ asset('computador.png') }}" alt="Painel de Disponibilidade" class="img-fluid-1" style="width:30px; cursor:pointer;">
                        </a>
                    </div>
                @endif
                
                <!-- Botão de sair à direita -->
                <div class="d-flex align-items-center gap-2">
                    <a href="" class="d-flex gap-2 align-items-center text-decoration-none text-dark">
                        <h6 class="m-0">Sair</h6>
                        <img src="{{ asset('logout.png') }}" alt="Sair" class="img-fluid-1" style="width:30px; cursor:pointer;">
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container mb-5">
        @yield('content') <!--Não Alterar essa linha;-->
    </main>

    <footer class="footer bg-light py-3 mt-auto">
        <div class="container text-center">
            <p class="mb-0">
                &copy; {{ date('Y') }} Estacionamento Inteligente
                <span class="d-block">Todos os direitos reservados</span>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-PtN+2iA0wNf7G2/3DqL6L2L2K5y6t0k9jF8y1tXk0K9tX0k9F8y2L6k9J2L8K0j9" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}"></script>

</body>
</html>