<nav>
    <ul>
        <li><a href="{{ route('home') }}" class="{{ Request::is('/') ? 'active' : '' }}">Inicio</a></li>
        <li><a href="{{ route('quienes_somos') }}" class="{{ Request::is('quienes_somos') ? 'active' : '' }}">Quienes Somomos</a></li>
        <li><a href="{{ route('Actividad') }}" class="{{ Request::is('Actividad') ? 'active' : '' }}">Actividades</a></li>
        <li><a href="{{ route('contact.create') }}" class="{{ Request::is('contact') ? 'active' : '' }}">Contacto</a></li>
    </ul>
</nav>