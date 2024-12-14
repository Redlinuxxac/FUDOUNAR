#!/bin/bash
# Actualiza los cambios remotos
git fetch origin
# Une los cambios locales con los remotos de la rama master
git pull origin master

if [ $? -eq 0 ]; then
  echo "Actualización completada exitosamente."
else
  echo "Error al actualizar el repositorio."
fi