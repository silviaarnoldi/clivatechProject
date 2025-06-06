<?php
include "connessione.php";
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nome = $connessione->real_escape_string($_POST['nome']);
    $intestatario = $connessione->real_escape_string($_POST['intestatario']);
    $descrizione = $connessione->real_escape_string($_POST['descrizione']);

    $query = "
        UPDATE COMMESSA 
        SET 
            NOME = '$nome',
            INTESTATARIO = '$intestatario',
            DESCRIZIONE = '$descrizione'
        WHERE ID = $id
    ";

    if ($connessione->query($query) === TRUE) {
        header("Location: commesse.php");
    } else {
        die("Errore nella modifica: " . $connessione->error);
    }
} else {
    header("Location: commesse.php");
}
