@php
    use App\Models\Activity;
    use App\Models\Course;
    use App\Models\CourseRegistration;
    use App\Models\Post;
    use App\Models\SocialShare;
    use Illuminate\Support\Carbon;

    // =========================================================================
    // 0. PROCESAR FILTRO DE TIEMPO (Lapso de tiempo o intervalo personalizado)
    // =========================================================================
    $period = request()->query('period', 'all');
    $from = request()->query('from');
    $to = request()->query('to');

    $startDate = null;
    $endDate = null;
    $periodLabel = 'Todo el tiempo (Histórico)';
    $isFiltered = false;

    if ($period === '7_days') {
        $startDate = Carbon::now()->subDays(7)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $periodLabel = 'Últimos 7 días';
        $isFiltered = true;
    } elseif ($period === '30_days' || $period === '1_month') {
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $periodLabel = 'Último mes (30 días)';
        $isFiltered = true;
    } elseif ($period === '60_days' || $period === '2_months') {
        $startDate = Carbon::now()->subDays(60)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $periodLabel = 'Últimos 2 meses (60 días)';
        $isFiltered = true;
    } elseif ($period === '90_days' || $period === '3_months') {
        $startDate = Carbon::now()->subDays(90)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $periodLabel = 'Últimos 3 meses (90 días)';
        $isFiltered = true;
    } elseif ($period === '1_year') {
        $startDate = Carbon::now()->subYear()->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $periodLabel = 'Último año (365 días)';
        $isFiltered = true;
    } elseif ($from || $to || $period === 'custom') {
        $period = 'custom';
        try {
            $startDate = $from ? Carbon::parse($from)->startOfDay() : null;
            $endDate = $to ? Carbon::parse($to)->endOfDay() : null;
            $fromStr = $startDate ? $startDate->format('d/m/Y') : 'Inicio';
            $toStr = $endDate ? $endDate->format('d/m/Y') : 'Hoy';
            $periodLabel = "Intervalo: {$fromStr} - {$toStr}";
            $isFiltered = true;
        } catch (\Exception $e) {
            $startDate = null;
            $endDate = null;
            $period = 'all';
            $periodLabel = 'Todo el tiempo (Histórico)';
            $isFiltered = false;
        }
    }

    // =========================================================================
    // 1. REDES SOCIALES: MÉTRICAS FILTRADAS
    // =========================================================================
    $sharesQuery = SocialShare::query();
    if ($startDate && $endDate) {
        $sharesQuery->whereBetween('created_at', [$startDate, $endDate]);
    } elseif ($startDate) {
        $sharesQuery->where('created_at', '>=', $startDate);
    } elseif ($endDate) {
        $sharesQuery->where('created_at', '<=', $endDate);
    }

    $totalShares = (clone $sharesQuery)->count();
    $activityShares = (clone $sharesQuery)->where('shareable_type', Activity::class)->count();
    $blogShares = (clone $sharesQuery)->where('shareable_type', Post::class)->count();
    $courseShares = (clone $sharesQuery)->where('shareable_type', Course::class)->count();

    $sharesByPlatform = (clone $sharesQuery)->selectRaw('platform, count(*) as count')
        ->groupBy('platform')
        ->pluck('count', 'platform');

    $facebookShares = (int) ($sharesByPlatform['facebook'] ?? 0);
    $twitterShares = (int) ($sharesByPlatform['twitter'] ?? 0);
    $linkedinShares = (int) ($sharesByPlatform['linkedin'] ?? 0);
    $whatsappShares = (int) ($sharesByPlatform['whatsapp'] ?? 0);

    // =========================================================================
    // 2. TOP 5 ACTIVIDADES (VISTAS Y COMPARTIDOS EN EL PERÍODO)
    // =========================================================================
    $activitiesQuery = Activity::query();
    $activitiesQuery->withCount(['shares' => function ($q) use ($startDate, $endDate) {
        if ($startDate && $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $q->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $q->where('created_at', '<=', $endDate);
        }
    }]);

    $topActivities = (clone $activitiesQuery)
        ->orderByDesc('views')
        ->take(5)
        ->get();

    $maxActivityViews = max(1, (int) $topActivities->max('views'));
    $totalActivityViews = (int) $topActivities->sum('views');

    // =========================================================================
    // 3. TOP 10 BLOGS / NUESTRA VOZ (VISTAS Y COMPARTIDOS EN EL PERÍODO)
    // =========================================================================
    $blogsQuery = Post::query();
    $blogsQuery->withCount(['shares' => function ($q) use ($startDate, $endDate) {
        if ($startDate && $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $q->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $q->where('created_at', '<=', $endDate);
        }
    }]);

    $topBlogs = (clone $blogsQuery)
        ->orderByDesc('views')
        ->take(10)
        ->get();

    $totalBlogViews = (int) $topBlogs->sum('views');

    // =========================================================================
    // 4. TOP 10 CURSOS (SOLICITUDES RECIBIDAS EN EL PERÍODO)
    // =========================================================================
    $coursesQuery = Course::query();
    $coursesQuery->withCount([
        'registrations' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $q->where('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $q->where('created_at', '<=', $endDate);
            }
        },
        'shares' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $q->where('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $q->where('created_at', '<=', $endDate);
            }
        }
    ]);

    $topCourses = (clone $coursesQuery)
        ->orderByDesc('registrations_count')
        ->take(10)
        ->get();

    $totalCourseRequests = (int) $topCourses->sum('registrations_count');

    // =========================================================================
    // 5. DATOS COMPARATIVOS PARA GRÁFICOS DE BARRAS
    // =========================================================================
    $activitiesByViews = (clone $activitiesQuery)
        ->orderByDesc('views')
        ->take(6)
        ->get();

    $activitiesMostShared = (clone $activitiesQuery)
        ->orderByDesc('shares_count')
        ->take(6)
        ->get();

    $blogsByViews = (clone $blogsQuery)
        ->orderByDesc('views')
        ->take(6)
        ->get();

    $blogsMostShared = (clone $blogsQuery)
        ->orderByDesc('shares_count')
        ->take(6)
        ->get();

    $coursesByRequests = (clone $coursesQuery)
        ->orderByDesc('registrations_count')
        ->take(6)
        ->get();

    $coursesMostShared = (clone $coursesQuery)
        ->orderByDesc('shares_count')
        ->take(6)
        ->get();

    // Dataset estructurado con datos reales filtrados
    $realAnalytics = [
        'isFiltered' => $isFiltered,
        'period' => $period,
        'periodLabel' => $periodLabel,
        'totalShares' => $totalShares,
        'activityShares' => $activityShares,
        'blogShares' => $blogShares,
        'courseShares' => $courseShares,
        'platformShares' => [
            'facebook' => $facebookShares,
            'twitter' => $twitterShares,
            'linkedin' => $linkedinShares,
            'whatsapp' => $whatsappShares,
        ],
        'topActivities' => $topActivities->map(fn($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'views' => (int) $a->views,
            'shares' => (int) $a->shares_count,
        ])->values(),
        'topBlogs' => $topBlogs->map(fn($b) => [
            'id' => $b->id,
            'title' => $b->title,
            'views' => (int) $b->views,
            'shares' => (int) $b->shares_count,
        ])->values(),
        'topCourses' => $topCourses->map(fn($c) => [
            'id' => $c->id,
            'title' => $c->title,
            'views' => (int) $c->views,
            'requests' => (int) $c->registrations_count,
            'shares' => (int) $c->shares_count,
        ])->values(),
        'activitiesByViews' => $activitiesByViews->map(fn($a) => [
            'title' => $a->title,
            'views' => (int) $a->views,
        ])->values(),
        'activitiesMostShared' => $activitiesMostShared->map(fn($a) => [
            'title' => $a->title,
            'shares' => (int) $a->shares_count,
        ])->values(),
        'blogsByViews' => $blogsByViews->map(fn($b) => [
            'title' => $b->title,
            'views' => (int) $b->views,
        ])->values(),
        'blogsMostShared' => $blogsMostShared->map(fn($b) => [
            'title' => $b->title,
            'shares' => (int) $b->shares_count,
        ])->values(),
        'coursesByRequests' => $coursesByRequests->map(fn($c) => [
            'title' => $c->title,
            'requests' => (int) $c->registrations_count,
        ])->values(),
        'coursesMostShared' => $coursesMostShared->map(fn($c) => [
            'title' => $c->title,
            'shares' => (int) $c->shares_count,
        ])->values(),
    ];
