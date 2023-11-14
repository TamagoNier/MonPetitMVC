<?php
declare (strict_types=1);

namespace App\Controller;
use Tools\MyTwig;
use Tools\Repository;
use App\Model\GestionClientModel;
use ReflectionClass;
use App\Exceptions\AppException;
use App\Entity\Client;


/**
 * Description of GestionClientController
 *
 * @author egor_
 */
class GestionClientController {
    
    public function chercheUn(array $params) {
        // Recuperation d'un objet ClientRepository
        $repository = Repository::getRepository("App\Entity\Client");
        //on recupere tous les id des clients
        $ids = $repository->findIds();
        //on place les ids trouves dans le tableau de parapetres à envoyer à la vue
        $params['lesId'] = $ids;
        //on teste si l'id du client à cherhcher à été passé dans l'url
        if(array_key_exists('id', $params)){
            $id = filter_var(intval($params['id']), FILTER_VALIDATE_INT);
            $unClient= $repository->find($id);
            if($unClient){
                $params['unClient'] = $unClient; //Client Trouvé
            }else{
                $params['message'] = "Client ".$id." inconnu";
            }
        }
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/unClient.html.twig";
        MyTwig::afficheVue($vue, $params);
    }
    
    public function chercheTous(){
        // Recuperation d'un objet ClientRepository
        $repository = Repository::getRepository("App\Entity\Client");
        $clients = $repository->findAll();
        if($clients){
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()). "/tousClients.html.twig";
            MyTwig::afficheVue($vue, array('Clients'=>$clients));
        }else{
            throw new AppException("Aucun Client à afficher");
        }
    }
    
    public function creerClient(array $params){
        $vue = "GestionClientView\creerClient.html.twig";
        MyTwig::afficheVue($vue, array());
    }
    
    public function enregistreClient(array $params) {
        try{
            //creation dde l'objet client à partir du form 
            $client = new Client($params);
            $repository = Repository::getRepository("App\Entity\Client");
            $repository->enregistreClient($client);
            header('Location: ?c=gestionClient&a=ChercheUn');
        } catch (Exception) {
            throw new AppException("Erreur de l'enregistrement d'un nouveau client");
        }
    }
}
