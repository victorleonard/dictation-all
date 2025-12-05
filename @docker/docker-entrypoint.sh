#!/bin/sh
set -e

# Si le volume public est monté et vide, copier le contenu depuis l'image
if [ -d "/app/public" ] && [ -d "/shared/public" ] && [ -z "$(ls -A /shared/public 2>/dev/null)" ]; then
    echo "Initialisation du volume public partagé..."
    cp -r /app/public/* /shared/public/ 2>/dev/null || true
    chown -R appuser:appuser /shared/public 2>/dev/null || true
fi

# Exécuter la commande (PHP-FPM doit s'exécuter en root pour créer les sockets)
exec "$@"

