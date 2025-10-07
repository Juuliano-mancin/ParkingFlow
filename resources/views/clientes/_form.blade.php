{{-- resources/views/clientes/_form.blade.php --}}
@csrf

<div class="mb-3">
    <label for="nomeCliente" class="form-label">Nome Fantasia</label>
    <input type="text" name="nomeCliente" id="nomeCliente" class="form-control" 
           value="{{ old('nomeCliente', $cliente->nomeCliente ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="razaoSocial" class="form-label">Razão Social</label>
    <input type="text" name="razaoSocial" id="razaoSocial" class="form-control" 
           value="{{ old('razaoSocial', $cliente->razaoSocial ?? '') }}" required>
</div>

<div class="mb-3">
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

<div class="mb-3">
    <label for="segmentoAtuacao" class="form-label">Segmento de Atuação</label>
    <input type="text" name="segmentoAtuacao" id="segmentoAtuacao" class="form-control"
           value="{{ old('segmentoAtuacao', $cliente->segmentoAtuacao ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="representanteCliente" class="form-label">Representante Cliente</label>
    <input type="text" name="representanteCliente" id="representanteCliente" class="form-control"
           value="{{ old('representanteCliente', $cliente->representanteCliente ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="emailCliente" class="form-label">E-mail Representante</label>
    <input type="email" name="emailCliente" id="emailCliente" class="form-control"
           value="{{ old('emailCliente', $cliente->emailCliente ?? '') }}">
</div>

<div class="mb-3">
    <label for="contatoCliente" class="form-label">Contato Representante</label>
    <input type="text" name="contatoCliente" id="contatoCliente" class="form-control"
           value="{{ old('contatoCliente', $cliente->contatoCliente ?? '') }}">
</div>

<div class="mb-3">
    <label for="cepCliente" class="form-label">CEP</label>
    <input type="text" name="cepCliente" id="cepCliente" class="form-control"
           value="{{ old('cepCliente', $cliente->cepCliente ?? '') }}">
</div>

<div class="mb-3">
    <label for="logradouroCliente" class="form-label">Logradouro</label>
    <input type="text" name="logradouroCliente" id="logradouroCliente" class="form-control"
           value="{{ old('logradouroCliente', $cliente->logradouroCliente ?? '') }}">
</div>

<div class="mb-3">
    <label for="numeroCliente" class="form-label">Número</label>
    <input type="text" name="numeroCliente" id="numeroCliente" class="form-control"
           value="{{ old('numeroCliente', $cliente->numeroCliente ?? '') }}">
</div>

<div class="mb-3">
    <label for="complementoCliente" class="form-label">Complemento</label>
    <input type="text" name="complementoCliente" id="complementoCliente" class="form-control"
           value="{{ old('complementoCliente', $cliente->complementoCliente ?? '') }}">
</div>

<div class="mb-3">
    <label for="cidadeCliente" class="form-label">Cidade</label>
    <input type="text" name="cidadeCliente" id="cidadeCliente" class="form-control"
           value="{{ old('cidadeCliente', $cliente->cidadeCliente ?? '') }}">
</div>

<div class="mb-3">
    <label for="ufCliente" class="form-label">UF</label>
    <input type="text" name="ufCliente" id="ufCliente" class="form-control" maxlength="2"
           value="{{ old('ufCliente', $cliente->ufCliente ?? '') }}">
</div>

<button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Salvar' }}</button>