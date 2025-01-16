#!/bin/bash
# Función para obtener la última etiqueta
get_latest_tag() {
  git ls-remote --tags origin | grep -oP 'refs/tags/\K(.*)' | sort -V | tail -n 1
}

# Obtener las últimas etiquetas
latest_remote_tag=$(get_latest_tag)
local_tag=$(git describe --tags --abbrev=0 2>/dev/null)

# Mostrar información detallada de las versiones
echo "Versión remota actual: $latest_remote_tag"
#version local
echo "Versión local actual: $local_tag"

# Comparar y actualizar si es necesario
if [[ -z "$local_tag" || "$local_tag" != "$latest_remote_tag" ]]; then
  echo "La versión local está desactualizada. Se actualizará a $latest_remote_tag."
    git fetch origin
    git checkout $latest_remote_tag
    echo "Actualización completada."
else
  echo "La versión local está actualizada."
fi