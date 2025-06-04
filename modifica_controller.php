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
    $categoria = intval($_POST['categoria']);
    $durata = intval($_POST['durata']);
    $percentuale = intval($_POST['percentuale']);
    $data_inizio = $_POST['data_inizio'];
    $data_fine = $_POST['data_fine'];
    $referente = intval($_POST['referente']);
    $collaboratori = $connessione->real_escape_string($_POST['collaboratori']);

    $sql = "UPDATE attività SET
        nome = '$nome',
        categoria_id = $categoria,
        durata = $durata,
        PERCENTUALE = $percentuale,
        data_inizio = '$data_inizio',
        data_fine = '$data_fine',
        referente = $referente,
        collaboratori = '$collaboratori'
        WHERE id = $id";

    if ($connessione->query($sql)) {
        header("Location: home.php?msg=Modifica effettuata");
    } else {
        header("Location: home.php?err=Errore durante la modifica");
    }
} else {
    header("Location: home.php");
}
?>
