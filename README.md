# altrh dwwm19 - symfony 

Ce projet est réalisé avec la promotion **altrh dwwm19** de l'année 2022.

Vous pouvez retrouver le projet sur <https://github.com/alexgaill/dwwm19-symfony>.

## Pour lancer le projet:

> Chargez les dépendances avec composer
>
> ``` 
> composer install 
> ```
>
> Modifiez la ligne du fichier *.env* pour la connexion à la BDD
>
> ``` 
> DATABASE_URL="mysql://root:@127.0.0.1:3306/superblog" # Pour Wamp et Xampp
>
> DATABASE_URL="mysql://root:root@127.0.0.1:8889/superblog" # Pour Mamp 
> ```
>
> Créez la BDD
>
> ``` 
> symfony console doctrine:database:create 
> ```
>
> Ajoutez les données en BDD
>
> ```
> symfony console doctrine:migrations:migrate
> 
> symfony console doctrine:fixtures:load 
> ```
>
> Lancez le serveur
>
> ``` 
> symfony server:start 
> ```

## Liens utiles 

[Documentation Symfony](https://symfony.com/doc/current/index.html)

[Documentation Twig](https://twig.symfony.com/)

[Documentation doctrine](https://www.doctrine-project.org/projects/orm.html)

[Fakerphp](https://fakerphp.github.io/)

## Une question sur le projet? 
[Contactez-moi](mailto:contact@steptosuccess.com)

## Pour aller plus loin vous pouvez:

> ### Rejoindre la communauté sur [discord](https://discord.gg/rb4bVeZX)
> Vous serez avertis des dernières news de cours, vous agrandirez la communauté d'entraide d'étudiants et devs juniors.
>
> ### Participer aux lives [twitch](https://www.twitch.tv/alex_gaill)
> Live de création et de mise à jour de cours, découvertes de langages et framework, talkshow sur différents sujets (S'améliorer en temps que dev, le freelancing, la recherche d'emploi/stage/alternance)

