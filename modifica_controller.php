<?php
include "connessione.php";
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

if (!$connessione) {
    die("Connessione fallita: " . mysqli_connect_error());
}

// Validazione base dati POST
$id = $_POST['id'] ?? null;
$nomeattivita = $_POST['nomeattivita'] ?? null;
$categoria = $_POST['categoria'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$durata = $_POST['durata'] ?? null;
$percentuale = $_POST['percentuale'] ?? null;
$data_inizio = $_POST['data_inizio'] ?? null;
$data_fine = $_POST['data_fine'] ?? null;
$referente = $_POST['referente'] ?? null;
$collaboratori = $_POST['collaboratori'] ?? '';

if (!$id || !is_numeric($id)) {
    die("ID attività non valido");
}

if (
    !$nomeattivita || !$categoria || !$tipo || !$durata || !$percentuale ||
    !$data_inizio || !$data_fine || !$referente
) {
    die("Campi obbligatori mancanti");
}

// Sanitizza dati numerici
$id = intval($id);
$nomeattivita = intval($nomeattivita);
$categoria = intval($categoria);
$tipo = intval($tipo);
$durata = intval($durata);
$percentuale = intval($percentuale);
$referente = intval($referente);

// Sanitize stringhe e date
$collaboratori = $connessione->real_escape_string($collaboratori);
$data_inizio = $connessione->real_escape_string($data_inizio);
$data_fine = $connessione->real_escape_string($data_fine);

// Controllo minimo su date (data_fine >= data_inizio)
if (strtotime($data_fine) < strtotime($data_inizio)) {
    die("La data di fine non può essere precedente alla data di inizio.");
}

// Query update
$sql = "
UPDATE attività SET
    nomeattività_id = $nomeattivita,
    categoria_id = $categoria,
    tipoattività_id = $tipo,
    durata = $durata,
    PERCENTUALE = $percentuale,
    data_inizio = '$data_inizio',
    data_fine = '$data_fine',
    referente = $referente,
    collaboratori = '$collaboratori'
WHERE ID = $id
LIMIT 1
";

if ($connessione->query($sql) === TRUE) {
    header("Location: home.php");
} else {
    die("Errore nell'aggiornamento: " . $connessione->error);
}
// Chiudi la connessione
$connessione->close();
?>