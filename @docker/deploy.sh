#!/bin/bash

# Script de déploiement Docker pour l'application Dictation
# Usage: ./deploy.sh [commande] [options]

set -e

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Répertoire du script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Fonction d'aide
show_help() {
    echo -e "${BLUE}Script de déploiement Docker - Application Dictation${NC}"
    echo ""
    echo "Usage: ./deploy.sh [commande] [options]"
    echo ""
    echo "Commandes disponibles:"
    echo "  start [all|backend|frontend|frontend-dev]  Démarre les services"
    echo "  stop [all|backend|frontend|frontend-dev]   Arrête les services"
    echo "  restart [all|backend|frontend]             Redémarre les services"
    echo "  build [all|backend|frontend]               Construit les images"
    echo "  rebuild [all|backend|frontend]             Reconstruit les images (sans cache)"
    echo "  deploy [all|backend|frontend]              Déploie (supprime + reconstruit + redémarre)"
    echo "  logs [service]                              Affiche les logs"
    echo "  status                                     Affiche le statut des conteneurs"
    echo "  migrate                                    Exécute les migrations de base de données"
    echo "  clean                                      Nettoie les conteneurs et volumes arrêtés"
    echo "  clean-all                                  Nettoie tout (conteneurs, volumes, images)"
    echo "  shell [backend|frontend]                   Ouvre un shell dans le conteneur"
    echo "  help                                       Affiche cette aide"
    echo ""
    echo "Exemples:"
    echo "  ./deploy.sh start all                      Démarre tous les services"
    echo "  ./deploy.sh start backend                  Démarre uniquement le backend"
    echo "  ./deploy.sh start frontend-dev             Démarre le frontend en mode dev"
    echo "  ./deploy.sh rebuild backend                Reconstruit l'image backend"
    echo "  ./deploy.sh deploy backend                 Déploie le backend (supprime + reconstruit + redémarre)"
    echo "  ./deploy.sh logs backend                   Affiche les logs du backend"
    echo "  ./deploy.sh migrate                        Exécute les migrations"
}

# Fonction pour vérifier si Docker est installé
check_docker() {
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}Erreur: Docker n'est pas installé${NC}"
        echo "Consultez @docker/INSTALLATION.md pour les instructions d'installation"
        exit 1
    fi
    
    if ! docker info &> /dev/null; then
        echo -e "${RED}Erreur: Docker n'est pas démarré${NC}"
        echo "Veuillez démarrer Docker Desktop ou Colima"
        exit 1
    fi
}

# Fonction pour démarrer les services
start_services() {
    local service=$1
    
    case $service in
        all)
            echo -e "${GREEN}Démarrage de tous les services...${NC}"
            docker compose -f docker-compose.yml up -d
            echo -e "${GREEN}Services démarrés avec succès!${NC}"
            echo -e "${BLUE}Frontend: http://localhost:8080${NC}"
            echo -e "${BLUE}Backend API: http://localhost:3000${NC}"
            ;;
        backend)
            echo -e "${GREEN}Démarrage du backend...${NC}"
            docker compose -f docker-compose.back.yml up -d
            echo -e "${GREEN}Backend démarré avec succès!${NC}"
            echo -e "${BLUE}Backend API: http://localhost:3000${NC}"
            ;;
        frontend)
            echo -e "${GREEN}Démarrage du frontend (production)...${NC}"
            docker compose -f docker-compose.front.yml up -d
            echo -e "${GREEN}Frontend démarré avec succès!${NC}"
            echo -e "${BLUE}Frontend: http://localhost:8080${NC}"
            ;;
        frontend-dev)
            echo -e "${GREEN}Démarrage du frontend (développement)...${NC}"
            docker compose -f docker-compose.front.dev.yml up -d
            echo -e "${GREEN}Frontend dev démarré avec succès!${NC}"
            echo -e "${BLUE}Frontend Dev: http://localhost:9000${NC}"
            ;;
        *)
            echo -e "${RED}Service inconnu: $service${NC}"
            echo "Services disponibles: all, backend, frontend, frontend-dev"
            exit 1
            ;;
    esac
}

