@extends('layouts.app')
@section('title')
    Actividades
@endsection
@section('menu')
    @include('menu', ['lugar' => 'Inicio'])
@endsection
@section('content')
proxima activadad
<div class="row">
    <div class="noti">
        <h1>Bienvenido a FUDOUNAR</h1>
        <img src="https://picsum.photos/200/300" alt="Imagen de ejemplo">
        <p>Este es un texto de ejemplo para rellenar el contenido de la página. Aquí puedes colocar información sobre tu organización, sus objetivos y actividades.</p>                
    </div>
</div>
@endsection