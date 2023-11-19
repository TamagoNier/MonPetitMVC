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
    
    public function creerClient(array $params) : void{
        if(empty($params)) {
            $vue = "GestionClientView\creerClient.html.twig";
            MyTwig::afficheVue($vue, array());
        }else {
            try{
            $params = $this->verificationSaisieClient($params);
            //creation de l'objet client à partir des données du formulaire
            $client= new Client($params);
            $repository = Repository::getRepository("App\Entity\Client");
            $repository->insert($client);
            $this->chercheTous();
        }catch(Exception){
            throw new AppException("Erreur à l'enregistrement d'un nouveau client");
    }}}
    
    private function verificationSaisieClient($params) : array {
        $params["nomCli"] = htmlspecialchars($params["nomCli"]);
        $params["prenomCli"] = htmlspecialchars($params["prenomCli"]);
        $params["adresseRue1Cli"] = htmlspecialchars($params["adresseRue1Cli"]);
        if($params["adresseRue2Cli"]){
            $params["adresseRue2Cli"] = htmlspecialchars($params["adresseRue2Cli"]);
        }
        $params["cpCli"] = filter_var($params["cpCli"], FILTER_SANITIZE_NUMBER_INT);
        $params["villeCli"] = htmlspecialchars($params["villeCli"]);
        $params["telCli"] = filter_var($params["telCli"], FILTER_SANITIZE_NUMBER_INT);
        return $params;
    }
    
    public function nbClients():void {
        $repository = Repository::getRepository("App\Entity\Client");
        $nbClients = $repository->countRows();
        echo "Nombre de clients : " . $nbClients;
    }
    
    public function statsClients(array $params) {
        $repositoryClient = Repository::getRepository("App\Entity\Client");
        $clientStats = $repositoryClient->statistiquesTousClients();
        if($clientStats){
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "\statsClients.html.twig";
            MyTwig::afficheVue($vue, array('clients' => $clientStats));
        }else{
            throw new AppException("Aucun clients");
        }
    }
    
    public function testFindBy(array $params) :void {
        $repository = Repository::getRepository("App\Entity\Client");
        $parametres = array("titreCli"=>"Monsieur", "villeCli" => "Toulon");
        $clients = $repository->findBytitreCli_and_villeCli($parametres);
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName())."/tousClients.html.twig";
        MyTwig::afficheVue($vue, array('Clients' => $clients));
    }
    
    public function rechercheClients(array $params) : void {
        $repository = Repository::getRepository("App\Entity\Client") ;
        $titres = $repository->findColumnDistinctValues('titreCli');
        $cps = $repository->findColumnDistinctValues('cpCli');
        $villes = $repository->findColumnDistinctValues('villeCli');
        $paramsVue ['titres'] = $titres;
        $paramsVue['cps'] = $cps;
        $paramsVue ['villes'] = $villes;
        
        //Gestion de retour du formulaire
        //On va d'abord filtrer et preparer le retour du formulaire avec la 
        //fonction verifieEtPrepareCriteres
        $criteresPrepares = $this->verifieEtPrepareCriteres($params);
        
        if(count($criteresPrepares)>0) {
            $clients = $repository->findBy($params);
            $paramsVue['Clients'] = $clients;
            foreach($criteresPrepares as $valeur) {
                ($valeur!= "Choisir...") ? ($criteres[]=$valeur):(null);
            }
            $paramsVue['criteres'] = $criteres;
            $vue = "GestionClientView\\tousClients.html.twig"; 
            MyTwig::afficheVue($vue, $paramsVue);
        }
        else{
            $vue = "GestionClientView\\filtreClients.html.twig";
            MyTwig::afficheVue($vue, $paramsVue);
        }       
    }
    
    public function verifieEtPrepareCriteres(array $params) : array {
        $args = array(
            'titreCli' => array(
                'filter' => FILTER_VALIDATE_REGEXP | FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE,
                'options' => array ('regexp' => '/^(Monsieur | Madame | Mademoiselle)$/')
            ),
            'cpCli' => array(
                'filter' => FILTER_VALIDATE_REGEXP | FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE,
                'options' => array ('regexp' => "/[0-9](5)/")
            ),
            'villeCli' => FILTER_SANITIZE_SPECIAL_CHARS,
        );
        $retour = filter_var_array($params, $args, false);
        if (isset($retour['titreCli']) || isset($retour ['cpCli']) || isset($retour ['villecli'])) {
            // c'est le retour du formulaire de choix de filtre
            $element = "Choisir ... ";
            while (in_array($element, $retour)) {
                unset($retour[array_search($element, $retour)]);
            }
        }
        return $retour;
    }
    
}
