<?php
declare (strict_types=1);

namespace App\Repository;

use Tools\Repository;
use PDO;
Use App\Entity\Client;

class ClientRepository extends Repository {
    
    public function enregistreClient(Client $client){
        try{
            $sql = "insert into Client(titreCli, nomCli, prenomCli, adresseRue1Cli, adresseRue2Cli, cpCli, villeCli, telCli) "
                    . "values(:titreCli, :nomCli, :prenomCli, :adresseRue1Cli, :adresseRue2Cli, :cpCli, :villeCli, :telCli)";
            $s = $this->connexion->prepare($sql);
            $s->bindValue(':titreCli', $client->getTitreCli(), PDO::PARAM_STR);
            $s->bindValue(':nomCli', $client->getNomCli(), PDO::PARAM_STR); 
            $s->bindValue(':prenomCli', $client->getPrenomCli(), PDO::PARAM_STR);
            $s->bindValue(':adresseRue1Cli', $client->getAdresseRue1Cli(), PDO::PARAM_STR);
            $s->bindValue(':adresseRue2Cli', ($client->getAdresseRue2Cli() == "") ? (null):($client->getAdresseRue2Cli()),PDO::PARAM_STR);
            $s->bindValue(':cpCli', $client->getCpCli(), PDO::PARAM_STR);
            $s->bindValue(':villeCli', $client->getVilleCli(), PDO::PARAM_STR);
            $s->bindValue(':telCli', $client->getTelCli(), PDO::PARAM_STR);
            $s->execute();
        } catch (PDOException) {
            throw new PDOException("Erreur technique innatendue");
        }
    }
}
