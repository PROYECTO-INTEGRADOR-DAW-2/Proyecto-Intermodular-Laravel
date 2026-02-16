<form action="{{ route('products.index') }}" method="GET" id="filterForm{{ $idSuffix ?? '' }}">
    <!-- Buscador -->
    <div class="mb-4">
        <label class="form-label fw-semibold small text-muted">Buscar</label>
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Nombre o SKU..." value="{{ request('search') }}">
    </div>

    <!-- Filtro por Sexo -->
    <div class="mb-4">
        <label class="form-label fw-semibold small text-muted">Género</label>
        <div class="ms-1">
            @foreach($sexos as $sexo)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="sexo[]" value="{{ $sexo }}" id="sexo_{{ $loop->index }}{{ $idSuffix ?? '' }}"
                        {{ in_array($sexo, request('sexo', [])) ? 'checked' : '' }}>
                    <label class="form-check-label text-capitalize" for="sexo_{{ $loop->index }}{{ $idSuffix ?? '' }}">
                        {{ $sexo }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Filtro por Marca -->
    <div class="mb-4">
        <label class="form-label fw-semibold small text-muted">Marca</label>
        @foreach($marcas as $marca)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="marca[]" value="{{ $marca }}" id="marca_{{ $loop->index }}{{ $idSuffix ?? '' }}"
                    {{ in_array($marca, request('marca', [])) ? 'checked' : '' }}>
                <label class="form-check-label text-capitalize" for="marca_{{ $loop->index }}{{ $idSuffix ?? '' }}">
                    {{ $marca }}
                </label>
            </div>
        @endforeach
    </div>

    <!-- Filtro por Categoría -->
    <div class="mb-4">
        <label class="form-label fw-semibold small text-muted">Categoría</label>
        @foreach($categorias as $cat)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="categoria[]" value="{{ $cat }}" id="cat_{{ $loop->index }}{{ $idSuffix ?? '' }}"
                    {{ in_array($cat, request('categoria', [])) ? 'checked' : '' }}>
                <label class="form-check-label text-capitalize" for="cat_{{ $loop->index }}{{ $idSuffix ?? '' }}">
                    {{ $cat }}
                </label>
            </div>
        @endforeach
    </div>

    <!-- Filtro de Precio -->
    <div class="mb-4">
        <label class="form-label fw-semibold small text-muted">Precio Máximo: <span id="priceVal{{ $idSuffix ?? '' }}">{{ request('max_price', $maxPrice) }}</span> €</label>
        <input type="range" class="form-range" name="max_price" min="0" max="{{ $maxPrice }}" step="5" 
            value="{{ request('max_price', $maxPrice) }}" 
            oninput="document.getElementById('priceVal{{ $idSuffix ?? '' }}').innerText = this.value">
    </div>
    
    <!-- Solo Ofertas -->
    <div class="mb-4 form-check form-switch">
         <input class="form-check-input" type="checkbox" name="oferta" value="1" id="ofertaCheck{{ $idSuffix ?? '' }}" {{ request('oferta') ? 'checked' : '' }}>
         <label class="form-check-label fw-semibold" for="ofertaCheck{{ $idSuffix ?? '' }}">Solo Ofertas</label>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="button" style="background-color: #000; color: white;">Aplicar Filtros</button>
        @if(request()->keys())
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar todo</a>
        @endif
    </div>
</form>
