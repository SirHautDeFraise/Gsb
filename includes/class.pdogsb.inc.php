
<?php

/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */
class PdoGsb {

  private static $serveur = 'mysql:host=localhost:3306';
  private static $bdd = 'dbname=gsb_frais';
  private static $user = 'userGsb';
  private static $mdp = 'secret';
  private static $monPdo;
  private static $monPdoGsb = null;

  /**
   * Constructeur privé, crée l'instance de PDO qui sera sollicitée
   * pour toutes les méthodes de la classe
   */
  private function __construct() {
    PdoGsb::$monPdo = new PDO(
      PdoGsb::$serveur . ';' . PdoGsb::$bdd,
      PdoGsb::$user,
      PdoGsb::$mdp
    );
    PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
  }

  /**
   * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
   * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
   */
  public function __destruct() {
    PdoGsb::$monPdo = null;
  }

  /**
   * Fonction statique qui crée l'unique instance de la classe
   * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
   *
   * @return l'unique objet de la classe PdoGsb
   */
  public static function getPdoGsb() {
    if (PdoGsb::$monPdoGsb == null) {
      PdoGsb::$monPdoGsb = new PdoGsb();
    }
    return PdoGsb::$monPdoGsb;
  }

  /**
   * Retourne les informations d'un visiteur
   *
   * @param String $login Login du visiteur
   * @param String $mdp   Mot de passe du visiteur
   *
   * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
   */
  public function getInfosVisiteur($login, $mdp) {
    if ($this->verif_mdp($mdp, 'visiteur', $login)) {
      $requetePrepare = PdoGsb::$monPdo->prepare(
        'SELECT visiteur.id AS id, visiteur.nom AS nom, '
        . 'visiteur.prenom AS prenom '
        . 'FROM visiteur '
        . 'WHERE visiteur.login = :unLogin'
      );
      $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
      $requetePrepare->execute();
      return $requetePrepare->fetch();
    }
    return null;
  }

