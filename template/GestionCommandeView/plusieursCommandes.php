<?php
include_once PATH_VIEW . "header.html";
echo "<p>Nombre de Commandes : ".count($clients) ."</p>";

foreach($clients as $client){
    echo $client->getId() . " - ". $client->getDateCde() . " - " . $client->getNoFacture() . " - ". $client->getIdClient() . "<br>";
}
include_once PATH_VIEW . "footer.html";