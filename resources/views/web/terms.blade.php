@extends('layouts.web')

@section('title', 'FUDOUNAR - Términos y Condiciones')
@section('meta_description', 'Consulta los Términos y Condiciones de uso del sitio web oficial de la Fundación Dominicana de Urología Dr. Nelson Adames.')
@section('canonical_url', route('terms'))

@section('content')
<div class="max-w-4xl mx-auto space-y-8 py-4">
    <div class="border-b border-gray-200 pb-4">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">Términos y Condiciones</h1>
        <p class="text-sm text-gray-500 mt-2">Última actualización: {{ date('d/m/Y') }}</p>
    </div>

    <div class="prose max-w-none text-gray-700 space-y-6 leading-relaxed">
        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">1. Aceptación de los Términos</h2>
            <p>
                Al acceder y utilizar el sitio web de la <strong>Fundación Dominicana de Urología Dr. Nelson Adames (FUDOUNAR)</strong>, usted acepta cumplir y estar sujeto a los presentes Términos y Condiciones de uso. Si no está de acuerdo con alguno de ellos, le solicitamos abstenerse de utilizar el sitio.
            </p>
        </section>

        <section class="bg-amber-50 p-5 rounded-2xl border border-amber-200">
            <h2 class="text-xl font-bold text-amber-900 mb-2">2. Descargo de Responsabilidad Médica</h2>
            <p class="text-amber-900 font-medium">
                La información contenida en los artículos, publicaciones y recursos de este sitio web tiene propósitos exclusivamente educativos e informativos sobre la salud urológica. En ningún caso debe interpretarse como diagnóstico, prescripción o recomendación médica directa ni reemplaza la consulta con un profesional de la salud calificado.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">3. Propiedad Intelectual</h2>
            <p>
                Todos los contenidos, marcas, logos, textos, imágenes, videos y diseños presentes en este sitio web son propiedad exclusiva de FUDOUNAR o de sus respectivos autores, encontrándose protegidos por las leyes de propiedad intelectual de la República Dominicana y tratados internacionales. Queda prohibida su reproducción no autorizada con fines comerciales.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">4. Inscripción a Cursos y Actividades</h2>
            <p>
                La participación en nuestros programas formativos y actividades comunitarias está sujeta a la disponibilidad de cupos y al cumplimiento de los requisitos establecidos en cada convocatoria. FUDOUNAR se reserva el derecho de reprogramar o cancelar cursos por razones de fuerza mayor, notificando oportunamente a los participantes registrados.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">5. Enlaces a Terceros</h2>
            <p>
                Nuestro sitio web puede contener enlaces hacia sitios de terceros o mostrar anuncios publicitarios servidos por redes autorizadas (como Google). FUDOUNAR no tiene control sobre el contenido, políticas de privacidad o prácticas de dichos sitios externos.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">6. Legislación Aplicable</h2>
            <p>
                Estos Términos y Condiciones se rigen e interpretan de acuerdo con las leyes vigentes de la República Dominicana. Para cualquier controversia, las partes se someten a la jurisdicción de los tribunales competentes de Santo Domingo, República Dominicana.
            </p>
        </section>
    </div>
</div>
@endsection
