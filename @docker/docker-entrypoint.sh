#!/bin/sh
set -e

# Créer un fichier .env minimal si il n'existe pas
# Symfony a besoin de ce fichier même s'il est vide ou minimal
if [ ! -f "/app/.env" ]; then
    echo "Création du fichier .env minimal..."
    # Créer un fichier .env avec les valeurs par défaut
    # Les variables d'environnement Docker prendront le dessus
    cat > /app/.env <<EOF
# This file is auto-generated. Do not edit manually.
# Les variables d'environnement définies dans docker-compose.yml prennent le dessus
APP_ENV=prod
APP_DEBUG=0
EOF
    chown appuser:appuser /app/.env 2>/dev/null || true
fi

# Si le volume public est monté et vide, copier le contenu depuis l'image
if [ -d "/app/public" ] && [ -d "/shared/public" ] && [ -z "$(ls -A /shared/public 2>/dev/null)" ]; then
    echo "Initialisation du volume public partagé..."
    cp -r /app/public/* /shared/public/ 2>/dev/null || true
    chown -R appuser:appuser /shared/public 2>/dev/null || true
fi

# Exécuter la commande (PHP-FPM doit s'exécuter en root pour créer les sockets)
exec "$@"

