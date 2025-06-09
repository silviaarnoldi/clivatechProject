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

$id = $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("ID attività non valido");
}
$id = intval($id);

// Recupero dati attuali
$result = $connessione->query("SELECT * FROM attività WHERE ID = $id");
if (!$result || $result->num_rows === 0) {
    die("Attività non trovata.");
}
$attivita = $result->fetch_assoc();

// Recupero dei dati dal POST (se presenti), altrimenti uso quelli già nel DB
$nomeattivita = isset($_POST['nomeattivita']) ? intval($_POST['nomeattivita']) : intval($attivita['nomeattività_id']);
$categoria = isset($_POST['categoria']) ? intval($_POST['categoria']) : intval($attivita['categoria_id']);
$tipo = isset($_POST['tipo']) ? intval($_POST['tipo']) : intval($attivita['tipoattività_id']);
$durata = isset($_POST['durata']) ? intval($_POST['durata']) : intval($attivita['durata']);
$percentuale = isset($_POST['percentuale']) ? intval($_POST['percentuale']) : intval($attivita['PERCENTUALE']);
$data_inizio = !empty($_POST['data_inizio']) ? $connessione->real_escape_string($_POST['data_inizio']) : $attivita['data_inizio'];
$data_fine = !empty($_POST['data_fine']) ? $connessione->real_escape_string($_POST['data_fine']) : $attivita['data_fine'];
$referente = isset($_POST['referente']) ? intval($_POST['referente']) : intval($attivita['referente'] ?? 0);

// Collaboratori: se vuoto o non inviato, salva come NULL
if (isset($_POST['collaboratori']) && trim($_POST['collaboratori']) !== '') {
    $collaboratori = "'" . $connessione->real_escape_string($_POST['collaboratori']) . "'";
} else {
    $collaboratori = "NULL";
}

// Controllo coerenza date se entrambe presenti
if (!empty($data_inizio) && !empty($data_fine) && strtotime($data_fine) < strtotime($data_inizio)) {
    die("La data di fine non può essere precedente alla data di inizio.");
}

// Costruzione query
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
        collaboratori = $collaboratori
    WHERE ID = $id
    LIMIT 1
";

// Esecuzione e chiusura
if ($connessione->query($sql) === TRUE) {
    header("Location: home.php");
    exit;
} else {
    die("Errore nell'aggiornamento: " . $connessione->error);
}

$connessione->close();
?>
