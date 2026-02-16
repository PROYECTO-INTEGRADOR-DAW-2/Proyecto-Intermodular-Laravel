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
        <!-- Vista Desktop: Tabla Premium -->
        <div class="d-none d-md-block table-responsive shadow-sm border rounded-4 overflow-hidden mb-5">
            <table class="table table-hover align-middle mb-0 bg-white">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="py-4 ps-5">PRODUCTO</th>
                        <th scope="col" class="py-4 text-center">TALLA</th>
                        <th scope="col" class="py-4 text-center">CANTIDAD</th>
                        <th scope="col" class="py-4 text-center">PRECIO</th>
                        <th scope="col" class="py-4 text-center">SUBTOTAL</th>
                        <th scope="col" class="py-4 text-center pe-5">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $id => $details)
                        @php
                            $detailsImage = $details['image'];
                            $detailsImageUrl = $detailsImage;
                            if ($detailsImage && !\Illuminate\Support\Str::startsWith($detailsImage, ['http://', 'https://'])) {
                                if (!\Illuminate\Support\Str::startsWith($detailsImage, ['img', 'imgNike', 'imgAdidas', 'imgPuma', 'imgAsics'])) {
                                    $pModel = $productsModels[$details['product_id']] ?? null;
                                    $folder = 'img';
                                    if ($pModel) {
                                        $m = strtolower($pModel->marca);
                                        if (str_contains($m, 'nike')) $folder = 'imgNike';
                                        elseif (str_contains($m, 'adidas')) $folder = 'imgAdidas';
                                        elseif (str_contains($m, 'puma')) $folder = 'imgPuma';
                                        elseif (str_contains($m, 'asics')) $folder = 'imgAsics';
                                    }
                                    $detailsImageUrl = asset($folder . '/' . $detailsImage);
                                } else {
                                    $detailsImageUrl = asset($detailsImage);
                                }
                            }
                            $product = $productsModels[$details['product_id']] ?? null;
                        @endphp
                        <tr>
                            <td class="py-4 ps-5">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-3 p-2 me-4 shadow-sm" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                                        @if($detailsImageUrl)
                                            <img src="{{ $detailsImageUrl }}" alt="{{ $details['name'] }}" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                                        @else
                                            <i class="bi bi-image text-muted"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold fs-5">{{ $details['name'] }}</div>
                                        <div class="text-muted small">ID: #{{ $details['product_id'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-center">
                                <form action="{{ route('cart.update', $id) }}" method="POST" class="d-inline-block" style="width: 100px;">
                                    @csrf
                                    @method('PATCH')
                                    @php
                                        $sizes = [];
                                        if ($product) {
                                            $catLower = strtolower($product->categoria);
                                            if (str_contains($catLower, 'zapatillas') || str_contains($catLower, 'calzado') || str_contains($catLower, 'botas')) {
                                                $sizes = range(38, 46);
                                            } elseif (str_contains($catLower, 'calcetines')) {
                                                $sizes = ['S (34-38)', 'M (38-42)', 'L (42-46)'];
                                            } else {
                                                $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                            }
                                        } else {
                                            $sizes = [$details['size']];
                                        }
                                    @endphp
                                    <select name="size" class="form-select border-2 fw-bold text-center" onchange="this.form.submit()">
                                        @foreach($sizes as $size)
                                            <option value="{{ $size }}" {{ $details['size'] == $size ? 'selected' : '' }}>{{ $size }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="py-4 text-center">
                                <form action="{{ route('cart.update', $id) }}" method="POST" class="d-inline-block" style="width: 80px;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control border-2 text-center fw-bold" min="1" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td class="py-4 text-center fs-5">{{ number_format($details['price'], 2) }} €</td>
                            <td class="py-4 text-center fs-5 fw-bold text-success">{{ number_format($details['price'] * $details['quantity'], 2) }} €</td>
                            <td class="py-4 text-center pe-5">
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-2 shadow-sm" title="Eliminar">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Vista Móvil: Tarjetas (d-md-none) -->
        <div class="row row-cols-1 g-4 d-md-none">
            @foreach($cart as $id => $details)
                @php
                    $detailsImage = $details['image'];
                    $detailsImageUrl = $detailsImage;
                    if ($detailsImage && !\Illuminate\Support\Str::startsWith($detailsImage, ['http://', 'https://'])) {
                        if (!\Illuminate\Support\Str::startsWith($detailsImage, ['img', 'imgNike', 'imgAdidas', 'imgPuma', 'imgAsics'])) {
                            $pModel = $productsModels[$details['product_id']] ?? null;
                            $folder = 'img';
                            if ($pModel) {
                                $m = strtolower($pModel->marca);
                                if (str_contains($m, 'nike')) $folder = 'imgNike';
                                elseif (str_contains($m, 'adidas')) $folder = 'imgAdidas';
                                elseif (str_contains($m, 'puma')) $folder = 'imgPuma';
                                elseif (str_contains($m, 'asics')) $folder = 'imgAsics';
                            }
                            $detailsImageUrl = asset($folder . '/' . $detailsImage);
                        } else {
                            $detailsImageUrl = asset($detailsImage);
                        }
                    }
                    $product = $productsModels[$details['product_id']] ?? null;
                @endphp
                <div class="col">
                    <div class="card h-100 shadow border-0 rounded-4 overflow-hidden">
                        <div class="bg-light d-flex align-items-center justify-content-center p-4" style="min-height: 280px;">
                            @if($detailsImageUrl)
                                <img src="{{ $detailsImageUrl }}" alt="{{ $details['name'] }}" class="img-fluid" style="max-height: 220px; object-fit: contain;">
                            @else
                                <i class="bi bi-image fs-1 text-muted"></i>
                            @endif
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="fw-bold fs-4 mb-0 text-dark">{{ $details['name'] }}</h3>
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-2 shadow-sm" title="Eliminar">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="row g-3 mb-4 mt-auto">
                                <div class="col-6">
                                    <label class="small text-muted fw-bold d-block mb-1">Talla</label>
                                    <form action="{{ route('cart.update', $id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @php
                                            $sizes = [];
                                            if ($product) {
                                                $catLower = strtolower($product->categoria);
                                                if (str_contains($catLower, 'zapatillas') || str_contains($catLower, 'calzado') || str_contains($catLower, 'botas')) {
                                                    $sizes = range(38, 46);
                                                } elseif (str_contains($catLower, 'calcetines')) {
                                                    $sizes = ['S (34-38)', 'M (38-42)', 'L (42-46)'];
                                                } else {
                                                    $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                                }
                                            } else {
                                                $sizes = [$details['size']];
                                            }
                                        @endphp
                                        <select name="size" class="form-select border-2 fw-bold" onchange="this.form.submit()">
                                            @foreach($sizes as $size)
                                                <option value="{{ $size }}" {{ $details['size'] == $size ? 'selected' : '' }}>{{ $size }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted fw-bold d-block mb-1">Cantidad</label>
                                    <form action="{{ route('cart.update', $id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control border-2 text-center fw-bold" min="1" onchange="this.form.submit()">
                                    </form>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
                                <div>
                                    <span class="text-muted small fw-bold">Subtotal:</span>
                                    <div class="fs-3 fw-bold text-success">{{ number_format($details['price'] * $details['quantity'], 2) }} €</div>
                                    <span class="text-muted extra-small">({{ number_format($details['price'], 2) }} € / ud)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Resumen de Total Unificado -->
        <div class="row mt-5 justify-content-center justify-content-md-end">
            <div class="col-12 col-md-5 col-xl-4">
                <div class="card border-0 shadow rounded-4 bg-dark text-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <span class="fs-3 fw-bold">TOTAL:</span>
                        <span class="fs-1 fw-bold text-success">{{ number_format($total, 2) }} €</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary order-2 order-md-1">
                <i class="bi bi-arrow-left"></i> Continuar comprando
            </a>
            <div class="d-flex flex-column flex-md-row gap-2 order-1 order-md-2">
                 <a href="{{ route('cart.clear') }}" class="btn btn-outline-danger">Vaciar Carrito</a>
                 <a href="{{ route('cart.checkout') }}" class="btn btn-success px-4 fw-bold shadow-sm">Finalizar Compra</a>
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

    <!-- Lista de Deseos -->
    <div class="mt-5 pt-5 pb-5">
        <h2 class="mb-4 text-center text-md-start fw-bold">Tu Lista de Deseos</h2>
        @if(count($wishlist) > 0)
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach($wishlist as $id => $item)
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="bg-light p-4 d-flex align-items-center justify-content-center" style="height: 250px;">
                                @php
                                    $itemImage = $item['image'];
                                    $itemImageUrl = $itemImage;
                                    if ($itemImage && !\Illuminate\Support\Str::startsWith($itemImage, ['http://', 'https://'])) {
                                        if (!\Illuminate\Support\Str::startsWith($itemImage, ['img', 'imgNike', 'imgAdidas', 'imgPuma', 'imgAsics'])) {
                                            $itemImageUrl = asset('imgNike/' . $itemImage);
                                        } else {
                                            $itemImageUrl = asset($itemImage);
                                        }
                                    }
                                @endphp
                                @if($itemImageUrl)
                                    <img src="{{ $itemImageUrl }}" 
                                         alt="{{ $item['name'] }}" 
                                         class="img-fluid" 
                                         style="max-height: 200px; object-fit: contain;">
                                @else
                                    <i class="bi bi-image fs-1 text-muted"></i>
                                @endif
                            </div>
                            <div class="card-body p-4 d-flex flex-column text-center">
                                <h5 class="fw-bold mb-2">{{ $item['name'] }}</h5>
                                <p class="fw-bold fs-5 text-success mb-3">{{ number_format($item['price'], 2) }} €</p>
                                
                                <div class="mt-auto d-grid gap-2">
                                    <form action="{{ route('wishlist.move', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-dark w-100 py-2 fw-bold" style="font-size: 0.9rem;">Mover al carrito</button>
                                    </form>
                                    <form action="{{ route('wishlist.remove', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger w-100 py-2" style="font-size: 0.9rem;">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-light border text-center py-4">
                <p class="text-muted mb-0">No tienes productos en tu lista de deseos.</p>
            </div>
        @endif
    </div>
</main>
@endsection
