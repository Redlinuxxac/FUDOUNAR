#!/bin/bash

# Función para obtener la versión del repositorio remoto
get_remote_version() {
  version=$( git ls-remote --tags origin | grep -oP 'refs/tags/\K(.*)' | sort -V | tail -n 1)
  echo "$version"
}

# Función para guardar la versión en el archivo version.red
save_version() {
  version="$1"
  echo "$version" > version.red
}

# Obtener la versión actual del repositorio remoto
remote_version=$(get_remote_version)


# Verificar si existe el archivo version.red
if [ ! -f version.red ]; then
  # Crear el archivo y guardar la versión actual
  save_version "$remote_version"
  echo "Archivo version.red creado y versión inicializada."
else
  # Leer la versión del archivo
  local_version=$(cat version.red)

# Mostrar información detallada de las versiones
echo "Versión remota actual: $remote_version"
#version local
echo "Versión local actual: $local_version"

  # Comparar versiones
  if [ "$local_version" \< "$remote_version" ]; then
    # Actualizar al repositorio remoto
    echo "La versión local está desactualizada. Se actualizará a $remote_version."
    #git fetch origin
    # Une los cambios locales con los remotos de la rama master
    #git pull origin master
    #git checkout $remote_version
    # Guardar la nueva versión
    save_version "$remote_version D4"
    echo "Repositorio actualizado a la versión $remote_version."
    echo "Actualización completada."
  else
    echo "El repositorio local está actualizado."
  fi
fi