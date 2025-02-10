<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!--a class="navbar-brand" href="#">Navbar</a-->
      <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link {{ Request::is('/') ? 'active' : '' }}">Inicio</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('quienes.somos') }}" class="nav-link {{ Request::is('quienes_somos') ? 'active' : '' }}">Quienes Somos</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('Actividad') }}" class="nav-link {{ Request::is('Actividad') ? 'active' : '' }}">Actividades</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('Actividad') }}" class="nav-link {{ Request::is('Actividad') ? 'active' : '' }}">Actividades</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('contact.create') }}" class="nav-link {{ Request::is('contact') ? 'active' : '' }}">Contacto</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>