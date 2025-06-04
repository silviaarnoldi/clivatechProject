<?php
$connessione= new mysqli('localhost','root','','progetto_db'); 
if($connessione->connect_error){
    die("Connessione fallita: " . $connessione->connect_error);
    exit();
}
?>