# Artificial Intelligence Extinction 

## Règles du jeu 

C'est un jeu de survie dans lequel tu dois récupérer de la nourriture, des matériaux et réparer ton abri afin de survivre pendant 30 jours. 

### Les actions du jeu 

- Se reposer : Rajoute 10 points en énergie et retire 5 points de nourriture.
- Chercher de la nourriture : Rajoute 10 points de nourriture, enlève 10 points d'énergie. 
- Chercher des matériaux : Rajoute 10 points de matériaux, enlève 10 points d'énergie.
- Réparer l'abri : Rajoute 25 points à l'abri, enlève 10 points d'énergie et 10 points de matériaux. 

Lorsque l'on fait une action, la journée se termine et on perd 5 points de santé pour l'abri. 
Si on a pas assez de nourriture (0) lorsque l'on fait une action, on perd 10 points de santé en fin de journée. 
Si on a pas assez d'énergie (0) lorsqu'on veut faire une action, un message nous en informe et on est obligé de se reposer. 
Si l'abri est à 0, on perd 10 points de santé après une action à cause des radiations (à part l'action "Réparer l'abri" si on a suffisamment de matérieux). 
Si les matériaux sont à 0 et qu'on essaie de réparer l'abri, un message nous indique que l'on ne peut pas et on est obligé de faire une autre action.  


### Statistiques et ressources de début de jeu 

- Santé -> 100
- Énergie -> 100
- Abri ->	100
- Nourriture -> 0
- Matériaux -> 0


### Conditions de fin de partie 

Victoire : Tu gagnes la partie si les 30 jours se sont écoulés. 
Défaite : Tu perds la partie si les points de santé sont à 0.


### Comportement de la partie 

Lorsque l'on commence une partie, les statistiques du joueurs sont enregistrées dans la base de données. 
Si on termine une partie (victoire ou défaite), on peut relancer une nouvelle partie avec le bouton "Nouvelle partie" et les statistiques se réinitialisent dans la base de données pour une début de partie. 

Les boutons d'actions enregistrent automatiquement les changements dans la base de données (sauvegarde). 
La bouton "Voir les stats" permet d'afficher la partie de chaque joueur inscrit.
Le bouton "Se déconnecter" permet de déconnecter sa session. 


## Installation 

Il faut avoir PHP 8.1 ou une version supérieur, avoir composer installé et MySql

Pour voir si PHP et composer sont installés dans le terminal :

- php -v
- composer --version
- php -m | findstr /i pdo_mysql


### Récupérer le projet 

- git clone https://github.com/RaphaelP3/projet_aie.git
- cd projet_aie

### Installer les dépendances 

- composer install

(Permet l'installation de Twig)

### Créer la base de données 

Récupérer la structure de la base de données (script LDD) dans le fichier .loo puis créer la base de données avec MySql

### Configurer la connexion 

Il faut ouvrir config/database.php et adapter les lignes ci-dessous en fonction du nom de la base de données crée, du nom d'utilisateur et du mot de passe si un on en a un sur MySql :

- $host = '127.0.0.1';
- $dbname = 'nom_de_bdd';
- $username = 'root';
- $password = 'ton_mot_de_passe_mysql';

### Lancer le jeu 

Il faut être à la racine du projet et ensuite taper la commande suivante dans le terminal :

- php -S localhost:8000 -t public

Ensuite il faut ouvrir http://localhost:8000 dans son navigateur. 

Pour jouer : 

- Clique sur "S'inscrire" et crée un compte (pseudo et mot de passe obligatoire).
- Clique sur "Se connecter".
- Joue en utilisant les boutons d'actions.