  /**
   * Permet de récupérer le mot de passe visiteur
   * @param string $login le login visiteur
   * @return type retourne le mot de passe
   */
  public function recupereMdpVisiteur(string $login) {
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'SELECT  mdp '
      . 'FROM visiteur '
      . 'WHERE visiteur.login = :unLogin');
    $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
    $requetePrepare->execute();
    return $requetePrepare->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Permet de récupérer le mot de passe comptable
   * @param string $login le login comptable
   * @return type retourne le mot de passe
   */
  public function recupereMdpComptable(string $login) {
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'SELECT  mdp '
      . 'FROM comptable '
      . 'WHERE comptable.login = :unLogin');
    $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
    $requetePrepare->execute();
    return $requetePrepare->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Permet de vérifier si le mot de passe en paramètre est bien celui associé au login 
   * @param string $mdp le mot de passe entré
   * @param string $table la table sur laquelle on cherche
   * @param string $login le login
   * @return boolean si mot de passe correspond
   */
  public function verif_mdp(string $mdp, string $table, string $login) {
    if ($table === 'visiteur') {
      $resultat = $this->recupereMdpVisiteur($login);
    } else if ($table === 'comptable') {
      $resultat = $this->recupereMdpComptable($login);
    }
    $mdp = hash('sha512', $mdp);
    $mdp_hash = $resultat['mdp'];
    if ($mdp === $mdp_hash) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Retourne les infos du comptable
   * @param type $login le login mis dans le formulaire
   * @param type $mdp le mot de passe mis dans le formulaire
   * @return type
   */
  public function getInfosComptable($login, $mdp) {
    $verif = $this->verif_mdp($mdp, 'comptable', $login);
    $mdp_hash = password_hash($mdp, PASSWORD_BCRYPT);
    if ($verif) {
      $requetePrepare = PdoGsb::$monPdo->prepare(
        'SELECT comptable.id AS id, comptable.nom AS nom, '
        . 'comptable.prenom AS prenom '
        . 'FROM comptable '
        . 'WHERE comptable.login = :unLogin'
      );
      $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
      $requetePrepare->execute();
      return $requetePrepare->fetch();
    }
    return null;
  }

  /**
   * Retourne sous forme d'un tableau associatif toutes les lignes de frais
   * hors forfait concernées par les deux arguments.
   * La boucle foreach ne peut être utilisée ici car on procède
   * à une modification de la structure itérée - transformation du champ date-
   *
   * @param String $idVisiteur ID du visiteur
   * @param String $mois       Mois sous la forme aaaamm
   *
   * @return tous les champs des lignes de frais hors forfait sous la forme
   * d'un tableau associatif
   */
  public function getLesFraisHorsForfait($idVisiteur, $mois) {
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'SELECT * FROM lignefraishorsforfait '
      . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
      . 'AND lignefraishorsforfait.mois = :unMois'
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
    $lesLignes = $requetePrepare->fetchAll();
    for ($i = 0; $i < count($lesLignes); $i++) {
      $date = $lesLignes[$i]['date'];
      $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
    }
    return $lesLignes;
  }

  /**
   * Retourne le nombre de justificatif d'un visiteur pour un mois donné
   *
   * @param String $idVisiteur ID du visiteur
   * @param String $mois       Mois sous la forme aaaamm
   *
   * @return le nombre entier de justificatifs
   */
  public function getNbjustificatifs($idVisiteur, $mois) {
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
      . 'AND fichefrais.mois = :unMois'
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
    $laLigne = $requetePrepare->fetch();
    return $laLigne['nb'];
  }

  /**
   * Retourne sous forme d'un tableau associatif toutes les lignes de frais
   * au forfait concernées par les deux arguments
   *
   * @param String $idVisiteur ID du visiteur
   * @param String $mois       Mois sous la forme aaaamm
   *
   * @return l'id, le libelle et la quantité sous la forme d'un tableau
   * associatif
   */
  public function getLesFraisForfait($idVisiteur, $mois) {
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'select fraisforfait.id as idfrais,fraisforfait.libelle as libelle,lignefraisforfait.quantite as quantite,fraisforfait.montant as prix,fraiskm.prix as fraiskm '
      . 'from lignefraisforfait inner join fraisforfait '
      . 'on fraisforfait.id=lignefraisforfait.idfraisforfait inner join visiteur '
      . 'on visiteur.id=lignefraisforfait.idvisiteur inner join fraiskm '
      . 'on visiteur.idVehicule=fraiskm.id '
      . 'where lignefraisforfait.idvisiteur= :unIdVisiteur and '
      . 'lignefraisforfait.mois= :unMois'
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
    return $requetePrepare->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Retourne tous les id de la table FraisForfait
   *
   * @return un tableau associatif
   */
  public function getLesIdFrais() {
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'SELECT fraisforfait.id as idfrais '
      . 'FROM fraisforfait ORDER BY fraisforfait.id'
    );
    $requetePrepare->execute();
    return $requetePrepare->fetchAll();
  }

  /**
   * Met à jour la table ligneFraisForfait
   * Met à jour la table ligneFraisForfait pour un visiteur et
   * un mois donné en enregistrant les nouveaux montants
   *
   * @param String $idVisiteur ID du visiteur
   * @param String $mois       Mois sous la forme aaaamm
   * @param Array  $lesFrais   tableau associatif de clé idFrais et
   *                           de valeur la quantité pour ce frais
   *
   * @return null
   */
  public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
    $lesCles = array_keys($lesFrais);
    foreach ($lesCles as $unIdFrais) {
      $qte = $lesFrais[$unIdFrais];
      $requetePrepare = PdoGSB::$monPdo->prepare(
        'UPDATE lignefraisforfait '
        . 'SET lignefraisforfait.quantite = :uneQte '
        . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
        . 'AND lignefraisforfait.mois = :unMois '
        . 'AND lignefraisforfait.idfraisforfait = :idFrais'
      );
      $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
      $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
      $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
      $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
      $requetePrepare->execute();
    }
  }

