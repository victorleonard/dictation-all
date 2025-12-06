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

# Créer les répertoires var/cache et var/log avec les bonnes permissions
# Ces répertoires peuvent être écrasés par le volume backend_var
echo "Vérification des répertoires var/cache et var/log..."
mkdir -p /app/var/cache/prod /app/var/cache/dev /app/var/log
chown -R appuser:appuser /app/var 2>/dev/null || true
chmod -R 775 /app/var 2>/dev/null || true

# Générer les clés JWT si elles n'existent pas
if [ ! -f "/app/config/jwt/private.pem" ] || [ ! -f "/app/config/jwt/public.pem" ]; then
    echo "Génération des clés JWT..."
    mkdir -p /app/config/jwt
    # Générer la clé privée
    openssl genpkey -out /app/config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:${JWT_PASSPHRASE:-ChangeMeToASecretPassphrase} 2>/dev/null || \
    openssl genrsa -out /app/config/jwt/private.pem -aes256 -passout pass:${JWT_PASSPHRASE:-ChangeMeToASecretPassphrase} 4096 2>/dev/null || true
    # Générer la clé publique
    if [ -f "/app/config/jwt/private.pem" ]; then
        openssl pkey -in /app/config/jwt/private.pem -pubout -out /app/config/jwt/public.pem -passin pass:${JWT_PASSPHRASE:-ChangeMeToASecretPassphrase} 2>/dev/null || true
    fi
    chown -R appuser:appuser /app/config/jwt 2>/dev/null || true
    chmod 600 /app/config/jwt/*.pem 2>/dev/null || true
fi

# Si le volume public est monté et vide, copier le contenu depuis l'image
if [ -d "/app/public" ] && [ -d "/shared/public" ] && [ -z "$(ls -A /shared/public 2>/dev/null)" ]; then
    echo "Initialisation du volume public partagé..."
    cp -r /app/public/* /shared/public/ 2>/dev/null || true
    chown -R appuser:appuser /shared/public 2>/dev/null || true
fi

# Exécuter la commande (PHP-FPM doit s'exécuter en root pour créer les sockets)
exec "$@"

