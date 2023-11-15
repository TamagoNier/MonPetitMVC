<?php

declare (strict_types=1);

namespace App\Controller;

use Tools\myTwig;
use App\Model\GestionCommandeModel;
use ReflectionClass;
use App\Exceptions\AppException;
use Tools\Repository;

/**
 * Description of GestionCommandeController
 *
 * @author egor_
 */
class GestionCommandeController {

    public function chercheUne(array $params) {
        //Appel de la méthode find($id)de la classe Model adequate
        $repository = Repository::getRepository("App\Entity\Commande");
        $ids = $repository->findIds();
        $params['lesId'] = $ids;
        if (array_key_exists('id', $params)) {
            $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
            $uneCommande = $repository->find($id);
            if ($uneCommande) {
                $params['uneCommande'] = $uneCommande; //Commande Trouvé
            } else {
                $params['message'] = "Commande " . $id . " inconnu";
            }
        }
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/uneCommande.html.twig";
        MyTwig::afficheVue($vue, $params);
    }

    public function chercheToutes() {
        // Appel la methode findAll() de la classe Model adequate
        $repository = Repository::getRepository("App\Entity\Commande");
        $commandes = $repository->findAll();
        
        if ($commandes) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "/plusieursCommandes.html.twig";
            MyTwig::afficheVue($vue, array('Commandes'=>$commandes));
        } else {
            throw new AppException("Aucun Commande à afficher");
        }
    }
}
