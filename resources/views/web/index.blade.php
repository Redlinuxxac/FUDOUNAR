@extends('layouts.app')
@section('title')
    Inicio
@endsection
@section('menu')
    @include('menu', ['lugar' => 'Inicio'])
@endsection
@section('content')
<h2>Proximas Actividades</h2>
@foreach ($preview_posts as $post)    
    <div class="row">
        <div class="noti">
            <h1>{{ $post->title }}</h1>
                <img src="img/{{ $post->imagen }}" alt="Imagen de ejemplo">
                <p>{!! substr($post->body,0,1000) !!}...</p>
        </div>
    </div>
@endforeach
<h2>Actividades Realizadas</h2>
@foreach ($posts as $post)    
    <div class="row">
        <div class="noti">
            <h1>{{ $post->title }}</h1>
                <img src="img/{{ $post->imagen }}" alt="Imagen de ejemplo">
                <p>{!! substr($post->body,0,1000) !!}...</p>
        </div>
    </div>
@endforeach
@endsection
@section('footer')
<footer>
    <p>&copy; FUDOUNAR 2023</p>
</footer>
@endsection
@section('script')
<script src="{{ asset('assets/js/script.js') }}"></script>
<script>
    // Replace 'script nuestro' with the actual location you want to display on the map
  </script>
@endsection 