<?php
declare (strict_types=1);

namespace App\Model;

use PDO;
use App\Entity\Commande;
use Tools\Connexion;
use Exception;
use App\Exceptions\AppException;

/**
 * Description of GestionCommandeModel
 *
 * @author egor_
 */
class GestionCommandeModel {
    public function find(int $id): Commande {
        try{
            $unObjetPdo = Connexion::getConnexion();
            $sql = "select * from COMMANDE where id =:id";
            $ligne = $unObjetPdo->prepare($sql);
            $ligne->bindValue(':id', $id, PDO::PARAM_INT);
            $ligne->execute();
            return $ligne->fetchObject(Commande::class);
        } catch (Exception) {
            throw new AppException("Erreur technique inattendue") ;
        }
    }
}