# Fonction pour arrêter les services
stop_services() {
    local service=$1
    
    case $service in
        all)
            echo -e "${YELLOW}Arrêt de tous les services...${NC}"
            docker compose -f docker-compose.yml down
            docker compose -f docker-compose.back.yml down 2>/dev/null || true
            docker compose -f docker-compose.front.yml down 2>/dev/null || true
            docker compose -f docker-compose.front.dev.yml down 2>/dev/null || true
            echo -e "${GREEN}Services arrêtés${NC}"
            ;;
        backend)
            echo -e "${YELLOW}Arrêt du backend...${NC}"
            docker compose -f docker-compose.back.yml down
            echo -e "${GREEN}Backend arrêté${NC}"
            ;;
        frontend)
            echo -e "${YELLOW}Arrêt du frontend...${NC}"
            docker compose -f docker-compose.front.yml down
            docker compose -f docker-compose.front.dev.yml down 2>/dev/null || true
            echo -e "${GREEN}Frontend arrêté${NC}"
            ;;
        frontend-dev)
            echo -e "${YELLOW}Arrêt du frontend dev...${NC}"
            docker compose -f docker-compose.front.dev.yml down
            echo -e "${GREEN}Frontend dev arrêté${NC}"
            ;;
        *)
            echo -e "${RED}Service inconnu: $service${NC}"
            exit 1
            ;;
    esac
}

# Fonction pour redémarrer les services
restart_services() {
    local service=$1
    
    echo -e "${YELLOW}Redémarrage de $service...${NC}"
    stop_services "$service"
    sleep 2
    start_services "$service"
}

# Fonction pour construire les images
build_images() {
    local service=$1
    
    case $service in
        all)
            echo -e "${GREEN}Construction de toutes les images...${NC}"
            docker compose -f docker-compose.yml build
            echo -e "${GREEN}Images construites avec succès!${NC}"
            ;;
        backend)
            echo -e "${GREEN}Construction de l'image backend...${NC}"
            docker compose -f docker-compose.back.yml build
            echo -e "${GREEN}Image backend construite avec succès!${NC}"
            ;;
        frontend)
            echo -e "${GREEN}Construction de l'image frontend...${NC}"
            docker compose -f docker-compose.front.yml build
            echo -e "${GREEN}Image frontend construite avec succès!${NC}"
            ;;
        *)
            echo -e "${RED}Service inconnu: $service${NC}"
            exit 1
            ;;
    esac
}

# Fonction pour reconstruire les images (sans cache)
rebuild_images() {
    local service=$1
    
    case $service in
        all)
            echo -e "${GREEN}Reconstruction de toutes les images (sans cache)...${NC}"
            docker compose -f docker-compose.yml build --no-cache
            echo -e "${GREEN}Images reconstruites avec succès!${NC}"
            ;;
        backend)
            echo -e "${GREEN}Reconstruction de l'image backend (sans cache)...${NC}"
            docker compose -f docker-compose.back.yml build --no-cache
            echo -e "${GREEN}Image backend reconstruite avec succès!${NC}"
            ;;
        frontend)
            echo -e "${GREEN}Reconstruction de l'image frontend (sans cache)...${NC}"
            docker compose -f docker-compose.front.yml build --no-cache
            echo -e "${GREEN}Image frontend reconstruite avec succès!${NC}"
            ;;
        *)
            echo -e "${RED}Service inconnu: $service${NC}"
            exit 1
            ;;
    esac
}

