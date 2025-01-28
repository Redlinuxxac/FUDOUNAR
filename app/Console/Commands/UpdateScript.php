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
        // Obtener la última versión remota
        $verRemota = substr(shell_exec("git ls-remote --tags origin | grep -oP 'refs/tags/\K(.*)' | sort -V | tail -n 1"),0, -4);
        echo "versiòn remota: ";
        echo $verRemota;

        // Nombre del archivo
        $archivo = "version.red";

        // Nueva versión a escribir (ajusta según sea necesario)
        $nueva_version = $verRemota;

        // Verificar si el archivo existe y es escribible
        if (is_writable($archivo)) {
            // Si el archivo existe, lo leemos y agregamos la nueva versión
            if (file_exists($archivo)) {
                $contenido_actual = file_get_contents($archivo);
                echo " versiòn local:";echo $contenido_actual;
                if ($nueva_version>$contenido_actual){$nuevo_contenido = $nueva_version;$grabar=true;}else{$grabar=false;}
                //$nuevo_contenido = $contenido_actual . "\n" . $nueva_version;
            } else { 
                // Si el archivo no existe, creamos uno nuevo
                file_put_contents($archivo, "");
                $nuevo_contenido = $nueva_version;
            }

            // Escribimos el nuevo contenido en el archivo
            if($grabar){
                         //coma de gis para actualiza repositoeio.
                         $correr1=shell_exec("git fetch origin");
                         $correr2=shell_exec("git merge origin/master");
                         echo "\n ejecuta actualiacion: \n $correr1 $correr2 \n";
                         file_put_contents("bitagora.red", "date() \n $correr2");
                         // Actualiar el archivo a la nueva version
                         file_put_contents($archivo, $nuevo_contenido);
                         echo "\n El archivo $archivo ha sido actualizado con éxito.";
                        }
            else{
                echo "\n El archivo $archivo esta actualizado no necasita se actualizado.";
               }
        } else {
            echo "\n No se puede escribir en el archivo $archivo. Verifica los permisos.";
        }
    }
}
