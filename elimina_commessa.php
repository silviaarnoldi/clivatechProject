<?php
include "connessione.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID della commessa non specificato.");
}

$id_commessa = intval($_GET['id']);
$id_utente = $_SESSION['id'];

// Verifica che la commessa appartenga all'utente loggato
$query_check = "SELECT * FROM COMMESSA WHERE ID = $id_commessa AND REFERENTE_ID = $id_utente";
$result_check = $connessione->query($query_check);

if ($result_check->num_rows === 0) {
    die("Commessa non trovata o accesso non autorizzato.");
}

// Esegui l'eliminazione
$query_delete = "DELETE FROM COMMESSA WHERE ID = $id_commessa";

if ($connessione->query($query_delete) === TRUE) {
    header("Location: commesse.php?msg=Commessa eliminata con successo");
    exit;
} else {
    die("Errore durante l'eliminazione: " . $connessione->error);
}
