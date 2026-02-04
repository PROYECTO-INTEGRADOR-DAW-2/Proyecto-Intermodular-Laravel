@extends('layouts.ecommerce')

@section('title', 'Nuestros Productos')

@section('content')
<div class="container-fluid px-4 px-lg-5 my-5">
    <div class="row">
        <!-- Sidebar de Filtros -->
        <aside class="col-lg-3 col-xl-2 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0">Filtros</h5>
                </div>
                <div class="card-body px-3"> <!-- Reducido padding horizontal para aprovechar espacio pero sin pegar -->
                    <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                        
                        <!-- Buscador -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">Buscar</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Nombre o SKU..." value="{{ request('search') }}">
                        </div>

                        <!-- Filtro por Sexo -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">Género</label>
                            <div class="ms-1"> <!-- Añadido un pequeño margen interno en lugar de estar pegado -->
                                @foreach($sexos as $sexo)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sexo[]" value="{{ $sexo }}" id="sexo_{{ $loop->index }}"
                                            {{ in_array($sexo, request('sexo', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label text-capitalize" for="sexo_{{ $loop->index }}">
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
                                    <input class="form-check-input" type="checkbox" name="marca[]" value="{{ $marca }}" id="marca_{{ $loop->index }}"
                                        {{ in_array($marca, request('marca', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label text-capitalize" for="marca_{{ $loop->index }}">
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
                                    <input class="form-check-input" type="checkbox" name="categoria[]" value="{{ $cat }}" id="cat_{{ $loop->index }}"
                                        {{ in_array($cat, request('categoria', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label text-capitalize" for="cat_{{ $loop->index }}">
                                        {{ $cat }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Filtro de Precio -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">Precio Máximo: <span id="priceVal">{{ request('max_price', $maxPrice) }}</span> €</label>
                            <input type="range" class="form-range" name="max_price" min="0" max="{{ $maxPrice }}" step="5" 
                                value="{{ request('max_price', $maxPrice) }}" 
                                oninput="document.getElementById('priceVal').innerText = this.value">
                        </div>
                        
                        <!-- Solo Ofertas -->
                        <div class="mb-4 form-check form-switch">
                             <input class="form-check-input" type="checkbox" name="oferta" value="1" id="ofertaCheck" {{ request('oferta') ? 'checked' : '' }}>
                             <label class="form-check-label fw-semibold" for="ofertaCheck">Solo Ofertas</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="button" style="background-color: #000; color: white;">Aplicar Filtros</button>
                            @if(request()->keys())
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar todo</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Listado de Productos -->
        <div class="col-lg-9 col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Nuestros Productos</h2>
                <span class="text-muted small">{{ $products->count() }} resultados</span>
            </div>

            <div class="row">
                @forelse($products as $product)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 mb-4">
                        <div class="card h-100 shadow-sm border-0 product-card hover-zoom">
                            <div class="card-img-top d-flex align-items-center justify-content-center p-3 position-relative bg-white" style="height: 250px;">
                                 @if($product->oferta)
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-3 shadow-sm">Oferta</span>
                                 @endif
                                 
                                 <a href="{{ route('products.show', $product->id) }}" class="d-flex align-items-center justify-content-center h-100 w-100 text-decoration-none">
                                     @if($product->img)
                                        <img src="{{ Str::startsWith($product->img, 'http') ? $product->img : (Str::startsWith($product->img, 'img/') ? asset($product->img) : asset('img/' . $product->img)) }}" 
                                             alt="{{ $product->nombre }}" 
                                             class="img-fluid" 
                                             style="max-height: 100%; object-fit: contain; transition: transform 0.3s ease;">
                                     @else
                                        <div class="text-muted d-flex flex-column align-items-center justify-content-center h-100 w-100 bg-light rounded">
                                            <i class="bi bi-image fs-1 mb-2"></i>
                                            <span>Sin imagen</span>
                                        </div>
                                     @endif
                                 </a>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <small class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.75rem;">{{ $product->marca }}</small>
                                <h5 class="card-title text-truncate mb-1" title="{{ $product->nombre }}">
                                    <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark stretched-link">{{ $product->nombre }}</a>
                                </h5>
                                <div class="mb-2">
                                     <span class="badge bg-light text-dark border">{{ $product->sexo }}</span>
                                     <span class="badge bg-light text-dark border">{{ $product->categoria }}</span>
                                </div>
                                
                                <div class="mt-auto pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fw-bold fs-5">{{ number_format($product->precio, 2) }} €</span>
                                        @if($product->oferta)
                                            <small class="text-danger text-decoration-line-through">Antes: {{ number_format($product->precio * 1.2, 2) }} €</small>
                                        @endif
                                    </div>
                                    <a href="{{ route('cart.add', $product->id) }}" class="button w-100 d-block text-center text-decoration-none py-2 rounded-pill" style="background-color: #000; color: #fff;">Añadir al carrito</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="py-5 bg-light rounded-3">
                            <h3 class="text-muted mb-3">No hay productos que coincidan con estos filtros</h3>
                            <a href="{{ route('products.index') }}" class="button text-decoration-none px-4 py-2" style="background-color: #000; color: #fff;">Ver todos los productos</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .hover-zoom:hover .card-img-top img {
        transform: scale(1.05);
    }
    .hover-zoom {
        transition: box-shadow 0.3s ease;
    }
    .hover-zoom:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endsection
