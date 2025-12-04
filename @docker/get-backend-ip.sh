#!/bin/bash
# Script pour obtenir l'IP du conteneur backend

echo "=== IP du conteneur backend ==="
docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' dictation_backend 2>/dev/null

echo ""
echo "=== Informations réseau complètes ==="
docker inspect dictation_backend | grep -A 20 "Networks" 2>/dev/null

echo ""
echo "=== IP via docker compose ==="
cd "$(dirname "$0")"
docker compose -f docker-compose.back.yml exec backend hostname -i 2>/dev/null || echo "Le conteneur n'est pas en cours d'exécution"
