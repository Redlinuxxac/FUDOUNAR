#!/bin/bash

# Pregunta al usuario si desea actualizar
echo "¿Deseas actualizar el repositorio? (si/no)"
read respuesta

# Si la respuesta es "si" o "sí", procede con la actualización
if [[ "$respuesta" == "si" || "$respuesta" == "sí" ]]; then
  # Actualiza los cambios remotos
  git fetch origin

  # Une los cambios locales con los remotos de la rama master
  git pull origin master

  if [ $? -eq 0 ]; then
    echo "Actualización completada exitosamente."
  else
    echo "Error al actualizar el repositorio."
  fi
else
  echo "Se ha cancelado la actualización."
fi