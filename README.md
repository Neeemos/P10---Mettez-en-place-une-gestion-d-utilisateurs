![logo]
# OC - P10 -  TaskLinker Auth  !

Cet exercice vise donc à vous faire compléter l’outil TaskLinker, sur lequel vous avez déjà bien travaillé. L’objectif sera d’ajouter une couche de sécurité avec une authentification et des vérifications d’accès. À l’issue de cet exercice, votre site sera complet et 100 % fonctionnel. 

## Installation 

Installer les dépendances 

```bash
  composer install
```
Modifier le fichier .env avec les bonnes données de connexion à la base de données.

```bash 
DATABASE_URL="mysql://USERNAME:PASSWORD@127.0.0.1:3306/DATABASENAME?serverVersion=8.0.32&charset=utf8mb4"
```
Creation de la database vierge
```bash 
symfony console doctrine:database:create
```
Migration des schémas de la base de données

```bash 
symfony console doctrine:migrations:migrate
```
**Non obligatoire** : load les fixtures

```bash 
symfony console doctrine:fixtures:load

```

## DEPLOIEMENT

Lancez le serveur web


```bash
symfony serve -d 
```

## Note

Votre service php doit inclure l'extension gd

```bash
XAMPP Ajouter a votre php.init : extension=gd
```

## Authors

- [@Neeemos](https://github.com/Neeemos)
