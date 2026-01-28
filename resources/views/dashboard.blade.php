@extends('layouts.ecommerce')

@section('header')
    <div class="container-fluid py-3 bg-white shadow-sm">
        <div class="container">
            <h2 class="h4 font-weight-bold mb-0 text-dark">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <p class="mb-0 text-dark">{{ __("You're logged in!") }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
