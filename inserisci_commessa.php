<?php
include "connessione.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $connessione->real_escape_string($_POST['nome']);
    $intestatario = $connessione->real_escape_string($_POST['intestatario']);
    $descrizione = $connessione->real_escape_string($_POST['descrizione']);
    $referente_id = intval($_POST['referente_id']);

    $query = "INSERT INTO COMMESSA (nome, INTESTATARIO, DESCRIZIONE, REFERENTE_ID)
              VALUES ('$nome', '$intestatario', '$descrizione', $referente_id)";

    if ($connessione->query($query) === TRUE) {
        header("Location: commesse.php");
    } else {
        echo "Errore durante l'inserimento: " . $connessione->error;
    }
}
?>