  /**
   * Met à jour le nombre de justificatifs de la table ficheFrais
   * pour le mois et le visiteur concerné
   *
   * @param String  $idVisiteur      ID du visiteur
   * @param String  $mois            Mois sous la forme aaaamm
   * @param Integer $nbJustificatifs Nombre de justificatifs
   *
   * @return null
   */
  public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'UPDATE fichefrais '
      . 'SET nbjustificatifs = :unNbJustificatifs '
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
      . 'AND fichefrais.mois = :unMois'
    );
    $requetePrepare->bindParam(
      ':unNbJustificatifs',
      $nbJustificatifs,
      PDO::PARAM_INT
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
  }

  /**
   * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
   *
   * @param String $idVisiteur ID du visiteur
   * @param String $mois       Mois sous la forme aaaamm
   *
   * @return vrai ou faux
   */
  public function estPremierFraisMois($idVisiteur, $mois) {
    $boolReturn = false;
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'SELECT fichefrais.mois FROM fichefrais '
      . 'WHERE fichefrais.mois = :unMois '
      . 'AND fichefrais.idvisiteur = :unIdVisiteur'
    );
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->execute();
    if (!$requetePrepare->fetch()) {
      $boolReturn = true;
    }
    return $boolReturn;
  }

  /**
   * Retourne le dernier mois en cours d'un visiteur
   *
   * @param String $idVisiteur ID du visiteur
   *
   * @return le mois sous la forme aaaamm
   */
  public function dernierMoisSaisi($idVisiteur) {
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'SELECT MAX(mois) as dernierMois '
      . 'FROM fichefrais '
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->execute();
    $laLigne = $requetePrepare->fetch();
    $dernierMois = $laLigne['dernierMois'];
    return $dernierMois;
  }

  /**
   * Crée une nouvelle fiche de frais et les lignes de frais au forfait
   * pour un visiteur et un mois donnés
   *
   * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
   * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
   * les lignes de frais forfait de quantités nulles
   *
   * @param String $idVisiteur ID du visiteur
   * @param String $mois       Mois sous la forme aaaamm
   *
   * @return null
   */
  public function creeNouvellesLignesFrais($idVisiteur, $mois) {
    $dernierMois = $this->dernierMoisSaisi($idVisiteur);
    $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
    if ($laDerniereFiche['idEtat'] == 'CR') {
      $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
    }
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
      . 'montantvalide,datemodif,idetat) '
      . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
    $lesIdFrais = $this->getLesIdFrais();
    foreach ($lesIdFrais as $unIdFrais) {
      $requetePrepare = PdoGsb::$monPdo->prepare(
        'INSERT INTO lignefraisforfait (idvisiteur,mois,'
        . 'idfraisforfait,quantite) '
        . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
      );
      $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
      $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
      $requetePrepare->bindParam(
        ':idFrais',
        $unIdFrais['idfrais'],
        PDO::PARAM_STR
      );
      $requetePrepare->execute();
    }
  }

  /**
   * Crée un nouveau frais hors forfait pour un visiteur un mois donné
   * à partir des informations fournies en paramètre
   *
   * @param String $idVisiteur ID du visiteur
   * @param String $mois       Mois sous la forme aaaamm
   * @param String $libelle    Libellé du frais
   * @param String $date       Date du frais au format français jj//mm/aaaa
   * @param Float  $montant    Montant du frais
   *
   * @return null
   */
  public function creeNouveauFraisHorsForfait(
    $idVisiteur,
    $mois,
    $libelle,
    $date,
    $montant
  ) {
    $dateFr = dateFrancaisVersAnglais($date);
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'INSERT INTO lignefraishorsforfait '
      . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
      . ':unMontant) '
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
    $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
    $requetePrepare->execute();
  }

  /**
   * Supprime le frais hors forfait dont l'id est passé en argument
   *
   * @param String $idFrais ID du frais
   *
   * @return null
   */
  public function supprimerFraisHorsForfait(string $idFrais) {
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'DELETE FROM lignefraishorsforfait '
      . 'WHERE lignefraishorsforfait.id = :unIdFrais'
    );
    $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
    $requetePrepare->execute();
  }

  /**
   * Retourne les mois pour lesquel un visiteur a une fiche de frais
   *
   * @param String $idVisiteur ID du visiteur
   *
   * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
   *         l'année et le mois correspondant
   */
  public function getLesMoisDisponibles(string $idVisiteur) {
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'SELECT fichefrais.mois AS mois FROM fichefrais '
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
      . 'ORDER BY fichefrais.mois desc'
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->execute();
    $lesMois = array();
    while ($laLigne = $requetePrepare->fetch()) {
      $mois = $laLigne['mois'];
      $numAnnee = substr($mois, 0, 4);
      $numMois = substr($mois, 4, 2);
      $lesMois[] = array(
        'mois' => $mois,
        'numAnnee' => $numAnnee,
        'numMois' => $numMois
      );
    }
    return $lesMois;
  }

  /**
   * Retourne les mois pour lesquel un visiteur a une fiche de frais CR
   *
   * @param String $idVisiteur ID du visiteur
   *
   * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
   *         l'année et le mois correspondant
   */
  public function getLesMoisDisponiblesCR(string $idVisiteur) {
    $idEtat = 'CL';
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'SELECT fichefrais.mois AS mois FROM fichefrais '
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
      . 'AND fichefrais.idetat = :idEtat '
      . 'ORDER BY fichefrais.mois desc'
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':idEtat', $idEtat, PDO::PARAM_STR);
    $requetePrepare->execute();
    $lesMois = array();
    while ($laLigne = $requetePrepare->fetch()) {
      $mois = $laLigne['mois'];
      $numAnnee = substr($mois, 0, 4);
      $numMois = substr($mois, 4, 2);
      $lesMois[] = array(
        'mois' => $mois,
        'numAnnee' => $numAnnee,
        'numMois' => $numMois
      );
    }
    return $lesMois;
  }

  /**
   * Retourne les informations d'une fiche de frais d'un visiteur pour un
   * mois donné
   *
   * @param String $idVisiteur ID du visiteur
   * @param String $mois       Mois sous la forme aaaamm
   *
   * @return un tableau avec des champs de jointure entre une fiche de frais
   *         et la ligne d'état
   */
  public function getLesInfosFicheFrais(string $idVisiteur, string $mois) {
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'SELECT fichefrais.idetat as idEtat, '
      . 'fichefrais.datemodif as dateModif,'
      . 'fichefrais.nbjustificatifs as nbJustificatifs, '
      . 'fichefrais.montantvalide as montantValide, '
      . 'etat.libelle as libEtat '
      . 'FROM fichefrais '
      . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
      . 'AND fichefrais.mois = :unMois'
    );
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
    $laLigne = $requetePrepare->fetchAll(PDO::FETCH_ASSOC);
    return $laLigne;
  }

  /**
   * Modifie l'état et la date de modification d'une fiche de frais.
   * Modifie le champ idEtat et met la date de modif à aujourd'hui.
   *
   * @param String $idVisiteur ID du visiteur
   * @param String $mois       Mois sous la forme aaaamm
   * @param String $etat       Nouvel état de la fiche de frais
   *
   * @return null
   */
  public function majEtatFicheFrais(string $idVisiteur, string $mois, string $etat) {
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'UPDATE ficheFrais '
      . 'SET idetat = :unEtat, datemodif = now() '
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
      . 'AND fichefrais.mois = :unMois'
    );
    $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
  }

  
  /**
   * Retourne une liste de tous les visiteurs medicaux de GSB.
   *
   * @return un tableau associatif key : [nom][prenom] contenant tout les visiteurs medicaux 
   */
  public function getListeVisiteurs() {
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'SELECT visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom'
      . ' FROM visiteur'
      . ' ORDER BY id, nom, prenom asc'
    );
    $requetePrepare->execute();
    $lesVisiteurs = array();
    $lignes = $requetePrepare->fetchAll();
    foreach ($lignes as $ligne) {
      $id = $ligne['id'];
      $nom = $ligne['nom'];
      $prenom = $ligne['prenom'];
      $lesVisiteurs[] = array(
        'id' => $id,
        'nom' => $nom,
        'prenom' => $prenom
      );
    }
    return $lesVisiteurs;
  }

  /**
   * Recupere l'id d'un visiteur via son nom et prenom
   *
   * @param String $nom nom du visiteur
   * @param String $prenom prenom du visiteur
   *
   * @return l'id du visiteur concerner
   */
  public function getIdVisiteur(string $nom, string $prenom) {
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'SELECT visiteur.id'
      . ' FROM visiteur'
      . ' WHERE visiteur.nom = :unNom && visiteur.prenom = :unPrenom'
    );
    $requetePrepare->bindParam(':unNom', $nom, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unPrenom', $prenom, PDO::PARAM_STR);
    $requetePrepare->execute();
    $id = $requetePrepare->fetch();
    return $id;
  }

  /**
   * Valide la fiche de frais
   *
   * @param string $idVisiteur id du visiteur
   * @param string $mois mois de la fiche a valider
   * @param float $montant montant de la fiche à valider
   */
  public function validerFicheDeFrais(string $idVisiteur, string $mois, string $montant) {
    $dateCourante = date('Y-m-d');
    $idEtat = 'VA';
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'UPDATE fichefrais '
      . 'SET fichefrais.montantvalide = :unMontant, fichefrais.datemodif = :uneDate, fichefrais.idetat = :unIdEtat '
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
      . 'AND fichefrais.mois = :unMois'
    );
    $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
    $requetePrepare->bindParam(':uneDate', $dateCourante, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unIdEtat', $idEtat, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
  }

  /**
   *  Modifier les elements d'une fiche hors frais 
   * 
   * 
   * @param type $idVisiteur id du visiteur
   * @param type $mois le mois de la modif
   * @param type $lesHorsForfaitLibelle  libelle de la fiche hors frais
   * @param type $lesHorsForfaitMontant montant de la fiche hors frais
   * @param type $lesHorsForfaitDate date de la fiche hors frais
   */
  public function majFraisHorsForfait($idVisiteur, $mois, $lesHorsForfaitLibelle, $lesHorsForfaitMontant, $lesHorsForfaitDate) {
    $lesCles = array_keys($lesHorsForfaitLibelle);
    foreach ($lesCles as $unIdHorsFrais) {
      $libelle = $lesHorsForfaitLibelle[$unIdHorsFrais];
      $montant = $lesHorsForfaitMontant[$unIdHorsFrais];
      $date = $lesHorsForfaitDate[$unIdHorsFrais];
      $requetePrepare = PdoGSB::$monPdo->prepare(
        'UPDATE lignefraishorsforfait '
        . 'SET lignefraishorsforfait.libelle = :unLibelle, lignefraishorsforfait.montant = :unMontant, lignefraishorsforfait.date = :uneDate '
        . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
        . 'AND lignefraishorsforfait.mois = :unMois '
        . 'AND lignefraishorsforfait.id = :unId'
      );
      $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
      $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
      $requetePrepare->bindParam(':uneDate', $date, PDO::PARAM_STR);
      $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
      $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
      $requetePrepare->bindParam(':unId', $unIdHorsFrais, PDO::PARAM_INT);
      $requetePrepare->execute();
    }
  }

  /**
   * Cette fonction ajoute le terme REFUSE devant le libelle, non accepté par le comptable
   * @param type $idFrais
   */
  public function refuserFraisHorsForfait(string $idFrais) {
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'UPDATE lignefraishorsforfait '
      . 'SET lignefraishorsforfait.libelle= LEFT(CONCAT("REFUSE"," ",libelle),100) '
      . 'WHERE lignefraishorsforfait.id = :unIdFrais'
    );
    $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
    $requetePrepare->execute();
  }

  /**
   * Fonction qui retourne le mois suivant un mois passé en paramètre
   *
   * @param String $mois Contient le mois à utiliser
   *
   * @return String le mois d'après
   */
  public function getMoisSuivant(string $mois) {
    $numAnnee = substr($mois, 0, 4);
    $numMois = substr($mois, 4, 2);
    if ($numMois == '12') {
      $numMois = '01';
      $numAnnee++;
    } else {
      $numMois++;
    }
    if (strlen($numMois) == 1) {
      $numMois = '0' . $numMois;
    }
    return $numAnnee . $numMois;
  }

  /**
   * si il n y a pas de justificatifs, le frais est reporté pour le mois suivant
   * @param type $idFrais
   */
  public function reporterFraisHorsForfait(string $idFrais, string $ceMois) {
    $mois = $this->getMoisSuivant($ceMois);
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'UPDATE lignefraishorsforfait '
      . 'SET lignefraishorsforfait.mois= :unMois '
      . 'WHERE lignefraishorsforfait.id = :unIdFrais'
    );
    $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
    return $mois;
  }

  /**
   *  Fonction qui permet de retirer le montant en paramètre au montant validé 
   *  après report ou suppression du frais
   * 
   * @param string $idVisiteur l'id du visiteur
   * @param string $mois le mois de la fiche de frais à modifier
   * @param string $montant le montant à soustraire au montant.
   */
  public function retirerMontantFicheFrais(string $idVisiteur, string $mois, string $montant) {
    $dateCourante = date('Y-m-d');
    $requetePrepare = PdoGsb::$monPdo->prepare(
      'UPDATE fichefrais '
      . 'SET fichefrais.montantValide = fichefrais.montantValide - :unMontant '
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
      . 'AND fichefrais.mois = :unMois ');

    $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
    $requetePrepare->bindParam(':uneDate', $dateCourante, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
  }

  /**
   *  Retourne une liste de tous les visiteurs qui ont une fiche de frais validée
   * 
   * @param string $mois
   * @return type
   */
  public function getVisiteurFromMoisVA(string $mois) {
    $requetePrepare = PdoGSB::$monPdo->prepare(
      "select CONCAT(nom, ' ', prenom)as nomvisiteur, idvisiteur as visiteur from fichefrais "
      . "inner join visiteur on visiteur.id = fichefrais.idvisiteur "
      . "where mois=:unMois "
      . "AND idetat='VA'");
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
    $res = $requetePrepare->fetchAll(PDO::FETCH_ASSOC);
    return $res;
  }

  /**
   * Fonction qui change le statut de la fiche de l'idVisiteur 
   * en mise en paiement
   * 
   * @param string $idVisiteur l'id du visiteur
   * @param string $mois la date de la fiche de frais
   * @param string $montant le montant de la fiche de frais
   */
  public function validerFicheDeFraisVA(string $idVisiteur, string $mois, string $montant) {
    $dateCourante = date('Y-m-d');
    $idEtat = 'MP';
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'UPDATE fichefrais '
      . 'SET fichefrais.montantvalide = :unMontant, fichefrais.datemodif = :uneDate, fichefrais.idetat = :unIdEtat '
      . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
      . 'AND fichefrais.mois = :unMois '
    );
    $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
    $requetePrepare->bindParam(':uneDate', $dateCourante, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unIdEtat', $idEtat, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
    $requetePrepare->execute();
  }

}
