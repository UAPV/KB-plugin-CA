==Présentation==
============
Un plugin a été développé pour récupérer les informations de la DOSI de Kanboard permettant d'avoir une vision globale des activités de la DOSI.

https://projets.univ-avignon.fr/indicateurs/dosi

Le catalogue d'activité DOSI récupère les informations des projets DOSI sur http://projets.univ-avignon.fr.

Nous retrouvons sur cette page des informations sur les activités DOSI et les activités hors catalogue d'activité DOSI.

Un compteur, un diagramme montrant la répartition des projets par catégories et un tableau avec quelques informations.

Le catalogue d'activité DOSI récupère les informations renseignées dans chaque projet "KanBoard" pour afficher une synthèse (nom, catégories, Chef de projet/exploitation, Référent technique DOSI, Référent fonctionnel, description, ...). 

Il faut donc suivre à lettre les instructions ci-dessous, surtout pour les champs utilisés pour la synthèse ci-dessus.

===Différence entre les activités : Projets / Exploitations===
-------------------
:Les activités sont divisées en deux groupes différenciés par leurs champs :

Projet : 
* Une date de début, 
* Une date de fin, 
* Une catégorie obligatoire : "Projet"
* Des catégories optionnels à mettre en plus :  Stand-by ou Abandonné

Exploitation : 
* Une date de fin facultative
* Aucune de catégorie obligatoire
* Des catégories optionnels à mettre en plus :  Stand-by ou Abandonné

=== Validation ===
-------------------
À l'instar d'un bon de commande, une activité doit être validée (objectif, coût, planning) par Steph/Max.

Lorsqu'une personne modifie un des champs importants d'une activité, elle bascule dans un tableau "Modifications" pour qu'elle soit re-validée.

Un mail est envoyé aux membres, chefs de projet/responsable d'exploitation du projet KB validé + aux administrateurs.

=== Modification ===
-------------------
Lorsque vous modifiez un champ décrit ci-dessous, un mail est envoyé aux membres, chefs de projet du projet validé + aux administrateurs.
Ce mail contient les modifications effectuées.

== Mode opératoire de saisi (Fonctionnel)==
======
Pour certains champs des activités du catalogue, des champs de KanBoard ont directement été utilisés (par exemple la date de fin). Pour d'autres champs, on n'a pas trouvé d'équivalents pertinents dans KanBoard. Ces autres champs sont définis dans la description du projet KB qui est de type texte libre. Le texte contenu dans la description du projet KB est analysé pour affecter les champs du catalogue, il faut donc faire attention à bien respecter le formalisme du texte de la description KB.

La description du projet KB et les autres champs KB utilisés pour le catalogue se situent dans les préférences du projet KB.

=== Accéder aux préférences du projet KB ===
-------------------
* Aller sur la page du projet voulu : cliquer sur le lien du projet dans la page d'accueil ou le sélectionner dans la liste déroulante en haut à droite
* Aller sur les préférences du projet : cliquer sur "Menu" en haut à gauche puis "Préférences"

===Projet===
-------------------
/!\ Un projet doit avoir une date de début, une date de fin et une catégorie "projet"

Voici la liste des champs à définir (Les détails et la marche à suivre pour chaque champ se trouve à la suite) :
* Catégories : projet (+ si besoin : stand-by ou abandonné)
* Chef de projet 
* Date début et date fin
* Référent fonctionnel
* Référent technique
* Suppléant Technique
* Description
* Coût
* Lien Application
* Lien Intracri  => Voir la page [[Gestion de projets (DevOps)|Gestion de projets]]
* Lien FAQ


####==== Catégories ====
Tout projet doit avoir obligatoirement la catégorie "projet" : la sous catégorie (En cours, En retard, Futur) sera calculé en fonction des date de début et date de fin du projet

Un projet peut également avoir une autre catégorie :
* a/ Stand-by : Projet validé mais en pause
* b/ Abandonné

