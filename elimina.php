<?php
include "connessione.php";
session_start();

// Verifica sessione attiva
if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

// Verifica presenza ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID attività non valido.");
}

$id = intval($_GET['id']);

// Verifica connessione al DB
if (!$connessione) {
    die("Connessione fallita: " . mysqli_connect_error());
}

// Elimina attività
$stmt = $connessione->prepare("DELETE FROM attività WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: home.php");
} else {
    echo "Errore durante l'eliminazione: " . $connessione->error;
}

$stmt->close();
$connessione->close();
?>
