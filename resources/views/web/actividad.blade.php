@extends('layouts.app')
@section('title')
    Actividades
@endsection
@section('menu')
    @include('menu', ['lugar' => 'Inicio'])
@endsection
@section('content')
<h2>Proxima Activadad</h2>
<div class="row">
    <div class="noti">
        <h1>Bienvenido a FUDOUNAR</h1>
        <img src="https://picsum.photos/200/300" alt="Imagen de ejemplo">
        <p>Este es un texto de ejemplo para rellenar el contenido de la página. Aquí puedes colocar información sobre tu organización, sus objetivos y actividades.</p>                
    </div>
</div>
<h2>Activadad Realizada</h2>
@endsection
@section('footer')
<footer>
    <p>&copy; FUDOUNAR 2023</p>
</footer>
@endsection
@section('script')
<script src="{{ asset('assets/js/script.js') }}"></script>
<script>
    // Replace 'YOUR_LOCATION_NAME' with the actual location you want to display on the map
    //document.getElementById("gmap_iframe").src = "https://www.google.com/maps/embed?pb=!1m18&q=" + encodeURIComponent('YOUR_LOCATION_NAME') + "&zoom=15&output=embed";
  </script>
@endsection 