@endphp

<x-layouts::app :title="__('Dashboard Analítico')">
    <div 
        x-data="analyticsDashboard({{ Js::from($realAnalytics) }})" 
        x-init="initDashboard()" 
        class="flex flex-col gap-6 w-full pb-12 font-sans antialiased text-zinc-900 dark:text-zinc-100"
    >
        <!-- Encabezado del Dashboard -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-zinc-200/80 dark:border-zinc-800 pb-5">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        Dashboard Analítico
                    </h1>

                    @if($isFiltered)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                            <span class="size-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Filtro Activo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Datos en Vivo
                        </span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Métricas reales del proyecto: interacciones, visitas y solicitudes sincronizadas con la base de datos.
                </p>
            </div>

            <!-- BARRA DE FILTRO POR LAPSO DE TIEMPO / INTERVALO PERSONALIZADO -->
            <form 
                method="GET" 
                action="{{ route('dashboard') }}" 
                x-data="{ 
                    selectedPeriod: '{{ $period }}',
                    fromDate: '{{ $from ?? '' }}',
                    toDate: '{{ $to ?? '' }}',
                    handlePeriodChange() {
                        const today = new Date();
                        const formatDate = (d) => d.toISOString().split('T')[0];

                        if (this.selectedPeriod === '7_days') {
                            const past = new Date();
                            past.setDate(today.getDate() - 7);
                            this.fromDate = formatDate(past);
                            this.toDate = formatDate(today);
                        } else if (this.selectedPeriod === '30_days') {
                            const past = new Date();
                            past.setDate(today.getDate() - 30);
                            this.fromDate = formatDate(past);
                            this.toDate = formatDate(today);
                        } else if (this.selectedPeriod === '60_days') {
                            const past = new Date();
                            past.setDate(today.getDate() - 60);
                            this.fromDate = formatDate(past);
                            this.toDate = formatDate(today);
                        } else if (this.selectedPeriod === '90_days') {
                            const past = new Date();
                            past.setDate(today.getDate() - 90);
                            this.fromDate = formatDate(past);
                            this.toDate = formatDate(today);
                        } else if (this.selectedPeriod === '1_year') {
                            const past = new Date();
                            past.setFullYear(today.getFullYear() - 1);
                            this.fromDate = formatDate(past);
                            this.toDate = formatDate(today);
                        } else if (this.selectedPeriod === 'all') {
                            this.fromDate = '';
                            this.toDate = '';
                        }
                    }
                }" 
                class="flex flex-wrap items-center gap-2.5 bg-zinc-50 dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800 p-2.5 rounded-xl shadow-xs"
            >
                <!-- Selector de Lapso de Tiempo Predefinido -->
                <div class="flex items-center gap-1.5">
                    <svg class="size-4 text-zinc-500 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <select 
                        name="period" 
                        x-model="selectedPeriod" 
                        @change="handlePeriodChange()"
                        class="text-xs font-medium rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1.5 px-2.5 focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:outline-hidden"
                    >
                        <option value="all">Todo el tiempo</option>
                        <option value="7_days">Últimos 7 días</option>
                        <option value="30_days">Último mes (30 días)</option>
                        <option value="60_days">Últimos 2 meses (60 días)</option>
                        <option value="90_days">Últimos 3 meses (90 días)</option>
                        <option value="1_year">Último año (365 días)</option>
                        <option value="custom">Personalizado (Intervalo)</option>
                    </select>
                </div>

                <!-- Intervalo de Fechas Personalizado (Desde / Hasta) -->
                <div 
                    x-show="selectedPeriod === 'custom' || fromDate !== '' || toDate !== ''" 
                    x-cloak
                    class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400"
                >
                    <div class="flex items-center gap-1">
                        <span class="text-[11px] font-medium">Desde:</span>
                        <input 
                            type="date" 
                            name="from" 
                            x-model="fromDate"
                            class="text-xs rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1 px-2 focus:ring-1 focus:ring-sky-500 focus:outline-hidden"
                        />
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-[11px] font-medium">Hasta:</span>
                        <input 
                            type="date" 
                            name="to" 
                            x-model="toDate"
                            class="text-xs rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 py-1 px-2 focus:ring-1 focus:ring-sky-500 focus:outline-hidden"
                        />
                    </div>
                </div>

                <!-- Botón Aplicar / Recargar -->
                <button 
                    type="submit" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white transition-colors shadow-xs cursor-pointer"
                    title="Aplicar filtro y recargar datos"
                >
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Filtrar</span>
                </button>

                <!-- Botón Limpiar o Quitar Filtro -->
                @if($isFiltered)
                    <a 
                        href="{{ route('dashboard') }}" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-colors shadow-xs"
                        title="Restablecer a todo el tiempo"
                    >
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Limpiar Filtro</span>
                    </a>
                @endif
            </form>
        </div>

        <!-- Banner Informativo del Período Filtrado -->
        @if($isFiltered)
            <div class="flex items-center justify-between px-4 py-2.5 rounded-xl bg-sky-500/10 border border-sky-500/20 text-xs text-sky-800 dark:text-sky-300">
                <div class="flex items-center gap-2">
                    <svg class="size-4 text-sky-600 dark:text-sky-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                        Métricas filtradas por período: <strong>{{ $periodLabel }}</strong>. Mostrando únicamente interacciones y solicitudes ocurridas dentro de este lapso.
                    </span>
                </div>
                <a href="{{ route('dashboard') }}" class="font-bold underline hover:no-underline ml-4 text-xs whitespace-nowrap text-sky-700 dark:text-sky-200">
                    Restablecer Todo
                </a>
            </div>
        @endif

        <!-- ========================================================================= -->
        <!-- 1. FILA SUPERIOR: 4 TARJETAS DE MÉTRICAS (GRID 1x4)                       -->
        <!-- ========================================================================= -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            
            <!-- PANEL 1: Redes Sociales: Total Compartidos -->
            <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            Redes Sociales
                        </span>
                        <div class="size-8 rounded-lg bg-sky-500/10 text-sky-500 dark:bg-sky-400/10 dark:text-sky-400 flex items-center justify-center">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white mt-1">
                        Total Compartidos
                    </h2>

                    <!-- Total General Destacado Real -->
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white" x-text="data.totalShares.toLocaleString()">
                            {{ number_format($totalShares) }}
                        </span>
                        <span class="inline-flex items-center text-xs font-semibold {{ $isFiltered ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            <svg class="size-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            {{ $isFiltered ? 'En período' : 'Histórico' }}
                        </span>
                    </div>

                    <!-- Desglose por tipo real -->
                    <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                            <span class="flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-sky-500"></span>
                                Actividades
                            </span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-200" x-text="data.activityShares.toLocaleString()">
                                {{ number_format($activityShares) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                            <span class="flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-violet-500"></span>
                                Blogs (Nuestra Voz)
                            </span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-200" x-text="data.blogShares.toLocaleString()">
                                {{ number_format($blogShares) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                            <span class="flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-emerald-500"></span>
                                Cursos
                            </span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-200" x-text="data.courseShares.toLocaleString()">
                                {{ number_format($courseShares) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Micro-badges / Iconos SVG de plataformas principales con conteo real -->
                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 grid grid-cols-4 gap-1.5 text-center">
                    <!-- Facebook -->
                    <div class="flex flex-col items-center p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <svg class="size-4 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span class="text-[11px] font-bold text-zinc-900 dark:text-zinc-100 mt-1" x-text="data.platformShares.facebook">
                            {{ $facebookShares }}
                        </span>
                        <span class="text-[9px] text-zinc-500 dark:text-zinc-400">FB</span>
                    </div>

                    <!-- X / Twitter -->
                    <div class="flex flex-col items-center p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <svg class="size-4 text-zinc-900 dark:text-zinc-100" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                        <span class="text-[11px] font-bold text-zinc-900 dark:text-zinc-100 mt-1" x-text="data.platformShares.twitter">
                            {{ $twitterShares }}
                        </span>
                        <span class="text-[9px] text-zinc-500 dark:text-zinc-400">X</span>
                    </div>

                    <!-- LinkedIn -->
                    <div class="flex flex-col items-center p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <svg class="size-4 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                        <span class="text-[11px] font-bold text-zinc-900 dark:text-zinc-100 mt-1" x-text="data.platformShares.linkedin">
                            {{ $linkedinShares }}
                        </span>
                        <span class="text-[9px] text-zinc-500 dark:text-zinc-400">In</span>
                    </div>

                    <!-- WhatsApp -->
                    <div class="flex flex-col items-center p-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <svg class="size-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 0C5.396 0 .029 5.367.029 12.002c0 2.122.554 4.195 1.607 6.018L0 24l6.167-1.618a11.97 11.97 0 005.864 1.523h.005c6.635 0 12.002-5.367 12.002-12.002C24.038 5.367 18.667 0 12.031 0zm0 21.908a9.92 9.92 0 01-5.064-1.385l-.364-.216-3.763.987 1.004-3.667-.238-.378a9.907 9.907 0 01-1.517-5.247c0-5.474 4.453-9.927 9.932-9.927 2.652 0 5.145 1.033 7.02 2.908a9.88 9.88 0 012.908 7.019c0 5.475-4.453 9.906-9.918 9.906z"/>
                        </svg>
                        <span class="text-[11px] font-bold text-zinc-900 dark:text-zinc-100 mt-1" x-text="data.platformShares.whatsapp">
                            {{ $whatsappShares }}
                        </span>
                        <span class="text-[9px] text-zinc-500 dark:text-zinc-400">WA</span>
                    </div>
                </div>
            </div>

            <!-- PANEL 2: Top 5 Actividades (Vistas) -->
            <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            Actividades
                        </span>
                        <div class="size-8 rounded-lg bg-indigo-500/10 text-indigo-500 dark:bg-indigo-400/10 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white mt-1">
                        Top 5 Actividades (Vistas)
                    </h2>

                    <!-- Lista numerada 1 a 5 con datos reales y barras de progreso -->
                    <div class="mt-4 space-y-3">
                        <template x-if="data.topActivities.length === 0">
                            <p class="text-xs text-zinc-400 py-4 text-center">No hay actividades registradas en este período.</p>
                        </template>

                        <template x-for="(activity, index) in data.topActivities" :key="activity.id || index">
                            <div class="group">
                                <div class="flex items-center justify-between text-xs gap-2">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <span class="inline-flex items-center justify-center size-4 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[10px] font-bold text-zinc-600 dark:text-zinc-400" x-text="index + 1"></span>
                                        <span class="truncate font-medium text-zinc-800 dark:text-zinc-200" x-text="activity.title"></span>
                                    </div>
                                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 whitespace-nowrap" x-text="activity.views.toLocaleString() + ' vistas'"></span>
                                </div>
                                <!-- Barra de progreso horizontal en Tailwind -->
                                <div class="w-full h-1.5 rounded-full bg-indigo-500/20 mt-1.5 overflow-hidden">
                                    <div 
                                        class="h-1.5 rounded-full bg-indigo-500 transition-all duration-500" 
                                        :style="`width: ${data.topActivities[0]?.views > 0 ? (activity.views / data.topActivities[0].views) * 100 : 0}%`"
                                    ></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400">
                    <span>Total acumulado top 5:</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-200">
                        {{ number_format($totalActivityViews) }} vistas
                    </span>
                </div>
            </div>

            <!-- PANEL 3: Top 10 Blogs (Vistas) -->
            <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            Nuestra Voz / Blogs
                        </span>
                        <div class="size-8 rounded-lg bg-violet-500/10 text-violet-500 dark:bg-violet-400/10 dark:text-violet-400 flex items-center justify-center">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white mt-1">
                        Top 10 Blogs (Vistas)
                    </h2>

                    <!-- Lista compacta numerada 1 a 10 con icono SVG de documento y datos reales -->
                    <div class="mt-3 max-h-64 overflow-y-auto space-y-2 pr-1 divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <template x-if="data.topBlogs.length === 0">
                            <p class="text-xs text-zinc-400 py-4 text-center">No hay artículos publicados en este período.</p>
                        </template>

                        <template x-for="(blog, index) in data.topBlogs" :key="blog.id || index">
                            <div class="pt-2 first:pt-0 flex items-center justify-between text-xs gap-2 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 p-1 rounded transition-colors">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="font-semibold text-zinc-400 dark:text-zinc-500 w-4 text-right" x-text="index + 1 + '.'"></span>
                                    <svg class="size-3.5 text-violet-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="truncate text-zinc-800 dark:text-zinc-200 font-medium" x-text="blog.title"></span>
                                </div>
                                <span class="font-bold text-zinc-900 dark:text-zinc-100 whitespace-nowrap text-right pl-2" x-text="blog.views.toLocaleString()"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400">
                    <span>Total vistas top 10:</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-200">
                        {{ number_format($totalBlogViews) }} vistas
                    </span>
                </div>
            </div>

            <!-- PANEL 4: Top 10 Cursos (Solicitudes) -->
            <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            Cursos y Talleres
                        </span>
                        <div class="size-8 rounded-lg bg-emerald-500/10 text-emerald-500 dark:bg-emerald-400/10 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white mt-1">
                        Top 10 Cursos (Solicitudes)
                    </h2>

                    <!-- Lista numerada 1 a 10 con icono SVG de birrete y solicitudes reales -->
                    <div class="mt-3 max-h-64 overflow-y-auto space-y-2 pr-1 divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <template x-if="data.topCourses.length === 0">
                            <p class="text-xs text-zinc-400 py-4 text-center">No hay solicitudes en este período.</p>
                        </template>

                        <template x-for="(course, index) in data.topCourses" :key="course.id || index">
                            <div class="pt-2 first:pt-0 flex items-center justify-between text-xs gap-2 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 p-1 rounded transition-colors">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="font-semibold text-zinc-400 dark:text-zinc-500 w-4 text-right" x-text="index + 1 + '.'"></span>
                                    <svg class="size-3.5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span class="truncate text-zinc-800 dark:text-zinc-200 font-medium" x-text="course.title"></span>
                                </div>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap text-right pl-2" x-text="course.requests + ' sol.'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400">
                    <span>Total solicitudes registradas:</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-200">
                        {{ number_format($totalCourseRequests) }} solicitudes
                    </span>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- 2. ÁREA INFERIOR: PANEL ANALÍTICO DE GRÁFICOS                            -->
        <!-- ========================================================================= -->

        <div class="mt-4 flex flex-col gap-6">

            <!-- SECCIÓN A: FILA DE GRÁFICOS CIRCULARES / DONAS (Grid 1x4) -->
            <div>
                <div class="mb-3">
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <span class="size-2 rounded-full bg-sky-500"></span>
                        Distribución y Proporciones
                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Datos agregados directamente de la base de datos de actividades, blogs y cursos
                        <span class="font-semibold text-zinc-700 dark:text-zinc-300">({{ $periodLabel }})</span>.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    
                    <!-- Gráfico 1: Desglose por Plataforma (Total Redes) -->
                    <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                Desglose por Plataforma
                            </h3>
                            <span class="text-[11px] font-medium text-zinc-400">Redes Sociales</span>
                        </div>
                        <div wire:ignore class="relative h-56 w-full flex items-center justify-center">
                            <canvas id="chartPlatformDonut"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico 2: Proporción de Vistas del Top 5 Actividades -->
                    <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                Top 5 Actividades
                            </h3>
                            <span class="text-[11px] font-medium text-zinc-400">Proporción Vistas</span>
                        </div>
                        <div wire:ignore class="relative h-56 w-full flex items-center justify-center">
                            <canvas id="chartActivitiesDonut"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico 3: Distribución de Vistas Top 10 Blogs (Top vs Otros) -->
                    <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                Top 10 Blogs
                            </h3>
                            <span class="text-[11px] font-medium text-zinc-400">Top vs Otros</span>
                        </div>
                        <div wire:ignore class="relative h-56 w-full flex items-center justify-center">
                            <canvas id="chartBlogsDonut"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico 4: Distribución de Solicitudes Top 10 Cursos (Top vs Otros) -->
                    <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                Solicitudes Cursos
                            </h3>
                            <span class="text-[11px] font-medium text-zinc-400">Top vs Otros</span>
                        </div>
                        <div wire:ignore class="relative h-56 w-full flex items-center justify-center">
                            <canvas id="chartCoursesDonut"></canvas>
                        </div>
                    </div>

                </div>
            </div>

            <!-- SECCIÓN B: CUADRÍCULA DE GRÁFICOS DE BARRAS (Grid 2 columnas) -->
            <div>
                <div class="mb-3">
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <span class="size-2 rounded-full bg-emerald-500"></span>
                        Análisis Comparativo de Rendimiento Real
                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Comparativa por títulos de actividades, artículos y cursos registrados en el sistema
                        <span class="font-semibold text-zinc-700 dark:text-zinc-300">({{ $periodLabel }})</span>.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                    <!-- Gráfico A (Vertical): Total Vistas de Actividades -->
                    <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    Total Vistas de Actividades
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Actividades con mayor alcance en la plataforma</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/10 text-sky-600 dark:text-sky-400">Vertical</span>
                        </div>
                        <div wire:ignore class="relative h-64 w-full">
                            <canvas id="chartActivitiesBarVertical"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico B (Horizontal): Actividades Más Compartidas -->
                    <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    Actividades Más Compartidas
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Total de compartidos registrados en redes sociales</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Horizontal</span>
                        </div>
                        <div wire:ignore class="relative h-64 w-full">
                            <canvas id="chartActivitiesBarHorizontal"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico C (Vertical): Total Vistas de Blogs -->
                    <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    Total Vistas de Blogs
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Lecturas reales en la sección Nuestra Voz</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-violet-500/10 text-violet-600 dark:text-violet-400">Vertical</span>
                        </div>
                        <div wire:ignore class="relative h-64 w-full">
                            <canvas id="chartBlogsBarVertical"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico D (Horizontal): Blogs Más Compartidos -->
                    <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    Blogs Más Compartidos
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Artículos difundidos en WhatsApp, FB, X y LinkedIn</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-500/10 text-orange-600 dark:text-orange-400">Horizontal</span>
                        </div>
                        <div wire:ignore class="relative h-64 w-full">
                            <canvas id="chartBlogsBarHorizontal"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico E (Vertical): Total Solicitudes de Cursos -->
                    <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    Total Solicitudes de Cursos
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Inscripciones recibidas en el período seleccionado</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400">Vertical</span>
                        </div>
                        <div wire:ignore class="relative h-64 w-full">
                            <canvas id="chartCoursesBarVertical"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico F (Horizontal): Cursos Más Compartidos -->
                    <div class="rounded-xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    Cursos Más Compartidos
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Cursos formativos con mayor recomendación social</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400">Horizontal</span>
                        </div>
                        <div wire:ignore class="relative h-64 w-full">
                            <canvas id="chartCoursesBarHorizontal"></canvas>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SCRIPTS: Alpine.js + Chart.js CDN con Paleta Neón / Modo Oscuro          -->
    <!-- ========================================================================= -->
    <script>
        function analyticsDashboard(initialData) {
            return {
                charts: {},
                data: initialData || {
                    isFiltered: false,
                    period: 'all',
                    periodLabel: 'Todo el tiempo',
                    totalShares: 0,
                    activityShares: 0,
                    blogShares: 0,
                    courseShares: 0,
                    platformShares: { facebook: 0, twitter: 0, linkedin: 0, whatsapp: 0 },
                    topActivities: [],
                    topBlogs: [],
                    topCourses: [],
                    activitiesByViews: [],
                    activitiesMostShared: [],
                    blogsByViews: [],
                    blogsMostShared: [],
                    coursesByRequests: [],
                    coursesMostShared: []
                },

                initDashboard() {
                    this.loadChartJsLibrary(() => {
                        this.renderAllCharts();
                    });

                    // Re-renderizar automáticamente ante cambios de modo oscuro
                    const observer = new MutationObserver(() => {
                        this.destroyCharts();
                        this.renderAllCharts();
                    });
                    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                },

                loadChartJsLibrary(callback) {
                    if (window.Chart) {
                        callback();
                        return;
                    }
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js';
                    script.onload = () => callback();
                    document.head.appendChild(script);
                },

                isDarkMode() {
                    return document.documentElement.classList.contains('dark');
                },

                getThemeColors() {
                    const isDark = this.isDarkMode();
                    return {
                        textColor: isDark ? '#d4d4d8' : '#3f3f46',
                        mutedTextColor: isDark ? '#71717a' : '#a1a1aa',
                        gridColor: isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.05)',
                        tooltipBg: isDark ? '#18181b' : '#ffffff',
                        tooltipText: isDark ? '#f4f4f5' : '#09090b',
                        tooltipBorder: isDark ? '#27272a' : '#e4e4e7',
                        // Paleta Neón / Vibrante
                        neonBlue: '#00D2FF',
                        neonEmerald: '#10B981',
                        neonOrange: '#F97316',
                        neonViolet: '#A855F7',
                        neonPink: '#EC4899',
                        neonCyan: '#06B6D4',
                        mutedOther: isDark ? '#3f3f46' : '#cbd5e1'
                    };
                },

                destroyCharts() {
                    Object.values(this.charts).forEach(chart => {
                        if (chart && typeof chart.destroy === 'function') {
                            chart.destroy();
                        }
                    });
                    this.charts = {};
                },

                formatLabel(text, max = 16) {
                    if (!text) return '';
                    return text.length > max ? text.slice(0, max - 2) + '...' : text;
                },

                renderAllCharts() {
                    const t = this.getThemeColors();

                    const commonPlugins = {
                        legend: {
                            labels: {
                                color: t.textColor,
                                font: { family: 'inherit', size: 11, weight: '500' },
                                boxWidth: 12,
                                padding: 12
                            }
                        },
                        tooltip: {
                            backgroundColor: t.tooltipBg,
                            titleColor: t.tooltipText,
                            bodyColor: t.tooltipText,
                            borderColor: t.tooltipBorder,
                            borderWidth: 1,
                            padding: 10,
                            boxPadding: 4,
                            cornerRadius: 8
                        }
                    };

                    // ==========================================
                    // 1. Gráfico 1: Desglose por Plataforma (Dona)
                    // ==========================================
                    const ctxPlatform = document.getElementById('chartPlatformDonut');
                    if (ctxPlatform) {
                        const pf = this.data.platformShares || {};
                        const platformData = [pf.facebook || 0, pf.twitter || 0, pf.linkedin || 0, pf.whatsapp || 0];
                        const hasData = platformData.some(v => v > 0);

                        this.charts.platform = new Chart(ctxPlatform, {
                            type: 'doughnut',
                            data: {
                                labels: hasData ? ['Facebook', 'X / Twitter', 'LinkedIn', 'WhatsApp'] : ['Sin compartidos'],
                                datasets: [{
                                    data: hasData ? platformData : [1],
                                    backgroundColor: hasData 
                                        ? [t.neonBlue, t.neonViolet, t.neonEmerald, t.neonOrange]
                                        : [t.mutedOther],
                                    borderWidth: 2,
                                    borderColor: this.isDarkMode() ? '#18181b' : '#ffffff',
                                    hoverOffset: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: {
                                    ...commonPlugins,
                                    legend: { position: 'bottom', labels: { color: t.textColor, boxWidth: 10, font: { size: 10 } } }
                                }
                            }
                        });
                    }

                    // ==========================================
                    // 2. Gráfico 2: Proporción Vistas Top 5 Actividades (Dona)
                    // ==========================================
                    const ctxActDonut = document.getElementById('chartActivitiesDonut');
                    if (ctxActDonut) {
                        const acts = this.data.topActivities || [];
                        const actLabels = acts.map(a => this.formatLabel(a.title, 14));
                        const actViews = acts.map(a => a.views || 0);
                        const hasData = actViews.length && actViews.some(v => v > 0);

                        this.charts.activitiesDonut = new Chart(ctxActDonut, {
                            type: 'doughnut',
                            data: {
                                labels: hasData ? actLabels : ['Sin vistas'],
                                datasets: [{
                                    data: hasData ? actViews : [1],
                                    backgroundColor: hasData 
                                        ? [t.neonBlue, t.neonEmerald, t.neonOrange, t.neonViolet, t.neonPink]
                                        : [t.mutedOther],
                                    borderWidth: 2,
                                    borderColor: this.isDarkMode() ? '#18181b' : '#ffffff',
                                    hoverOffset: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: {
                                    ...commonPlugins,
                                    legend: { position: 'bottom', labels: { color: t.textColor, boxWidth: 10, font: { size: 10 } } }
                                }
                            }
                        });
                    }

                    // ==========================================
                    // 3. Gráfico 3: Distribución Vistas Top 10 Blogs (Top vs Otros) (Dona)
                    // ==========================================
                    const ctxBlogsDonut = document.getElementById('chartBlogsDonut');
                    if (ctxBlogsDonut) {
                        const blogs = this.data.topBlogs || [];
                        const top3 = blogs.slice(0, 3);
                        const otherBlogsViews = blogs.slice(3).reduce((acc, b) => acc + (b.views || 0), 0);

                        const blogLabels = top3.map(b => this.formatLabel(b.title, 12));
                        if (blogs.length > 3) {
                            blogLabels.push('Otros Blogs');
                        }
                        const blogData = top3.map(b => b.views || 0);
                        if (blogs.length > 3) {
                            blogData.push(otherBlogsViews);
                        }
                        const hasData = blogData.length && blogData.some(v => v > 0);

                        this.charts.blogsDonut = new Chart(ctxBlogsDonut, {
                            type: 'doughnut',
                            data: {
                                labels: hasData ? blogLabels : ['Sin vistas'],
                                datasets: [{
                                    data: hasData ? blogData : [1],
                                    backgroundColor: hasData 
                                        ? [t.neonViolet, t.neonBlue, t.neonEmerald, t.mutedOther]
                                        : [t.mutedOther],
                                    borderWidth: 2,
                                    borderColor: this.isDarkMode() ? '#18181b' : '#ffffff',
                                    hoverOffset: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: {
                                    ...commonPlugins,
                                    legend: { position: 'bottom', labels: { color: t.textColor, boxWidth: 10, font: { size: 10 } } }
                                }
                            }
                        });
                    }

                    // ==========================================
                    // 4. Gráfico 4: Distribución Solicitudes Top 10 Cursos (Top vs Otros) (Dona)
                    // ==========================================
                    const ctxCoursesDonut = document.getElementById('chartCoursesDonut');
                    if (ctxCoursesDonut) {
                        const courses = this.data.topCourses || [];
                        const top3Courses = courses.slice(0, 3);
                        const otherCourseReqs = courses.slice(3).reduce((acc, c) => acc + (c.requests || 0), 0);

                        const courseLabels = top3Courses.map(c => this.formatLabel(c.title, 12));
                        if (courses.length > 3) {
                            courseLabels.push('Otros Cursos');
                        }
                        const courseData = top3Courses.map(c => c.requests || 0);
                        if (courses.length > 3) {
                            courseData.push(otherCourseReqs);
                        }
                        const hasData = courseData.length && courseData.some(v => v > 0);

                        this.charts.coursesDonut = new Chart(ctxCoursesDonut, {
                            type: 'doughnut',
                            data: {
                                labels: hasData ? courseLabels : ['Sin solicitudes'],
                                datasets: [{
                                    data: hasData ? courseData : [1],
                                    backgroundColor: hasData 
                                        ? [t.neonEmerald, t.neonOrange, t.neonBlue, t.mutedOther]
                                        : [t.mutedOther],
                                    borderWidth: 2,
                                    borderColor: this.isDarkMode() ? '#18181b' : '#ffffff',
                                    hoverOffset: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: {
                                    ...commonPlugins,
                                    legend: { position: 'bottom', labels: { color: t.textColor, boxWidth: 10, font: { size: 10 } } }
                                }
                            }
                        });
                    }

                    // ==========================================
                    // 5. Gráfico A (Vertical): Total Vistas de Actividades
                    // ==========================================
                    const ctxActBarVert = document.getElementById('chartActivitiesBarVertical');
                    if (ctxActBarVert) {
                        const actsByViews = this.data.activitiesByViews || [];
                        this.charts.actBarVert = new Chart(ctxActBarVert, {
                            type: 'bar',
                            data: {
                                labels: actsByViews.length ? actsByViews.map(a => this.formatLabel(a.title, 14)) : ['Sin datos'],
                                datasets: [{
                                    label: 'Vistas',
                                    data: actsByViews.length ? actsByViews.map(a => a.views || 0) : [0],
                                    backgroundColor: t.neonBlue,
                                    borderRadius: 6,
                                    borderSkipped: false
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { ...commonPlugins, legend: { display: false } },
                                scales: {
                                    x: { grid: { display: false }, ticks: { color: t.textColor, font: { size: 10 } } },
                                    y: { grid: { color: t.gridColor }, ticks: { color: t.mutedTextColor, font: { size: 10 } } }
                                }
                            }
                        });
                    }

                    // ==========================================
                    // 6. Gráfico B (Horizontal): Actividades Más Compartidas
                    // ==========================================
                    const ctxActBarHoriz = document.getElementById('chartActivitiesBarHorizontal');
                    if (ctxActBarHoriz) {
                        const actsShared = this.data.activitiesMostShared || [];
                        this.charts.actBarHoriz = new Chart(ctxActBarHoriz, {
                            type: 'bar',
                            data: {
                                labels: actsShared.length ? actsShared.map(a => this.formatLabel(a.title, 18)) : ['Sin datos'],
                                datasets: [{
                                    label: 'Compartidos',
                                    data: actsShared.length ? actsShared.map(a => a.shares || 0) : [0],
                                    backgroundColor: t.neonEmerald,
                                    borderRadius: 6,
                                    borderSkipped: false
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { ...commonPlugins, legend: { display: false } },
                                scales: {
                                    x: { grid: { color: t.gridColor }, ticks: { color: t.mutedTextColor, font: { size: 10 } } },
                                    y: { grid: { display: false }, ticks: { color: t.textColor, font: { size: 10 } } }
                                }
                            }
                        });
                    }

                    // ==========================================
                    // 7. Gráfico C (Vertical): Total Vistas de Blogs
                    // ==========================================
                    const ctxBlogsBarVert = document.getElementById('chartBlogsBarVertical');
                    if (ctxBlogsBarVert) {
                        const blogsByViews = this.data.blogsByViews || [];
                        this.charts.blogsBarVert = new Chart(ctxBlogsBarVert, {
                            type: 'bar',
                            data: {
                                labels: blogsByViews.length ? blogsByViews.map(b => this.formatLabel(b.title, 14)) : ['Sin datos'],
                                datasets: [{
                                    label: 'Vistas',
                                    data: blogsByViews.length ? blogsByViews.map(b => b.views || 0) : [0],
                                    backgroundColor: t.neonViolet,
                                    borderRadius: 6,
                                    borderSkipped: false
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { ...commonPlugins, legend: { display: false } },
                                scales: {
                                    x: { grid: { display: false }, ticks: { color: t.textColor, font: { size: 10 } } },
                                    y: { grid: { color: t.gridColor }, ticks: { color: t.mutedTextColor, font: { size: 10 } } }
                                }
                            }
                        });
                    }

                    // ==========================================
                    // 8. Gráfico D (Horizontal): Blogs Más Compartidos
                    // ==========================================
                    const ctxBlogsBarHoriz = document.getElementById('chartBlogsBarHorizontal');
                    if (ctxBlogsBarHoriz) {
                        const blogsShared = this.data.blogsMostShared || [];
                        this.charts.blogsBarHoriz = new Chart(ctxBlogsBarHoriz, {
                            type: 'bar',
                            data: {
                                labels: blogsShared.length ? blogsShared.map(b => this.formatLabel(b.title, 18)) : ['Sin datos'],
                                datasets: [{
                                    label: 'Compartidos',
                                    data: blogsShared.length ? blogsShared.map(b => b.shares || 0) : [0],
                                    backgroundColor: t.neonOrange,
                                    borderRadius: 6,
                                    borderSkipped: false
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { ...commonPlugins, legend: { display: false } },
                                scales: {
                                    x: { grid: { color: t.gridColor }, ticks: { color: t.mutedTextColor, font: { size: 10 } } },
                                    y: { grid: { display: false }, ticks: { color: t.textColor, font: { size: 10 } } }
                                }
                            }
                        });
                    }

                    // ==========================================
                    // 9. Gráfico E (Vertical): Total Solicitudes de Cursos
                    // ==========================================
                    const ctxCoursesBarVert = document.getElementById('chartCoursesBarVertical');
                    if (ctxCoursesBarVert) {
                        const coursesReq = this.data.coursesByRequests || [];
                        this.charts.coursesBarVert = new Chart(ctxCoursesBarVert, {
                            type: 'bar',
                            data: {
                                labels: coursesReq.length ? coursesReq.map(c => this.formatLabel(c.title, 14)) : ['Sin datos'],
                                datasets: [{
                                    label: 'Solicitudes',
                                    data: coursesReq.length ? coursesReq.map(c => c.requests || 0) : [0],
                                    backgroundColor: t.neonCyan,
                                    borderRadius: 6,
                                    borderSkipped: false
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { ...commonPlugins, legend: { display: false } },
                                scales: {
                                    x: { grid: { display: false }, ticks: { color: t.textColor, font: { size: 10 } } },
                                    y: { grid: { color: t.gridColor }, ticks: { color: t.mutedTextColor, font: { size: 10 } } }
                                }
                            }
                        });
                    }

                    // ==========================================
                    // 10. Gráfico F (Horizontal): Cursos Más Compartidos
                    // ==========================================
                    const ctxCoursesBarHoriz = document.getElementById('chartCoursesBarHorizontal');
                    if (ctxCoursesBarHoriz) {
                        const coursesShared = this.data.coursesMostShared || [];
                        this.charts.coursesBarHoriz = new Chart(ctxCoursesBarHoriz, {
                            type: 'bar',
                            data: {
                                labels: coursesShared.length ? coursesShared.map(c => this.formatLabel(c.title, 18)) : ['Sin datos'],
                                datasets: [{
                                    label: 'Compartidos',
                                    data: coursesShared.length ? coursesShared.map(c => c.shares || 0) : [0],
                                    backgroundColor: t.neonPink,
                                    borderRadius: 6,
                                    borderSkipped: false
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { ...commonPlugins, legend: { display: false } },
                                scales: {
                                    x: { grid: { color: t.gridColor }, ticks: { color: t.mutedTextColor, font: { size: 10 } } },
                                    y: { grid: { display: false }, ticks: { color: t.textColor, font: { size: 10 } } }
                                }
                            }
                        });
                    }
                }
            };
        }
    </script>
</x-layouts::app>