Pour modifier les catégories :
* [[#Accéder aux préférences du projet KB]]
* Aller dans catégories (Menu de gauche) et ajouter la ou les catégories associées à ce projet (seules les catégories précisées plus haut doivent être ajoutées)

####==== Chef de projet ====
Sur KB (projets.univ-avignon.fr), le chef de projet est appelé le "Responsable du projet".

Pour modifier le chef de projet :
* [[#Accéder aux préférences du projet KB]]
* Cliquer sur "Permission" dans le menu de gauche 
* Taper le nom de la personne et le choisir dans la liste des propositions, choisir dans la liste déroulante "Chef de projet" puis cliquer sur ajouter (A ce niveau toutes les personnes doivent avoir la permission "chef de projet")
* Cliquer sur "Modifier le projet" dans le menu de gauche 
* Choisir le titulaire du projet dans la liste déroulante "Responsable du projet" et cliquer sur "Enregistrer"

####==== Dates ====
La '''date de début''' mais surtout la '''date de fin''' sont nécessaires.

Pour modifier les dates :
* [[#Accéder aux préférences du projet KB]]
* Cliquer sur "Modifier le projet" dans le menu de gauche 
* Cliquer sur "Date"
* Cliquer sur "Enregistrer"

####==== Autres champs projet ====
Les autres champs du catalogue dont on n'a pas trouvé d'équivalent pertinent dans KanBoard sont définis dans le champ description. Le texte contenu dans la description du projet KB est analysé pour renseigner les champs du catalogue, il faut donc faire attention à bien respecter ce qui suit.

Pour accéder à la description du projet dans KB
* [[#Accéder aux préférences du projet KB]]
* Cliquer sur "Modifier le projet" dans le menu de gauche puis "Description" lien au dessous du titre

Syntaxe à respecter

"* Nom du champ du catalogue : sa valeur"

N.B. : "* ", les caractères 'étoile' 'espace' permettent de faire une liste, il est possible d'utiliser directement l’icône associé dans la barre d'outil

; Les champs du catalogue
* Description = une phrase simple de description du projet
* Référent fonctionnel
* Référent technique DOSI
* Suppléant Technique DOSI
* Coût = estimation du coût humain et financier
* Lien Application = le lien vers l'application
* Lien Intracri
* Lien FAQ site de la DOSI

Exemple pour l'application annuaire : 
* Description : Annuaire des personnels de l'Université d'Avignon
* Référent fonctionnel : XX / ZZ
* Référent technique : TT
* Suppléant technique : FF
* Coût : 
* Lien Application : http://annuaire.univ-avignon.fr
* Lien Intracri : https://intracri.univ-avignon.fr/index.php/PPD:Annuaire
* Lien FAQ site de la DOSI : https://dosi.univ-avignon.fr/faq/annuaire

=== Exploitation ===
-------
Voici la liste des champs à définir (Les détails et la marche à suivre pour chaque champ se trouve à la suite) :
* Responsable d'exploitation
* Date de renouvellement
* Référent fonctionnel
* Référent technique
* Suppléant Technique
* Coût
* Lien Application
* Lien Intracri
* Lien FAQ


####==== Catégories ====
Une exploitation peut avoir une catégorie :
* a/ Stand-by : Exploitation validé mais en pause
* b/ Abandonné

Pour modifier les catégories :
* [[#Accéder aux préférences du projet KB]]
* Aller dans catégories (Menu de gauche) et ajouter la ou les catégories associées à cette exploitation (seules les catégories précisées plus haut doivent être ajoutées)


####==== Chef d'exploitation ====
Sur KB (projets.univ-avignon.fr), c'est le "Responsable du projet".
* [[#Accéder aux préférences du projet KB]]
* Aller sur les "Permission" dans le menu de gauche
* Taper le nom de la personne et le choisir dans la liste des propositions, choisir dans la liste déroulante "Chef de projet" puis cliquer sur ajouter (à ce niveau toutes les personnes doivent avoir la permission "chef de projet")
* Cliquer sur "Modifier le projet" dans le menu de gauche 
* Choisir le '''chef d'exploitation''' dans la liste déroulante "Responsable du projet" et cliquer sur "Enregistrer"

####==== Date de renouvellement (optionnelle) ====
La date de renouvellement n'est pas forcément pertinente pour toutes les exploitations.
* [[#Accéder aux préférences du projet KB]]
* Cliquer sur "Modifier le projet" dans le menu de gauche 
* Cliquer sur "Date de fin"
* Cliquer sur "Enregistrer"

####==== Autres champs exploitation ====
Les autres champs du catalogue dont on n'a pas trouvé d'équivalent pertinent dans KanBoard sont définis dans le champ description. Le texte contenu dans la description du projet KB est analysé pour renseigner les champs du catalogue, il faut donc faire attention à bien respecter ce qui suit.

Pour accéder à la description du projet dans KB
* [[#Accéder aux préférences du projet KB]]
* Cliquer sur "Modifier le projet" dans le menu de gauche puis "Description" lien au dessous du titre

Syntaxe à respecter
"* Nom du champ du catalogue : sa valeur"

N.B. : "* ", les caractères 'étoile' 'espace' permettent de faire une liste, il est possible d'utiliser directement l’icône associé dans la barre d'outil

; Les champs du catalogue
* Description = une phrase simple de description de l'exploitation
* Référent fonctionnel
* Référent technique DOSI
* Suppléant Technique DOSI
* Coût = estimation du coût humain et financier
* Lien Application = le lien vers l'application
* Lien Intracri
* Lien FAQ site de la DOSI

Exemple pour l'application annuaire : 
 * Description : Annuaire des personnels de l'Université d'Avignon
 * Référent fonctionnel : XX / ZZ
 * Référent technique : TT
 * Suppléant technique : FF
 * Coût : 
 * Lien Application : http://annuaire.univ-avignon.fr
 * Lien Intracri : https://intracri.univ-avignon.fr/index.php/PPD:Annuaire
 * Lien FAQ site de la DOSI : https://dosi.univ-avignon.fr/faq/annuaire

=== Fermer / Terminer une activité ===
----
Voici la marche à suivre lorsqu'on veut fermer une activité (projet ou exploitation)
* [[#Accéder aux préférences du projet KB]] "SI - Outils - Annuaire"
* Dans le menu de gauche, cliquez sur "Fermer ce projet" (le projet disparaîtra de votre liste de projet Kanboard mais restera dans le catalogue d'activité DOSI. 

Pour le retrouver dans KanBoard, aller dans Menu / Gérer les projets puis trier par "État")

=== Passage d'une activité "projet" en "exploitation" ===
------
Voici la marche à suivre lorsqu'un projet est fini et que l'on souhaite passer en mode exploitation :
* Aller sur le projet concerné exemple : "SI - Outils - Annuaire"
* [[#Accéder aux préférences du projet KB]] "SI - Outils - Annuaire"
* Dans le menu de gauche cliquer sur Dupliquer (Choisir ce que vous voulez dupliquer)
* Valider : vous vous retrouvez sur le nouveau projet avec pour nom : "SI - Outils - Annuaire (Clone)"
* Retourner sur le projet "SI - Outils - Annuaire" puis Menu / Préférences
* Dans le menu de gauche, cliquez sur "Fermer ce projet" (le projet disparaitra de votre liste de projet Kanboard mais restera dans le catalogue d'activité DOSI. 
* Retourner sur "SI - Outils - Annuaire (Clone)", allez dans les préférences, puis "modifier le projet'
* renommez le projet en supprimant (Clone)
* supprimer la date de début et modifier la date de fin à la valeur de "renouvellement" voulu 
* Cliquer sur "enregistrer" en bas de page
* puis cliquer sur "Catégories" dans le menu de gauche
* supprimer obligatoirement la catégorie "projet" et si necessaire les autres catégories egalement

=== Passer son projet en "Stand-by" ===
---
:voir la section : [[#Cat.C3.A9gories | Catégories]]
:

=== Passer son projet en "abandonnée" ===
----
:voir la section : [[#Cat.C3.A9gories | Catégories]]
:Vous pouvez si vous le souhaiter "fermer ce projet", voir [[#Fermer_.2F_Terminer_une_activit.C3.A9 | Fermer une activité]]

=== En anomalie ===
---
:Plusieurs états peuvent mettre une activité en anomalie :
::Le projet est "Fermer", mais la date de fin est dans le futur
::Un projet ayant ni date de fin ni date de debut

==Technique==
====
; Architecture du plugin

:Accueil
::La vision global est disponible pour le moment donc dans le permier tableau il y a l'exploitation et les projets rassemblé, puis le tableau "projets modifié" puis le tableau projets en attente.

:Projets
::Liste des projets validé et ayant pour catégories : projet ou stand-by ou abandonnée

:Exploitation 
::Liste des projet validé n'ayant pas de catégorie

:Modifié 
::Liste des projets validé ayant été modifié 

:En attente
::Liste de projet non validé
