<?php
declare (strict_types=1);
use Tools\Connexion;

namespace Tools;

abstract class Repository {

    private string $classeNameLong;
    private string $classeNamespace;
    private string $table;
    private PDO $connexion;
    
    private function __construct(string $entity) {
        $tablo = explode("\\", $entity);
        $this->table = array_pop($tablo);
        $this->classeNamespace = implode("\\", $tablo);
        $this->classeNameLong = $entity;
        $this->connexion= Connexion::getConnexion();
    }
    
    public static function getRepository(string $entity): Repository{
        $repositoryName = str_replace('Entity', 'Repository', $entity) . 'Repository';
        $repository = new $repositoryName($entity);
        return $repository;
    }
    
    public function findAll() : array{
        $sql = "select * from ".$this->table;
        $lignes= $this->connexion->query($sql);
        $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
        return $lignes->fetchAll();
    }
}
