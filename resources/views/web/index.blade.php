@extends('layouts.app')
@section('title')
    Inicio
@endsection
@section('menu')
    @include('menu', ['lugar' => 'Inicio'])
@endsection
@section('content')
<h2 class="text-center">Proximas Actividades</h2>
<div class="row g-2">
    <div class="col-sm-12">
        <div class="row">
            @foreach ($preview_posts as $post)
            <div class="col-md-12">
                <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    @if ($loop->odd)
                    <div class="col-4 d-none d-sm-block d-lg-block">
                        <img src="img/{{ $post->imagen }}" class="img-fluid" alt="Imagen de ejemplo">
                    </div>
                    @else
                    @endif                    
                  <div class="col p-4 d-flex flex-column position-static">
                    <span>Por: <strong class="d-inline-block text-primary-emphasis">{{ $post->user->name }}</strong></span>
                    <h3 class="mb-0">{{ $post->title }}</h3>
                    <div class="mb-1 text-body-secondary">{{ $post->created_at->diffForHumans() }}</div>
                    <p class="card-text mb-auto">{!! substr($post->body,0,750) !!}...</p>
                    <a href="#" class="icon-link gap-1 icon-link-hover stretched-link">
                      Continue reading
                      <svg class="bi"><use xlink:href="#chevron-right"/></svg>
                    </a>
                  </div> @if ($loop->odd)
                  @else
                  <div class="col-4 d-none d-sm-block d-lg-block">
                      <img src="img/{{ $post->imagen }}" class="img-fluid" alt="Imagen de ejemplo">
                  </div>
                  @endif
                </div>
              </div>
            @endforeach
        </div>
    </div>
    <div class="col-sm-12">
        <div class="col-sm-12">
            <h2>Actividades Realizadas</h2>
        </div>
    </div>
    <!-- codigo aqui -->
    @foreach ($posts as $post)    
    <div class="row">
        <div class="col-md-12">
            <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
              
                @if ($loop->odd)
                <div class="col-4 d-none d-sm-block d-lg-block">
                    <img src="img/{{ $post->imagen }}" class="img-fluid" alt="Imagen de ejemplo">
                </div>
                @else
                @endif                
              <div class="col p-4 d-flex flex-column position-static">
                <span>Por: <strong class="d-inline-block text-primary-emphasis">{{ $post->user->name }}</strong></span>
                <h3 class="mb-0">{{ $post->title }}</h3>
                <div class="mb-1 text-body-secondary">{{ $post->created_at->diffForHumans() }}</div>
                <p class="card-text mb-auto">{!! substr($post->body,0,750) !!}...</p>
                <a href="#" class="icon-link gap-1 icon-link-hover stretched-link">
                  Continue reading
                  <svg class="bi"><use xlink:href="#chevron-right"/></svg>
                </a>
              </div> @if ($loop->odd)
              @else
              <div class="col-4 d-none d-sm-block d-lg-block">
                  <img src="img/{{ $post->imagen }}" class="img-fluid" alt="Imagen de ejemplo">
              </div>
              @endif
            </div>
          </div>
    </div>
@endforeach
</div>
@endsection
@section('footer')
<footer>
    <p>&copy; FUDOUNAR 2023 Creating by REDDATASRD</p>
</footer>
@endsection
@section('script')
<script src="{{ asset('assets/js/script.js') }}"></script>
<script>
    // Replace 'script nuestro' with the actual location you want to display on the map
  </script>
@endsection 