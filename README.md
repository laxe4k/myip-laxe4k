# MyIP - Laxe4k

![Licence](https://img.shields.io/github/license/laxe4k/myip.laxe4k.com)

## Description

**MyIP - Laxe4k** est une application web qui affiche votre adresse IP publique, votre nom d'hôte, votre localisation géographique (pays, ville, latitude, longitude), ainsi que la date et l'heure actuelles.

## Fonctionnalités

- **Adresse IP publique** : Affiche votre adresse IP externe.
- **Nom d'hôte** : Montre le nom d'hôte associé à votre adresse IP.
- **Localisation géographique** : Indique le pays et la ville correspondants à votre adresse IP.
- **Coordonnées GPS** : Fournit la latitude et la longitude de votre emplacement.
- **Date et heure actuelles** : Affiche la date et l'heure locales en temps réel.

## Technologies utilisées

- ![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)
- ![CSS3](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white)
- ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)
- ![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)

## Prérequis

- Un serveur web compatible PHP (par exemple, Apache ou Nginx)
- PHP version 7.4 ou supérieure
- Accès à Internet pour récupérer les informations de géolocalisation

## Installation

1. **Cloner le dépôt :**

    ```sh
    git clone https://github.com/laxe4k/myip.laxe4k.com.git
    ```

2. **Accéder au répertoire du projet :**

    ```sh
    cd myip.laxe4k.com
    ```

3. **Configurer le serveur web :**

   - Pour Apache : Assurez-vous que le module `mod_php` est activé et que le répertoire du projet est accessible via un hôte virtuel ou le répertoire `htdocs`.
   - Pour Nginx : Configurez le fichier de configuration pour pointer vers le répertoire du projet et assurez-vous que `php-fpm` est correctement configuré.

4. **Vérifier les permissions :**

   Assurez-vous que le serveur web a les permissions nécessaires pour lire les fichiers du projet.

## Déploiement

Le déploiement est automatisé grâce à GitHub Actions. À chaque push sur la branche principale, le workflow défini dans `.github/workflows/deploy.yml` est déclenché pour synchroniser les fichiers avec le serveur via FTP.

Pour configurer le déploiement automatique :

1. **Configurer les secrets GitHub :**

   - Accédez aux paramètres du dépôt sur GitHub.
   - Dans la section "Secrets and variables" > "Actions", ajoutez les secrets suivants :
     - `FTP_HOST` : l'adresse du serveur FTP
     - `FTP_USERNAME` : votre nom d'utilisateur FTP
     - `FTP_PASSWORD` : votre mot de passe FTP

2. **Personnaliser le workflow :**

   Si nécessaire, modifiez le fichier `.github/workflows/deploy.yml` pour adapter le processus de déploiement à vos besoins spécifiques.

## Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. **Forkez** le dépôt.
2. **Créez** une branche pour votre fonctionnalité ou correction de bug (`git checkout -b feature/ma-fonctionnalite`).
3. **Commitez** vos modifications (`git commit -m 'Ajout de ma fonctionnalité'`).
4. **Poussez** vers la branche (`git push origin feature/ma-fonctionnalite`).
5. **Ouvrez** une pull request.

Veuillez vous assurer que votre code respecte les normes de style du projet et qu'il est bien documenté.

## Licence

Ce projet est sous licence MIT. Veuillez consulter le fichier `LICENSE` pour plus de détails.

## Auteur

Développé par [Laxe4k](https://github.com/laxe4k).