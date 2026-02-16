@extends('layouts.ecommerce')

@section('title', 'Finalizar Compra')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white p-3 text-center">
                    <h5 class="mb-0">Datos de Envío y Pago</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('cart.process') }}" method="POST">
                        @csrf
                        <h5 class="mb-3">Información de Envío</h5>
                        <div class="row g-3">
                            @guest
                                <div class="col-12">
                                    <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="tu@email.com" required>
                                    <div class="form-text">Enviaremos la confirmación de tu pedido a esta dirección.</div>
                                </div>
                            @endguest
                            <div class="col-12">
                                <label for="name" class="form-label fw-bold">Nombre Completo</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ auth()->check() ? auth()->user()->nombre . ' ' . auth()->user()->apellidos : '' }}" required>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label fw-bold">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Calle, número, piso..." required>
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label fw-bold">Ciudad</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-6">
                                <label for="zip" class="form-label fw-bold">Código Postal</label>
                                <input type="text" class="form-control" id="zip" name="zip" required>
                            </div>
                            <div class="col-12">
                                <label for="phone" class="form-label fw-bold">Teléfono de Contacto</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Información de Pago</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="card_name" class="form-label fw-bold">Nombre en la tarjeta</label>
                                <input type="text" class="form-control" id="card_name" name="card_name" placeholder="Como aparece en la tarjeta" required>
                            </div>
                            <div class="col-12">
                                <label for="card_number" class="form-label fw-bold">Número de tarjeta</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="card_expiry" class="form-label fw-bold">Fecha de expiración</label>
                                <input type="text" class="form-control" id="card_expiry" name="card_expiry" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="col-md-6">
                                <label for="card_cvv" class="form-label fw-bold">CVV</label>
                                <input type="text" class="form-control" id="card_cvv" name="card_cvv" placeholder="123" maxlength="4" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg py-3">
                                Confirmar y Pagar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow border-0 rounded-4 overflow-hidden sticky-top" style="top: 2rem;">
                <div class="card-header bg-dark text-white p-3 text-center">
                    <h5 class="mb-0 fw-bold">Resumen del Pedido</h5>
                </div>
                <div class="card-body p-4">
                    @foreach($cart as $id => $item)
                        @php
                            $pImg = $item['image'];
                            $pImgUrl = $pImg;
                            if ($pImg && !\Illuminate\Support\Str::startsWith($pImg, ['http://', 'https://'])) {
                                if (!\Illuminate\Support\Str::startsWith($pImg, ['img', 'imgNike', 'imgAdidas', 'imgPuma', 'imgAsics'])) {
                                    $pModel = $productsModels[$item['product_id']] ?? null;
                                    $fld = 'img';
                                    if ($pModel) {
                                        $m = strtolower($pModel->marca);
                                        if (str_contains($m, 'nike')) $fld = 'imgNike';
                                        elseif (str_contains($m, 'adidas')) $fld = 'imgAdidas';
                                        elseif (str_contains($m, 'puma')) $fld = 'imgPuma';
                                        elseif (str_contains($m, 'asics')) $fld = 'imgAsics';
                                    }
                                    $pImgUrl = asset($fld . '/' . $pImg);
                                } else {
                                    $pImgUrl = asset($pImg);
                                }
                            }
                        @endphp
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-4 shadow-sm border position-relative">
                            @if($pImgUrl)
                                <img src="{{ $pImgUrl }}" alt="{{ $item['name'] }}" class="img-thumbnail me-3 bg-white shadow-sm" style="width: 100px; height: 100px; object-fit: contain; border-radius: 12px;">
                            @endif
                            <div class="flex-grow-1">
                                <div class="fw-bold fs-5 lh-1 mb-2">{{ $item['name'] }}</div>
                                <div class="text-muted small mb-1">
                                    Talla: <span class="fw-bold">{{ $item['size'] }}</span>
                                </div>
                                <div class="text-muted small mb-2">
                                    Cant: <span class="fw-bold text-dark">{{ $item['quantity'] }}</span>
                                </div>
                                <div class="fw-bold text-success fs-5">{{ number_format($item['price'] * $item['quantity'], 2) }} €</div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="p-4 bg-dark text-white rounded-4 mt-4 shadow text-center">
                        <div class="text-muted small mb-1 fw-bold">TOTAL A PAGAR</div>
                        <div class="fs-2 fw-bold text-success">{{ number_format($total, 2) }} €</div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-4">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">
                        <i class="bi bi-pencil-square"></i> Modificar Pedido
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
