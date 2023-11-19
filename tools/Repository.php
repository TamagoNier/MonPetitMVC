<?php

declare (strict_types=1);

namespace Tools;

use App\Entity\Client;
use Tools\Connexion;
use PDO;

abstract class Repository {

    protected string $classeNameLong;
    protected string $classeNamespace;
    protected string $table;
    protected PDO $connexion;

    private function __construct(string $entity) {
        $tablo = explode("\\", $entity);
        $this->table = array_pop($tablo);
        $this->classeNamespace = implode("\\", $tablo);
        $this->classeNameLong = $entity;
        $this->connexion = Connexion::getConnexion();
    }

    public static function getRepository(string $entity): Repository {
        $repositoryName = str_replace('Entity', 'Repository', $entity) . 'Repository';
        $repository = new $repositoryName($entity);
        return $repository;
    }

    public function findAll(): array {
        $sql = "select * from " . $this->table;
        $lignes = $this->connexion->query($sql);
        $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
        return $lignes->fetchAll();
    }

    public function find(int $id): ?object {
        try {
            $sql = "select * from " . $this->table . " where id =:id";
            $ligne = $this->connexion->prepare($sql);
            $ligne->bindValue(':id', $id, PDO::PARAM_INT);
            $ligne->execute();
            $resultat = $ligne->fetchObject($this->classeNameLong);
            if($resultat) {
                return $resultat;
            } else {
                return null;
            }
        } catch (Exception) {
            throw new AppException("Erreur technique inattendue");
        }
    }

    public function findIds(): array {
        try {
            $sql = "select id from " . $this->table;
            $lignes = $this->connexion->query($sql);
            // on va configurer le mode objet pour la lisibilité du code
            if ($lignes->rowCount() > 0) {
                $t = $lignes->fetchAll(PDO::FETCH_ASSOC);
                return $t;
            } else {
                throw new AppException('Aucun element trouvé');
            }
        } catch (PDOException) {
            throw new AppException("Erreur technique inattendue");
        }
    }
    
    public function insert(object $object) : void {
        // conversion en tableau 
        $attributs = (array) $object;
        array_shift($attributs);
        $colonnes = "(";
        $colonnesParams = "(";
        $parametres = array();
        foreach($attributs as $cle => $valeur){
            $cle = str_replace("\0", "", $cle);
            $c = str_replace($this->classeNameLong, "", $cle);
            if($c != "id"){
                $colonnes .= $c.",";
                $colonnesParams .= " ? ,";
                $parametres[]=$valeur;
            }
        }
        $cols = substr($colonnes, 0, -1);
        $colsParams = substr($colonnesParams, 0, -1);
        $sql = "insert into ". $this->table . " " . $cols . ") values" . $colsParams . ")";
        $req = $this->connexion->prepare($sql);
        $req->execute($parametres);
    }
    
}
