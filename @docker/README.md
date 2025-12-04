# Docker pour l'application Dictation

Ce dossier contient la configuration Docker pour le backend Symfony et le frontend Quasar.

## Fichiers Backend

- `Dockerfile.back` : Dockerfile pour construire l'image du backend Symfony
- `docker-compose.back.yml` : Configuration Docker Compose pour le backend avec Nginx et MySQL
- `nginx.conf` : Configuration Nginx pour servir l'application Symfony

## Fichiers Frontend

- `Dockerfile.front` : Dockerfile pour construire l'image du frontend Quasar
- `docker-compose.front.yml` : Configuration Docker Compose pour le frontend
- `nginx-front.conf` : Configuration Nginx pour servir les fichiers statiques du frontend

## Fichiers Communs

- `docker-compose.yml` : Configuration complète avec backend, frontend et base de données
- `.dockerignore` : Fichiers à exclure lors de la construction de l'image

## Utilisation

### Démarrer toute l'application (Backend + Frontend + Base de données)

```bash
cd @docker
docker compose up -d
```

### Démarrer uniquement le backend

```bash
cd @docker
docker compose -f docker-compose.back.yml up -d
```

### Démarrer uniquement le frontend

```bash
cd @docker
docker compose -f docker-compose.front.yml up -d
```

### Construction manuelle des images

```bash
cd @docker

# Backend
docker build -f Dockerfile.back -t dictation-backend ..

# Frontend
docker build -f Dockerfile.front -t dictation-frontend ..
```

### Arrêter les conteneurs

```bash
docker compose -f docker-compose.back.yml down
```

### Voir les logs

```bash
docker compose -f docker-compose.back.yml logs -f backend
```

## Configuration

Les variables d'environnement peuvent être définies dans un fichier `.env` dans le dossier `@docker` :

```env
APP_ENV=prod
APP_DEBUG=0
DATABASE_URL=postgresql://app:!ChangeMe!@database:5432/app?serverVersion=16&charset=utf8
POSTGRES_VERSION=16
POSTGRES_DB=app
POSTGRES_USER=app
POSTGRES_PASSWORD=!ChangeMe!
POSTGRES_PORT=5432
NGINX_PORT=3000
FRONTEND_PORT=8080
```

## Frontend - Commandes utiles

### Accéder au shell du conteneur frontend

```bash
docker compose -f docker-compose.front.yml exec frontend sh
```

### Voir les logs du frontend

```bash
docker compose -f docker-compose.front.yml logs -f frontend
```

## Accès

- **Frontend** : http://localhost:8080 (port configuré via FRONTEND_PORT)
- **Backend API** : http://localhost:3000 (port configuré via NGINX_PORT, par défaut 3000)
- **Base de données** : localhost:3306 (port configuré via MYSQL_PORT)

## Backend - Commandes utiles

### Exécuter des commandes Symfony dans le conteneur

```bash
docker compose -f docker-compose.back.yml exec backend php bin/console cache:clear
docker compose -f docker-compose.back.yml exec backend php bin/console doctrine:migrations:migrate
```

### Accéder au shell du conteneur

```bash
docker compose -f docker-compose.back.yml exec backend sh
```

### Reconstruire l'image après modification

```bash
docker compose -f docker-compose.back.yml build --no-cache backend
docker compose -f docker-compose.back.yml up -d
```

