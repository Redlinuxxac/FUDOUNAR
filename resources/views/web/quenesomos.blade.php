@extends('layouts.app')
@section('title')
    Inicio
@endsection
@section('menu')
    @include('menu', ['lugar' => 'Inicio'])
@endsection
@section('content')
<section id="quienes-somos"  class="row">
    <h2>Quiénes Somos</h2>
    <p>
        <h1>Nuestra misión es</h1>
        <p>Frase concisa y clara que exprese el propósito principal de la fundación. Ejemplo: "Nuestra misión es mejorar la calidad de vida de las comunidades marginadas a través de la educación, la salud y el desarrollo sostenible."</p>
        <h1>Nuestra visión es</h1>
        <p>Descripción inspiradora del futuro que la fundación desea crear. Ejemplo: "Vislumbramos un mundo donde todas las personas tengan acceso a oportunidades equitativas y puedan alcanzar su máximo potencial."</p>
        <h1>Nuestros valores son</h1>
        <p>Lista de los principios fundamentales que guían las acciones de la fundación. Ejemplo: "Integridad, solidaridad, excelencia, respeto, innovación."</p>
    </p>
    <img src="{{ asset('img/imagen_equipo.jpg') }}" alt="Nuestro equipo">
</section>
<section id="nuestra-historia">
    <h2>Nuestra Historia</h2>
    <p>Breve relato de cómo surgió la fundación, sus hitos más importantes y su evolución a lo largo del tiempo.
    Incluir anécdotas o testimonios que humanicen la historia y conecten con el lector.</p>
</section>
<section>
    <h1>Nuestro Equipo</h1>
    <p>Presentaciones breves de los miembros clave del equipo, destacando sus roles, experiencia y pasiones.
    Fotos de los miembros del equipo para crear un ambiente más cercano.</p>
</section>
<section id="areas-de-impacto">
    <h2>Áreas de Impacto</h2>
    <ul>
        <li>Descripción detallada de los programas y proyectos que la fundación lleva a cabo.</li>
        <li>Utilizar imágenes y gráficos para ilustrar los resultados obtenidos.</li>
        <li>Destacar los beneficios que los programas generan en las comunidades.</li>
    </ul>
</section>
<section id="logros">
    <h2>Nuestros Logros</h2>
    <ul>
        <li>Lista de los principales logros alcanzados por la fundación.</li>
        <li>Utilizar métricas y datos cuantitativos para respaldar los logros.</li>
        <li>Incluir testimonios de beneficiarios para mostrar el impacto real de la fundación.</li>
    </ul>
</section>
<section>
    <h2>¿Por qué Donar?</h2>
    <p>Explicar por qué es importante apoyar a la fundación.
    Destacar el impacto que cada donación puede generar.
    Facilitar el proceso de donación con botones o enlaces claros.</p>
</section>
@endsection
@section('script')
<script>
    // Replace 'YOUR_LOCATION_NAME' with the actual location you want to display on the map
    //document.getElementById("gmap_iframe").src = "https://www.google.com/maps/embed?pb=!1m18&q=" + encodeURIComponent('YOUR_LOCATION_NAME') + "&zoom=15&output=embed";
  </script>
@endsection 