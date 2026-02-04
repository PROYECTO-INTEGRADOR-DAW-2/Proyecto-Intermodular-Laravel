@extends('layouts.ecommerce')

@section('title', 'Carrito de Compras')

@section('content')
<main class="container my-5">
    <h1 class="text-center mb-5">Tu Carrito de Compras</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(count($cart) > 0)
        <div class="table-responsive shadow-sm border rounded">
            <table class="table table-hover align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="py-3">Producto</th>
                        <th scope="col" class="py-3">Precio</th>
                        <th scope="col" class="py-3">Cantidad</th>
                        <th scope="col" class="py-3">Subtotal</th>
                        <th scope="col" class="py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $id => $details)
                        <tr>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    @if($details['image'])
                                        <img src="{{ Str::startsWith($details['image'], 'http') ? $details['image'] : (Str::startsWith($details['image'], 'img/') ? asset($details['image']) : asset('img/' . $details['image'])) }}" 
                                             alt="{{ $details['name'] }}" 
                                             class="img-thumbnail me-3" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $details['name'] }}</div>
                                        <div class="text-muted small">Talla: {{ $details['size'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">{{ number_format($details['price'], 2) }} €</td>
                            <td class="py-3">
                                <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex" style="max-width: 150px;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control form-control-sm text-center me-2" min="1" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td class="py-3">{{ number_format($details['price'] * $details['quantity'], 2) }} €</td>
                            <td class="py-3">
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold py-3 fs-5">Total:</td>
                        <td colspan="2" class="fw-bold py-3 fs-5 text-start ps-3">{{ number_format($total, 2) }} €</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Continuar comprando
            </a>
            <div>
                 <a href="{{ route('cart.clear') }}" class="btn btn-outline-danger me-2">Vaciar Carrito</a>
                 <button class="btn btn-success px-4" onclick="alert('Funcionalidad de pago no implementada aún.')">Finalizar Compra</button>
            </div>
        </div>
    @else
        <div class="text-center py-5 bg-light rounded shadow-sm">
            <div class="mb-3 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            </div>
            <h3>Tu carrito está vacío</h3>
            <p class="text-muted mb-4">¡Añade algunos productos para empezar!</p>
            <a href="{{ route('products.index') }}" class="button text-decoration-none d-inline-block px-4 py-2" style="background-color: #000; color: #fff;">Ver Productos</a>
        </div>
    @endif
</main>
@endsection
