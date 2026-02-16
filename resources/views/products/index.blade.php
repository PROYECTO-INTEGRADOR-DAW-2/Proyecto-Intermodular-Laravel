@extends('layouts.ecommerce')

@section('title', 'Nuestros Productos')

@section('content')
<div class="container-fluid px-4 px-lg-5 my-5">
    <div class="row">
        <!-- Sidebar de Filtros (Desktop) -->
        <aside class="col-lg-3 col-xl-2 mb-4 d-none d-lg-block">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0">Filtros</h5>
                </div>
                <div class="card-body px-3">
                    @include('products.partials.filter-form')
                </div>
            </div>
        </aside>

        <!-- Botón Filtros (Móvil) -->
        <div class="col-12 d-lg-none mb-3">
            <button class="btn btn-dark w-100 d-flex align-items-center justify-content-center py-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFilters" aria-controls="offcanvasFilters">
                <i class="bi bi-filter-left fs-4 me-2"></i>
                <span class="fw-bold">Filtrar Productos</span>
            </button>
        </div>

        <!-- Offcanvas Filtros (Móvil) -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasFilters" aria-labelledby="offcanvasFiltersLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title fw-bold" id="offcanvasFiltersLabel">Filtros</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                @include('products.partials.filter-form', ['idSuffix' => '_mobile'])
            </div>
        </div>

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
                                     @if($product->image_url)
                                        <img src="{{ $product->image_url }}" 
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
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('products.show', $product->id) }}" class="button flex-grow-1 text-center text-decoration-none py-2 rounded-pill" style="background-color: #000; color: #fff;">Ver producto</a>
                                        <a href="{{ route('wishlist.add', $product->id) }}" class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center p-2" style="width: 40px; height: 40px;" title="Añadir a lista de deseos">
                                            <i class="bi bi-heart"></i>
                                        </a>
                                    </div>
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
