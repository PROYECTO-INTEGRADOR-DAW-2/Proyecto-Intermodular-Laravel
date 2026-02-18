@extends('layouts.ecommerce')

@section('title', __('Productos'))

@section('header_actions')
    <a href="{{ route('equips.create') }}" class="btn btn--primary">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        {{ __('Nou Equip') }}
    </a>
@endsection

@section('content')


    
@endsection