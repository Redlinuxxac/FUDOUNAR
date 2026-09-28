@extends('layouts.web')

@section('title', 'FUDOUNAR - Política de Privacidad')
@section('meta_description', 'Conoce nuestra Política de Privacidad, el uso de cookies de Google y el tratamiento de datos personales en la Fundación Dominicana de Urología Dr. Nelson Adames.')
@section('canonical_url', route('privacy'))

@section('content')
<div class="max-w-4xl mx-auto space-y-8 py-4">
    <div class="border-b border-gray-200 pb-4">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">Política de Privacidad</h1>
        <p class="text-sm text-gray-500 mt-2">Última actualización: {{ date('d/m/Y') }}</p>
    </div>

    <div class="prose max-w-none text-gray-700 space-y-6 leading-relaxed">
        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">1. Información General y Responsable</h2>
            <p>
                La <strong>Fundación Dominicana de Urología Dr. Nelson Adames (FUDOUNAR)</strong>, con domicilio en la República Dominicana, está comprometida con la protección de la privacidad y los datos personales de los usuarios que visitan nuestro sitio web oficial.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">2. Datos Personales que Recopilamos</h2>
            <p>
                Recopilamos información personal únicamente cuando es suministrada de manera voluntaria por el usuario a través de nuestros canales:
            </p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
                <li><strong>Inscripción a Cursos y Talleres:</strong> Nombre completo, correo electrónico, número de teléfono y datos académicos o profesionales necesarios para la gestión del curso.</li>
                <li><strong>Formularios de Contacto:</strong> Nombre, dirección de correo electrónico y contenido de la consulta enviada.</li>
                <li><strong>Datos de Navegación:</strong> Dirección IP anónima, tipo de navegador, sistema operativo y páginas visitadas a través de herramientas analíticas.</li>
            </ul>
        </section>

        <section class="bg-blue-50/60 p-5 rounded-2xl border border-blue-100">
            <h2 class="text-xl font-bold text-blue-900 mb-2">3. Uso de Cookies y Publicidad de Google (Google AdSense)</h2>
            <p class="text-gray-800">
                Este sitio web puede utilizar <strong>Google AdSense</strong> y otros servicios publicitarios de terceros para mostrar anuncios relevantes a los visitantes. De conformidad con las políticas de Google:
            </p>
            <ul class="list-disc pl-6 space-y-2 mt-3 text-gray-800">
                <li>
                    Proveedores terceros, incluido <strong>Google</strong>, utilizan cookies para publicar anuncios basados en las visitas previas de un usuario a este sitio web o a otros sitios en Internet.
                </li>
                <li>
                    El uso de cookies de publicidad permite a Google y a sus socios mostrar anuncios basados en las visitas que los usuarios realizan a sus sitios o a otros sitios web.
                </li>
                <li>
                    Los usuarios pueden inhabilitar la publicidad personalizada consultando la 
                    <a href="https://adssettings.google.com/" target="_blank" rel="noopener noreferrer" class="text-blue-700 underline font-semibold">Configuración de Anuncios de Google</a>.
                </li>
                <li>
                    Alternativamente, los usuarios pueden inhabilitar el uso de cookies de proveedores externos para la publicidad personalizada visitando 
                    <a href="https://www.aboutads.info/" target="_blank" rel="noopener noreferrer" class="text-blue-700 underline font-semibold">www.aboutads.info</a>.
                </li>
            </ul>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">4. Analítica Web (Google Analytics)</h2>
            <p>
                Utilizamos <strong>Google Analytics</strong> para recopilar información estadística no identificable de manera individual sobre cómo los usuarios interactúan con nuestra página web. Esto nos ayuda a mejorar la experiencia del usuario y optimizar nuestros programas educativos y comunitarios.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">5. Finalidad del Tratamiento de Datos</h2>
            <p>Los datos suministrados se utilizan exclusivamente para:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
                <li>Gestionar la inscripción, confirmación y emisión de certificados de cursos.</li>
                <li>Responder a consultas recibidas a través de la sección de contacto.</li>
                <li>Difundir información sobre actividades benéficas, médicas y comunitarias de FUDOUNAR.</li>
            </ul>
            <p class="mt-2 font-medium">FUDOUNAR no vende, alquila ni comparte información personal con terceros con fines comerciales ajenos a nuestra misión.</p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">6. Derechos del Usuario</h2>
            <p>
                Tienes derecho a acceder, rectificar, cancelar u oponerte al tratamiento de tus datos personales en cualquier momento. Para ejercer estos derechos, puedes escribirnos mediante nuestro formulario en la sección de <a href="{{ route('contact') }}" class="text-blue-600 underline">Contacto</a>.
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-2">7. Modificaciones a esta Política</h2>
            <p>
                Nos reservamos el derecho de actualizar esta Política de Privacidad para reflejar cambios legales o ajustes en nuestros servicios. Cualquier cambio será publicado de inmediato en esta página.
            </p>
        </section>
    </div>
</div>
@endsection