# Fonction pour déployer (suppression + reconstruction + redémarrage)
deploy_services() {
    local service=$1
    
    case $service in
        all)
            echo -e "${YELLOW}Déploiement complet: arrêt et suppression des conteneurs...${NC}"
            docker compose -f docker-compose.yml down
            echo -e "${GREEN}Reconstruction des images (sans cache)...${NC}"
            docker compose -f docker-compose.yml build --no-cache
            echo -e "${GREEN}Démarrage des services...${NC}"
            docker compose -f docker-compose.yml up -d
            echo -e "${GREEN}Déploiement terminé avec succès!${NC}"
            echo -e "${BLUE}Frontend: http://localhost:8080${NC}"
            echo -e "${BLUE}Backend API: http://localhost:3000${NC}"
            ;;
        backend)
            echo -e "${YELLOW}Déploiement backend: arrêt et suppression des conteneurs...${NC}"
            docker compose -f docker-compose.back.yml down
            echo -e "${GREEN}Reconstruction de l'image backend (sans cache)...${NC}"
            docker compose -f docker-compose.back.yml build --no-cache
            echo -e "${GREEN}Démarrage du backend...${NC}"
            docker compose -f docker-compose.back.yml up -d
            echo -e "${GREEN}Déploiement backend terminé avec succès!${NC}"
            echo -e "${BLUE}Backend API: http://localhost:3000${NC}"
            ;;
        frontend)
            echo -e "${YELLOW}Déploiement frontend: arrêt et suppression des conteneurs...${NC}"
            docker compose -f docker-compose.front.yml down
            docker compose -f docker-compose.front.dev.yml down 2>/dev/null || true
            echo -e "${GREEN}Reconstruction de l'image frontend (sans cache)...${NC}"
            docker compose -f docker-compose.front.yml build --no-cache
            echo -e "${GREEN}Démarrage du frontend...${NC}"
            docker compose -f docker-compose.front.yml up -d
            echo -e "${GREEN}Déploiement frontend terminé avec succès!${NC}"
            echo -e "${BLUE}Frontend: http://localhost:8080${NC}"
            ;;
        *)
            echo -e "${RED}Service inconnu: $service${NC}"
            echo "Services disponibles: all, backend, frontend"
            exit 1
            ;;
    esac
}

# Fonction pour afficher les logs
show_logs() {
    local service=$1
    
    if [ -z "$service" ]; then
        echo -e "${YELLOW}Affichage des logs de tous les services...${NC}"
        docker compose -f docker-compose.yml logs -f
    else
        case $service in
            backend)
                docker compose -f docker-compose.back.yml logs -f backend
                ;;
            frontend)
                docker compose -f docker-compose.front.yml logs -f frontend
                ;;
            frontend-dev)
                docker compose -f docker-compose.front.dev.yml logs -f frontend-dev
                ;;
            database)
                docker compose -f docker-compose.back.yml logs -f database
                ;;
            nginx)
                docker compose -f docker-compose.back.yml logs -f nginx
                ;;
            *)
                echo -e "${RED}Service inconnu: $service${NC}"
                echo "Services disponibles: backend, frontend, frontend-dev, database, nginx"
                exit 1
                ;;
        esac
    fi
}

# Fonction pour afficher le statut
show_status() {
    echo -e "${BLUE}Statut des conteneurs:${NC}"
    echo ""
    echo -e "${YELLOW}Services complets:${NC}"
    docker compose -f docker-compose.yml ps 2>/dev/null || echo "Aucun service complet en cours"
    echo ""
    echo -e "${YELLOW}Backend:${NC}"
    docker compose -f docker-compose.back.yml ps 2>/dev/null || echo "Backend non démarré"
    echo ""
    echo -e "${YELLOW}Frontend:${NC}"
    docker compose -f docker-compose.front.yml ps 2>/dev/null || echo "Frontend non démarré"
    echo ""
    echo -e "${YELLOW}Frontend Dev:${NC}"
    docker compose -f docker-compose.front.dev.yml ps 2>/dev/null || echo "Frontend dev non démarré"
}

