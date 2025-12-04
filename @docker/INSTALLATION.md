# Installation de Docker sur macOS

Docker n'est pas installé sur votre système. Voici comment l'installer :

## Option 1 : Docker Desktop (Recommandé)

### Installation via le site officiel

1. **Télécharger Docker Desktop** :
   - Allez sur https://www.docker.com/products/docker-desktop/
   - Cliquez sur "Download for Mac"
   - Choisissez la version appropriée (Intel ou Apple Silicon)

2. **Installer Docker Desktop** :
   - Ouvrez le fichier `.dmg` téléchargé
   - Glissez Docker dans le dossier Applications
   - Lancez Docker Desktop depuis Applications
   - Acceptez les conditions d'utilisation

3. **Vérifier l'installation** :
   ```bash
   docker --version
   docker compose version
   ```

### Installation via Homebrew (Alternative)

Si vous avez Homebrew installé :

```bash
# Installer Docker Desktop
brew install --cask docker

# Lancer Docker Desktop
open /Applications/Docker.app
```

## Option 2 : Colima (Alternative légère)

Colima est une alternative plus légère à Docker Desktop :

```bash
# Installer Colima via Homebrew
brew install colima docker docker-compose

# Démarrer Colima
colima start

# Vérifier l'installation
docker --version
```

## Après l'installation

Une fois Docker installé et lancé :

1. **Vérifier que Docker fonctionne** :
   ```bash
   docker ps
   ```

2. **Construire et lancer le backend** :
   ```bash
   cd @docker
   docker compose -f docker-compose.back.yml up -d
   ```

3. **Vérifier les conteneurs** :
   ```bash
   docker compose -f docker-compose.back.yml ps
   ```

## Dépannage

### Docker n'est pas dans le PATH

Si Docker est installé mais la commande n'est pas trouvée :

```bash
# Ajouter Docker au PATH (si installé via Homebrew)
echo 'export PATH="/usr/local/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
```

### Docker Desktop ne démarre pas

- Vérifiez que Docker Desktop est bien lancé (icône dans la barre de menu)
- Redémarrez Docker Desktop si nécessaire
- Vérifiez les permissions dans les Préférences Système > Sécurité et confidentialité

## Ressources

- Documentation Docker : https://docs.docker.com/
- Docker Desktop pour Mac : https://docs.docker.com/desktop/install/mac-install/
