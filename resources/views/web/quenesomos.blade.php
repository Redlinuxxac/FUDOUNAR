@extends('layouts.app')
@section('title')
    Inicio
@endsection
@section('menu')
    @include('menu', ['lugar' => 'Inicio'])
@endsection
@section('content')
<div class="row">
    <div class="noti">
        <h1>Mision</h1>
        <img src="https://picsum.photos/200/300" alt="Imagen de ejemplo">
        <p>texto.</p>                
    </div>
</div>
@endsection