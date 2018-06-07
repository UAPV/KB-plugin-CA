Projets 
==================================================================
Chef de projet : Jade Tavernier

Techno : Kanboard v1.2.4, mysql, php >= 5.6, ldap

Documentation intracri : https://intracri.univ-avignon.fr/index.php/Catalogue_d%27activit%C3%A9s

Liens : https://kanboard.org/
 
Description
-----------------


Plus d'information : https://kanboard.net/ 

attention !!!
ajouter dans le .htaccess :
 * RewriteCond %{HTTP:Authorization} ^(.+)$
 * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

Prerequis
-----------------
#### Librairies necessaires


#### Autres

#### API-DOSI

#### CRON
  
Process de mise en test / prod
-----------------
Les fichiers de configurations ne sont pas dans le dépôt git mais directement en prod ou en test

#### En test


#### En prod


Change Log
-----------------

