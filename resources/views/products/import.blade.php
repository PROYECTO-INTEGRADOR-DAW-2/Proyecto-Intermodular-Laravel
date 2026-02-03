@extends("layouts.ecommerce")

@section('title', __('Importar productos'))

@section('content')

<main>
    <div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <x-input-label for="file" :value="__('Fichero csv, xlsx, xls')" />
                <x-text-input id="file" name="file" type="file" class="form-control" />
                <x-input-error :messages="$errors->get('file')" />
            </div>

            <x-primary-button>
                    {{ __('Importar') }}
            </x-primary-button>
        </form>
    </div>
    </div>




</main>


@endsection