# Fonction pour exécuter les migrations
run_migrations() {
    echo -e "${GREEN}Exécution des migrations de base de données...${NC}"
    
    # Vérifier si le backend est en cours d'exécution
    if ! docker compose -f docker-compose.back.yml ps backend | grep -q "Up"; then
        echo -e "${YELLOW}Le backend n'est pas démarré. Démarrage...${NC}"
        docker compose -f docker-compose.back.yml up -d backend
        echo -e "${YELLOW}Attente du démarrage du backend...${NC}"
        sleep 5
    fi
    
    docker compose -f docker-compose.back.yml exec backend php bin/console doctrine:migrations:migrate --no-interaction
    echo -e "${GREEN}Migrations exécutées avec succès!${NC}"
}

# Fonction pour nettoyer
clean_containers() {
    echo -e "${YELLOW}Nettoyage des conteneurs et volumes arrêtés...${NC}"
    docker compose -f docker-compose.yml down 2>/dev/null || true
    docker compose -f docker-compose.back.yml down 2>/dev/null || true
    docker compose -f docker-compose.front.yml down 2>/dev/null || true
    docker compose -f docker-compose.front.dev.yml down 2>/dev/null || true
    docker system prune -f
    echo -e "${GREEN}Nettoyage terminé${NC}"
}

# Fonction pour nettoyer tout
clean_all() {
    echo -e "${RED}ATTENTION: Cette opération va supprimer tous les conteneurs, volumes et images!${NC}"
    read -p "Êtes-vous sûr? (oui/non): " confirm
    if [ "$confirm" != "oui" ]; then
        echo "Opération annulée"
        exit 0
    fi
    
    echo -e "${YELLOW}Nettoyage complet...${NC}"
    docker compose -f docker-compose.yml down -v 2>/dev/null || true
    docker compose -f docker-compose.back.yml down -v 2>/dev/null || true
    docker compose -f docker-compose.front.yml down -v 2>/dev/null || true
    docker compose -f docker-compose.front.dev.yml down -v 2>/dev/null || true
    docker system prune -af --volumes
    echo -e "${GREEN}Nettoyage complet terminé${NC}"
}

# Fonction pour ouvrir un shell
open_shell() {
    local service=$1
    
    case $service in
        backend)
            if docker compose -f docker-compose.back.yml ps backend | grep -q "Up"; then
                docker compose -f docker-compose.back.yml exec backend sh
            else
                echo -e "${RED}Le backend n'est pas démarré${NC}"
                exit 1
            fi
            ;;
        frontend)
            if docker compose -f docker-compose.front.yml ps frontend | grep -q "Up"; then
                docker compose -f docker-compose.front.yml exec frontend sh
            else
                echo -e "${RED}Le frontend n'est pas démarré${NC}"
                exit 1
            fi
            ;;
        frontend-dev)
            if docker compose -f docker-compose.front.dev.yml ps frontend-dev | grep -q "Up"; then
                docker compose -f docker-compose.front.dev.yml exec frontend-dev sh
            else
                echo -e "${RED}Le frontend dev n'est pas démarré${NC}"
                exit 1
            fi
            ;;
        *)
            echo -e "${RED}Service inconnu: $service${NC}"
            echo "Services disponibles: backend, frontend, frontend-dev"
            exit 1
            ;;
    esac
}

# Vérification de Docker
check_docker

# Traitement des commandes
case "${1:-help}" in
    start)
        start_services "${2:-all}"
        ;;
    stop)
        stop_services "${2:-all}"
        ;;
    restart)
        restart_services "${2:-all}"
        ;;
    build)
        build_images "${2:-all}"
        ;;
    rebuild)
        rebuild_images "${2:-all}"
        ;;
    deploy)
        deploy_services "${2:-all}"
        ;;
    logs)
        show_logs "$2"
        ;;
    status)
        show_status
        ;;
    migrate)
        run_migrations
        ;;
    clean)
        clean_containers
        ;;
    clean-all)
        clean_all
        ;;
    shell)
        if [ -z "$2" ]; then
            echo -e "${RED}Veuillez spécifier un service (backend, frontend, frontend-dev)${NC}"
            exit 1
        fi
        open_shell "$2"
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        echo -e "${RED}Commande inconnue: $1${NC}"
        echo ""
        show_help
        exit 1
        ;;
esac
