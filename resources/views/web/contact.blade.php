@extends('layouts.app')
@section('title')
   Contacto
@endsection
@section('menu')
    @include('menu', ['lugar' => 'Inicio'])
@endsection
@section('content')
<h1>Ubicación</h1><br>
<!-- <iframe
      width="600"
      height="400"
      id="gmap_iframe"
      src="https://www.google.com/maps/embed?pb=!1m18&q=YOUR_LOCATION_NAME+&zoom=15&output=embed"
      frameborder="0"
      allowfullscreen
    ></iframe> -->
<iframe 
      width="600"
      height="450"
      style="border:0;"
      allowfullscreen=""
      loading="lazy"
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2764.363459640466!2d-73.98472228476707!3d40.74844457904225!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a2c61073331%3A0xc809cce60ad8aa8c!2sNew%20York%26%252C+NY%252C+USA!5e0!3m2!1ses-419!2sar!4v1611633084847!5m2!1ses-419!2sar">
</iframe><br>
<h1>Contáctanos</h1><br>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('contact.store') }}" class="form-contro">
        @csrf
        <div  class="form-group">
            <label for="name">Nombre:</label>
            <input type="text" name="name" id="name"  class="form-control"  required>
        </div>
        <div  class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email"  class="form-control"  required>
        </div>
        <div  class="form-group">
            <label for="message">Mensaje:</label>
            <textarea name="message" id="message"  class="form-control"  required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
@endsection
@section('script')
<script>
    // Replace 'YOUR_LOCATION_NAME' with the actual location you want to display on the map
    //document.getElementById("gmap_iframe").src = "https://www.google.com/maps/embed?pb=!1m18&q=" + encodeURIComponent('YOUR_LOCATION_NAME') + "&zoom=15&output=embed";
  </script>
@endsection  