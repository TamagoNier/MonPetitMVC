<?php
declare (strict_types=1);

namespace App\Controller;
use Tools\MyTwig;
use App\Model\GestionClientModel;
use ReflectionClass;
use App\Exceptions\AppException;


/**
 * Description of GestionClientController
 *
 * @author egor_
 */
class GestionClientController {
    
    public function chercheUn(array $params) {
        //Appel de la méthode find($id)de la classe Model adequate
        $modele = new GestionClientModel();
        $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
        $unClient = $modele->find($id);
        if($unClient){
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()). "/unClient.html.twig";
            MyTwig::afficheVue($vue, array('unClient'=>$unClient));
        }else{
            throw new AppException("Client ". $id. " inconnu");
        }
    }
    
    public function chercheTous(){
        // Appel la methode findAll() de la classe Model adequate
        $modele = new GestionClientModel();
        $clients = $modele->findAll();
        if($clients){
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()). "/tousClients.html.twig";
            MyTwig::afficheVue($vue, array('Clients'=>$clients));
        }else{
            throw new AppException("Aucun Client à afficher");
        }
    }
    
    public function creerClient(array $params){
        $vue = "GestionClientView\\creerClient.html.twig";
        MyTwig::afficheVue($vue, array());
    }
}
