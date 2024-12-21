#!/bin/bash

# Función para obtener la última etiqueta
get_latest_tag() {
  git ls-remote --tags origin | grep -oP 'refs/tags/\K(.*)' | sort -V | tail -n 1
}

# Obtener la última etiqueta remota
latest_tag=$(get_latest_tag)

# Obtener la etiqueta local (si existe)
local_tag=$(git describe --tags --abbrev=0 2>/dev/null)

# Comparar y actualizar si es necesario
if [[ -z "$local_tag" || "$local_tag" != "$latest_tag" ]]; then
  echo "La versión local está desactualizada. Actualizando..."
  git fetch origin
  git checkout $latest_tag
else
  echo "La versión local está actualizada."
fi