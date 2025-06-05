<?php
session_start();
include "connessione.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

if (!$connessione) {
    die("Connessione fallita: " . mysqli_connect_error());
}

// Funzione per sanitizzare input
function clean_input($data) {
    return trim(htmlspecialchars($data));
}

// Controllo e sanitizzazione dati POST
$nomeattivita = isset($_POST['nomeattivita']) ? intval($_POST['nomeattivita']) : 0;
$categoria = isset($_POST['categoria']) ? intval($_POST['categoria']) : 0;
$tipo = isset($_POST['tipo']) ? intval($_POST['tipo']) : 0;
$durata = isset($_POST['durata']) ? intval($_POST['durata']) : 0;
$percentuale = isset($_POST['percentuale']) ? intval($_POST['percentuale']) : 0;
$data_inizio = isset($_POST['data_inizio']) ? $_POST['data_inizio'] : '';
$referente = isset($_POST['referente']) ? intval($_POST['referente']) : 0;
$collaboratori = isset($_POST['collaboratori']) ? clean_input($_POST['collaboratori']) : '';

// Validazioni base
$errors = [];

if ($nomeattivita <= 0) $errors[] = "Nome attività non valido.";
if ($categoria <= 0) $errors[] = "Categoria non valida.";
if ($tipo <= 0) $errors[] = "Tipo non valido.";
if ($durata <= 0) $errors[] = "Durata deve essere positiva.";
if ($percentuale < 0 || $percentuale > 100) $errors[] = "Percentuale deve essere tra 0 e 100.";
if (!$data_inizio || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_inizio)) $errors[] = "Data inizio non valida.";
if ($referente <= 0) $errors[] = "Referente non valido.";

// Calcolo data_fine in base a durata
if (empty($errors)) {
    $dataInizioObj = DateTime::createFromFormat('Y-m-d', $data_inizio);
    if (!$dataInizioObj) {
        $errors[] = "Data inizio non valida.";
    } else {
        $dataFineObj = clone $dataInizioObj;
        // durata = numero giorni, fine = inizio + durata - 1
        $dataFineObj->modify('+' . ($durata - 1) . ' days');
        $data_fine = $dataFineObj->format('Y-m-d');
    }
}

if (!empty($errors)) {
    // Puoi gestire gli errori come preferisci, qui semplicemente reindirizzo con errore in GET
    $errMsg = urlencode(implode(' ', $errors));
    header("Location: index.php?err=$errMsg");
    exit;
}

// Prepariamo la query con prepared statements per sicurezza
$stmt = $connessione->prepare("INSERT INTO attività (nomeattività_id, categoria_id, tipoattività_id, durata, PERCENTUALE, data_inizio, data_fine, referente, collaboratori) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Errore preparazione query: " . $connessione->error);
}

$stmt->bind_param(
    "iiiiissis",
    $nomeattivita,
    $categoria,
    $tipo,
    $durata,
    $percentuale,
    $data_inizio,
    $data_fine,
    $referente,
    $collaboratori
);

if ($stmt->execute()) {
    // Inserimento riuscito, redirect alla pagina principale
    header("Location: home.php");
} else {
    // Errore inserimento
    $errMsg = urlencode("Errore inserimento: " . $stmt->error);
    header("Location: home.php?err=$errMsg");
}

$stmt->close();
$connessione->close();
exit;
?>
