![logo]
# OC - P8 -  TaskLinker  !

Guillaume, notre stagiaire, a fait un prototype de l’outil en HTML et CSS, qui a été validé en interne. II faudrait que tu t'appuies dessus pour réaliser l’outil.

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
## Authors

- [@Neeemos](https://github.com/Neeemos)
