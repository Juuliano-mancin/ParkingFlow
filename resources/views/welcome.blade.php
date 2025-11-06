<!DOCTYPE html>
<html lang="pt-br">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ParkingFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Lobster&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Satisfy&family=Shadows+Into+Light&family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Lobster&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Satisfy&family=Shadows+Into+Light&family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <style>
        h4{
            font-family: 'Noto Sans', sans-serif;
            color: #002B5B;
        }
        label {
            color: #4B4B4B;
        }
        label, button[type="submit"], input::placeholder {
            font-family: 'Nunito', sans-serif;
        }
        button[type="submit"] {
            color: #F2C200;
            background-color: #002B5B;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        h4, label {
            text-shadow: 1px 2px 3px rgba(0,0,0,0.25);
        }
        @media (max-width: 767px) {
            .col-12.col-md-6.text-center.p-3 {
                display: none;
            }
            .card-custom {
                width: 90% !important; /* Sobrescreve o width inline */
                margin: 0 auto;        /* Centraliza horizontalmente */
                padding: 1rem !important; /* Reduz padding em telas menores */
            }
        }
    </style>
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow-lg card-custom p-4" style="width: 60rem; min-height: 20rem;">
        <div class="row align-items-center g-0">
            <div class="col-12 col-md-6 text-center p-3">
                <img src="{{ asset('logotipo.jpg') }}" alt="Logo ParkingFlow" class="img-fluid border border-2 border-secondary rounded shadow" style="width: 95%;">
            </div>

            <div class="col-12 col-md-6 p-4">
                <h4 class="text-center mb-4 mt-2">Acesso Administrativo</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="w-100">
                    @csrf
                    <div class="mb-3 w-100">
                        <label for="email" class="form-label">E-mail:</label>
                        <input type="email" class="form-control form-control-lg shadow" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="mb-3 w-100">
                        <label for="password" class="form-label">Senha:</label>
                        <input type="password" class="form-control form-control-lg shadow" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-3 shadow">Entrar</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>