@extends('layouts.ecommerce')

@section('title', $product->nombre)

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none text-muted">Productos</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->nombre }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Imagen del Producto -->
        <div class="col-md-6">
            <div class="p-4 bg-white rounded shadow-sm d-flex align-items-center justify-content-center border" style="min-height: 400px;">
                 @if($product->oferta)
                    <span class="badge bg-danger position-absolute top-0 start-0 m-3 fs-5 px-3 py-2" style="z-index: 10;">Oferta</span>
                 @endif
                 
                 @if($product->img)
                    <img src="{{ Str::startsWith($product->img, 'http') ? $product->img : (Str::startsWith($product->img, 'img/') ? asset($product->img) : asset('img/' . $product->img)) }}" 
                         alt="{{ $product->nombre }}" 
                         class="img-fluid" 
                         style="max-height: 400px; object-fit: contain;">
                 @else
                    <div class="text-center text-muted">
                        <i class="bi bi-image" style="font-size: 5rem;"></i>
                        <p>Sin imagen</p>
                    </div>
                 @endif
            </div>
        </div>

        <!-- Detalles del Producto -->
        <div class="col-md-6">
            <h6 class="text-muted text-uppercase fw-bold">{{ $product->marca }}</h6>
            <h1 class="display-5 fw-bold mb-3">{{ $product->nombre }}</h1>
            
            <div class="d-flex align-items-center mb-4">
                <span class="fs-2 fw-bold text-dark me-3">{{ number_format($product->precio, 2) }} €</span>
                @if($product->oferta)
                    <span class="fs-5 text-muted text-decoration-line-through">{{ number_format($product->precio * 1.2, 2) }} €</span>
                @endif
            </div>

            <p class="lead mb-4">{{ $product->descripcion ?? 'Sin descripción disponible para este producto.' }}</p>

            <div class="mb-4">
                <span class="badge bg-light text-dark border me-2 p-2">{{ $product->sexo }}</span>
                <span class="badge bg-light text-dark border me-2 p-2">{{ $product->categoria }}</span>
                <span class="badge {{ $product->stock > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} p-2">
                    {{ $product->stock > 0 ? 'En Stock' : 'Agotado' }}
                </span>
            </div>

            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                @csrf
                
                @if($product->stock > 0)
                    <div class="mb-4">
                        <label class="form-label fw-bold">Selecciona tu talla:</label>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                // Determinar tallas según categoría
                                $sizes = [];
                                $catLower = strtolower($product->categoria);
                                if (str_contains($catLower, 'zapatillas') || str_contains($catLower, 'calzado') || str_contains($catLower, 'botas')) {
                                    $sizes = range(38, 46);
                                } elseif (str_contains($catLower, 'calcetines')) {
                                    $sizes = ['S (34-38)', 'M (38-42)', 'L (42-46)'];
                                } else {
                                    // Ropa general
                                    $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                }
                            @endphp

                            @foreach($sizes as $size)
                                <input type="radio" class="btn-check" name="talla" id="size_{{ $loop->index }}" value="{{ $size }}" {{ $loop->first ? 'checked' : '' }}>
                                <label class="btn btn-outline-dark" for="size_{{ $loop->index }}">{{ $size }}</label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <button type="submit" class="btn btn-dark btn-lg px-4 flex-grow-1" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                       <i class="bi bi-cart-plus me-2"></i> Añadir al carrito
                    </button>
                </div>
            </form>

            <hr class="my-5">

            <div class="small text-muted">
                <p class="mb-1"><i class="bi bi-truck me-2"></i> Envío gratis en pedidos superiores a 50€</p>
                <p class="mb-1"><i class="bi bi-arrow-repeat me-2"></i> Devoluciones gratuitas en 30 días</p>
                <p class="mb-0"><i class="bi bi-shield-check me-2"></i> Garantía de 2 años</p>
            </div>
        </div>
    </div>

    @if($relatedProducts->count() > 0)
        <hr class="my-5">
        <h3 class="mb-4 fw-bold">Productos Relacionados</h3>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
            @foreach($relatedProducts as $related)
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="bg-white p-3 rounded-top d-flex align-items-center justify-content-center" style="height: 200px;">
                            @if($related->img)
                                <img src="{{ Str::startsWith($related->img, 'http') ? $related->img : (Str::startsWith($related->img, 'img/') ? asset($related->img) : asset('img/' . $related->img)) }}" 
                                     class="img-fluid" 
                                     style="max-height: 100%; object-fit: contain;">
                            @endif
                        </div>
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate"><a href="{{ route('products.show', $related->id) }}" class="text-decoration-none text-dark">{{ $related->nombre }}</a></h6>
                            <p class="card-text fw-bold">{{ number_format($related->precio, 2) }} €</p>
                            <a href="{{ route('products.show', $related->id) }}" class="btn btn-sm btn-outline-dark w-100">Ver producto</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
