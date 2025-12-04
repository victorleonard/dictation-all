# Docker pour l'application Dictation

Ce dossier contient la configuration Docker pour le backend Symfony et le frontend Quasar.

## Fichiers Backend

- `Dockerfile.back` : Dockerfile pour construire l'image du backend Symfony (production)
- `Dockerfile.back.dev` : Dockerfile pour le développement (avec hot-reload)
- `docker-compose.back.yml` : Configuration Docker Compose pour le backend avec Nginx et PostgreSQL
- `nginx.conf` : Configuration Nginx pour servir l'application Symfony

## Fichiers Frontend

- `Dockerfile.front` : Dockerfile pour construire l'image du frontend Quasar (production)
- `Dockerfile.front.dev` : Dockerfile pour le développement (avec hot-reload)
- `docker-compose.front.yml` : Configuration Docker Compose pour le frontend (production)
- `docker-compose.front.dev.yml` : Configuration Docker Compose pour le frontend (développement)
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

### Démarrer uniquement le frontend (production)

```bash
cd @docker
docker compose -f docker-compose.front.yml up -d
```

### Démarrer le frontend en mode développement

```bash
cd @docker
docker compose -f docker-compose.front.dev.yml up -d
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
FRONTEND_DEV_PORT=9000
```

## Frontend - Commandes utiles

### Installer les dépendances dans le conteneur

```bash
docker compose -f docker-compose.front.dev.yml exec frontend-dev npm install
```

### Exécuter le build de production

```bash
docker compose -f docker-compose.front.dev.yml exec frontend-dev npm run build
```

### Accéder au shell du conteneur frontend

```bash
docker compose -f docker-compose.front.dev.yml exec frontend-dev sh
```

### Voir les logs du frontend

```bash
docker compose -f docker-compose.front.dev.yml logs -f frontend-dev
```

## Accès

- **Frontend** : http://localhost:8080 (port configuré via FRONTEND_PORT)
- **Backend API** : http://localhost:3000 (port configuré via NGINX_PORT, par défaut 3000)
- **Base de données** : localhost:5432 (port configuré via POSTGRES_PORT)
- **Frontend Dev** : http://localhost:9000 (port configuré via FRONTEND_DEV_PORT)

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

## Mode Développement

Pour le développement, utilisez `Dockerfile.back.dev` qui permet le hot-reload des fichiers :

1. Modifiez `docker-compose.back.yml` pour utiliser `Dockerfile.back.dev` :
   ```yaml
   backend:
     build:
       context: ..
       dockerfile: @docker/Dockerfile.back.dev
   ```

2. Le code source est monté en volume, donc les modifications sont immédiatement visibles.

3. Pour installer les dépendances dans le conteneur :
   ```bash
   docker compose -f docker-compose.back.yml exec backend composer install
   ```

4. Pour exécuter les migrations :
   ```bash
   docker compose -f docker-compose.back.yml exec backend php bin/console doctrine:migrations:migrate
   ```
