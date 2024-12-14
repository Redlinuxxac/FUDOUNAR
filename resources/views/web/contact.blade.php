@extends('layouts.app')
@section('title')
   Contacto
@endsection
@section('menu')
    @include('menu', ['lugar' => 'Inicio'])
@endsection
@section('content')
<h1>Contáctanos</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('contact.store') }}">
        @csrf
        <div>
            <label for="name">Nombre:</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="message">Mensaje:</label>
            <textarea name="message" id="message" required></textarea>
        </div>
        <button type="submit">Enviar</button>
    </form>
@endsection