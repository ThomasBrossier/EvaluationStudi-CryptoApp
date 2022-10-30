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
 
 Nous devons ajouter un utilisateur fictif dans chacune de ces bases pour pouvoir acceder à l'application. 
 
 Accédez à ladministration de votre base de données . et dans la table "USER" ajoutez une entrée. 
 Mettez l'e-mail :
  ```
 test@test.fr
  ```
 et comme mot de passe : 
  ```
  $2y$13$V1Owfy.wLpsZGhlbLyVETuLQEHshDl4s6NrHImlgsQBL7xoz7htSy
   ```
Ce qui correspond à : 
    ```
    azerty
     ```
une fois déhashé.
 
Si vous souaitez utiliser votre propre mot de passe, vous pouvez le générer grace à cette commande : 
    ```
    php bin/console security:hash-password VotreMotDePasse
    ```

Penser à faire la meme chose pour la base données de test.
  
  
## Effectuer les Tests

Enfin vous pouvez effectuer les tests via la commande : 
```
php bin/phpunit   
```
et même voir la couverture des tests via : 
```
php bin/phpunit --coverage-text 
```

Vous pouvez aussi vous connecter à l'application avec les identifiants crées précédement ! 
