<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:script';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta el script de actualización';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ejecuta el script y captura la salida
        $output = shell_exec('./update.sh');

        // Busca la línea donde se pide la confirmación
        $pattern = '/[Ss] para continuar/'; // Ajusta la expresión regular según la pregunta exacta
        preg_match($pattern, $output, $matches, PREG_OFFSET_CAPTURE);

        // Si se encontró la pregunta, simula la respuesta "s"
        if (!empty($matches)) {
            $this->info('Simulando respuesta "s"');
            $this->call('shell', ['command' => 'echo s']);
        }
    }
}
