Gestion des evenements sur 2 niveaux : 

Le champ "parent" a été ajouté a l'évenement pour différencier le niveau d'un événement
Si ce champ est égale à 0, l'événement est parent (niveau 1), sinon, s'il est enfant, le champ parent est égal à l'id de l'événement parent
Si l'événement n'est lié à aucun autre événement (orphelin ou autre, sans grand rapport avec d'autres) il est égale à -1


Added parent field to create two level events
Father has field "parent" set to 0 and children set to idEvenement of his parent
Orphan events (unlinked to any other events) have "parent" field set to -1
