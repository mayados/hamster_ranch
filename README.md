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

## Lancer le serveur Symfony
php bin/console server:start 

## Lancer les fixtures
php bin/console d:f:l

## Création clé publique et privée pour le JWT
- Créer un fichier jwt dans le dossier config (si cela n'est pas déjà fait)
- Générer les clés :
        openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
        openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout 
- Placer la passphrase choisie dans le .env.local sous la variable JWT_PASSPHRASE

## Postman
sur la route /api/login , générer un JWT et le mettre dans l'authorization des différentes routes avec bearer token et coller sa valeur
Vous pouvez à présent tester les différentes routes






