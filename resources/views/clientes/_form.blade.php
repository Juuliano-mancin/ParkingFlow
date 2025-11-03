{{-- resources/views/clientes/_form.blade.php --}}
@csrf

<style>
    .underline-title {
        position: relative;
        padding-bottom: 0.6rem;
    }
    .underline-title::after {
        content: "";
        display: block;
        width: 100%;          
        height: 1px;           
        color: #F2C200;    
        border-radius: 2px;
        margin: 0.8rem 0; 
    }
    button[type="submit"] {
        color: #F2C200;
        background-color: #002B5B;
    }
    button[type="submit"]:hover {
        background-color: #0056b3;
    }
    label {
        text-shadow: 1px 2px 3px rgba(0,0,0,0.25);
        color: #4B4B4B;
    }
    input {
        box-shadow: 3px 3px 3px rgba(184, 184, 184, 0.25);
    }
</style>

<div class="row d-flex">
    <div class="col-md-6 mb-3">
        <label for="nomeCliente" class="form-label">Nome Fantasia</label>
        <input type="text" name="nomeCliente" id="nomeCliente" class="form-control" 
            value="{{ old('nomeCliente', $cliente->nomeCliente ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="razaoSocial" class="form-label">Razão Social</label>
        <input type="text" name="razaoSocial" id="razaoSocial" class="form-control" 
            value="{{ old('razaoSocial', $cliente->razaoSocial ?? '') }}" required>
    </div>
</div>

<div class="row d-flex">
    <div class="col-md-6 mb-3">
            <label for="setorAtuacao" class="form-label">Setor de Atuação</label>
            <select name="setorAtuacao" id="setorAtuacao" class="form-select">
                @php
                    $setores = ['comercial', 'industrial', 'residencial', 'outros'];
                    $selectedSetor = old('setorAtuacao', $cliente->setorAtuacao ?? 'comercial');
                @endphp
                @foreach($setores as $setor)
                    <option value="{{ $setor }}" {{ $selectedSetor === $setor ? 'selected' : '' }}>
                        {{ ucfirst($setor) }}
                    </option>
                @endforeach
            </select> 
        </div>

    <div class="col-md-6 mb-3">
        <label for="segmentoAtuacao" class="form-label">Segmento de Atuação</label>
        <input type="text" name="segmentoAtuacao" id="segmentoAtuacao" class="form-control"
            value="{{ old('segmentoAtuacao', $cliente->segmentoAtuacao ?? '') }}" required>
    </div>
</div>

<div class="row d-flex">
    <div class="col-md-4 mb-3">
        <label for="representanteCliente" class="form-label">Representante Cliente</label>
        <input type="text" name="representanteCliente" id="representanteCliente" class="form-control"
            value="{{ old('representanteCliente', $cliente->representanteCliente ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label for="emailCliente" class="form-label">E-mail Representante</label>
        <input type="email" name="emailCliente" id="emailCliente" class="form-control"
            value="{{ old('emailCliente', $cliente->emailCliente ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="contatoCliente" class="form-label">Contato Representante</label>
        <input type="text" name="contatoCliente" id="contatoCliente" class="form-control"
            value="{{ old('contatoCliente', $cliente->contatoCliente ?? '') }}">
    </div>
</div>

<div class="row d-flex">
    <div class="col-md-4 mb-3">
        <label for="cepCliente" class="form-label">CEP</label>
        <input type="text" name="cepCliente" id="cepCliente" class="form-control"
            value="{{ old('cepCliente', $cliente->cepCliente ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="logradouroCliente" class="form-label">Logradouro</label>
        <input type="text" name="logradouroCliente" id="logradouroCliente" class="form-control"
            value="{{ old('logradouroCliente', $cliente->logradouroCliente ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="numeroCliente" class="form-label">Número</label>
        <input type="text" name="numeroCliente" id="numeroCliente" class="form-control"
            value="{{ old('numeroCliente', $cliente->numeroCliente ?? '') }}">
    </div>
</div>

<div class="row d-flex">
    <div class="col-md-4 mb-3">
        <label for="complementoCliente" class="form-label">Complemento</label>
        <input type="text" name="complementoCliente" id="complementoCliente" class="form-control"
            value="{{ old('complementoCliente', $cliente->complementoCliente ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="cidadeCliente" class="form-label">Cidade</label>
        <input type="text" name="cidadeCliente" id="cidadeCliente" class="form-control"
            value="{{ old('cidadeCliente', $cliente->cidadeCliente ?? '') }}">
    </div>

    <div class="col-md-4 mb-2">
        <label for="ufCliente" class="form-label">UF</label>
        <input type="text" name="ufCliente" id="ufCliente" class="form-control" maxlength="2"
            value="{{ old('ufCliente', $cliente->ufCliente ?? '') }}">
    </div>
</div>

<div class="underline-title"></div>

<div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary shadow">
        {{ $buttonText ?? 'Salvar' }}
    </button>
</div>

<script>
let cepTimer;

document.getElementById('cepCliente').addEventListener('input', function () {
    clearTimeout(cepTimer); // evita múltiplas requisições

    const cep = this.value.replace(/\D/g, '');

    if (cep.length === 8) {
        cepTimer = setTimeout(() => {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('logradouroCliente').value = data.logradouro;
                        document.getElementById('cidadeCliente').value = data.localidade;
                        document.getElementById('ufCliente').value = data.uf;
                    }
                })
                .catch(() => {
                    console.log("Erro ao buscar o CEP.");
                });
        }, 300); // pequeno atraso para não disparar múltiplas vezes
    }
});
</script>