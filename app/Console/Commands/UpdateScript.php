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
        // Aquí puedes agregar lógica adicional si lo deseas
        shell_exec('bash /update.sh');
    }
}
