@extends('layouts.ecommerce')

@section('title', 'Profile')

@section('header')
    <div class="container-fluid py-3 bg-white">
        <div class="container">
            <h2 class="h4 font-weight-bold mb-0 text-dark">
                {{ __('Profile') }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
