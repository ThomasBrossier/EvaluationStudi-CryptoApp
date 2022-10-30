# EvaluationStudi-CryptoApp
Cette application a pour but une gestion de crypto monnaie et permet de voir l'evolution des gains/pertes

## I - Preparation du projet 

Commencez par cloner l'application.
```
git clone https://github.com/ThomasBrossier/EvaluationStudi-CryptoApp/
```
et allez dans le dossier de l'application. 
```
cd EvaluationStudi-CryptoApp/
```
Ensuite installez toutes les dependances php et javascript. 
```
composer install
```
et
```
yarn install
```
Enfin vous pouver build la config webpack via la commande : 
```
yarn build
```

## II - Preparation de la base de donnée. 

IL faut ensuite créer la base de donnée en mode dev
```
symfony console d:d:c
```
et
```
symfony console doctrine:schema:update -f
```
Nous pouvons faire de même pour la base de données de test. 
```
 symfony console d:d:c --env=test
 ```
 et
 ```
 symfony console doctrine:schema:update -f --env=test
 ```
 Cela permet de dissocier les base de données suivant les environnements. 
