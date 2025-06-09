<?php
include "connessione.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

// Prendo i dati POST senza forzare a int dove non serve

$commessa_id = $_GET['id_commessa'] 
    ?? $_SESSION['id_commessa'] 
    ?? $_COOKIE['id_commessa'] 
    ?? null;

if ($commessa_id === null) {
    die("Commessa non specificata.");
}
$nome_attivita = intval($_POST['nomeattivita'] ?? 0);
$categoria = intval($_POST['categoria'] ?? 0);
$tipo = intval($_POST['tipo'] ?? 0);
$durata = intval($_POST['durata'] ?? 0);
$data_inizio = $_POST['data_inizio'] ?? null;
$referente = intval($_POST['referente'] ?? $_SESSION['id']);
$descrizione = isset($_POST['noDescrizione']) ? null : trim($_POST['descrizione'] ?? null);
$collaboratori = isset($_POST['noCollaboratori']) ? null : trim($_POST['collaboratori'] ?? null);

// Controllo dati essenziali
if (!$commessa_id || !$nome_attivita || !$categoria || !$tipo || !$durata || !$data_inizio) {
    die("Dati mancanti o non validi.");
}

// Controllo che la commessa esista (senza prepare e bind_param)
$query = "SELECT ID FROM COMMESSA WHERE ID = '$commessa_id'";
$result = $connessione->query($query);

if (!$result || $result->num_rows === 0) {
    die("Errore: La commessa con ID '$commessa_id' non esiste nel database.");
}

// Calcolo data fine
try {
    $inizio = new DateTime($data_inizio);
    $fine = clone $inizio;
    $fine->modify("+".($durata - 1)." days");
    $data_fine = $fine->format('Y-m-d');
} catch (Exception $e) {
    die("Data di inizio non valida.");
}

// Preparo inserimento
$stmt = $connessione->prepare("
    INSERT INTO attivita 
    (COMMESSA_ID, nomeattivita_id, categoria_id, tipoattivita_id, durata, data_inizio, data_fine, referente, collaboratori, descrizione, PERCENTUALE)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
");

if ($stmt === false) {
    die("Errore nella preparazione della query: " . $connessione->error);
}

// Tipi parametri: s = stringa, i = intero
$stmt->bind_param(
    "siiiisssss",
    $commessa_id,
    $nome_attivita,
    $categoria,
    $tipo,
    $durata,
    $data_inizio,
    $data_fine,
    $referente,
    $collaboratori,
    $descrizione
);

if ($stmt->execute()) {
    header("Location: home.php?id_commessa=" . urlencode($commessa_id));
    exit;
} else {
    die("Errore nell'inserimento: " . $stmt->error);
}
?>
