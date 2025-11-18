# Symfony API Project

Ce projet est une application Symfony (API) et ce guide explique comment la lancer sur **Windows avec Laragon**, configurer la base de données, et tester les routes API avec JWT.

---

##  Prérequis

- [Laragon](https://laragon.org/) installé (version Full recommandée)  
- PHP >= 8.1  
- Composer installé  
- Symfony CLI (optionnel mais recommandé)  
- MySQL ou MariaDB (inclus avec Laragon)  
- Un client API pour tester (Postman, Insomnia…)

---

##  Cloner le projet
git clone <URL_DU_PROJET>
cd <NOM_DU_PROJET>

## Installer les dépendances
composer install

## Création du fichier .env.local
DATABASE_URL="mysql://root:root@127.0.0.1:3306/hamster?serverVersion=8.0"

## Création de la base de données
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate


## Créer un jeu de données
Créer dans la base de données les utilisateurs et hamsters

## Postman
sur la route /api/login , générer un JWT et le mettre dans authorization des différentes routes avec bearer token et coller sa valeur






