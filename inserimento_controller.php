<?php
include "connessione.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupero e sanitizzazione dati
    $nome = trim($_POST['nome'] ?? '');
    $categoria_id = (int)($_POST['categoria'] ?? 0);
    $durata = (int)($_POST['durata'] ?? 0);
    $percentuale = (int)($_POST['percentuale'] ?? 0);
    $data_inizio = $_POST['data_inizio'] ?? '';
    $dataObj = DateTime::createFromFormat('Y-m-d', $data_inizio);
    if ($dataObj === false) {
        $errors[] = "Formato data_inizio non valido.";
    } else {
        $dataObj->modify('+' . ($durata - 1) . ' days');
        $data_fine = $dataObj->format('Y-m-d');
    }

    $referente = (int)($_POST['referente'] ?? 0);
    $collaboratori = trim($_POST['collaboratori'] ?? '');

    $errors = [];

    // Validazioni
    if ($nome === '') $errors[] = "Il nome dell'attività è obbligatorio.";
    if ($categoria_id <= 0) $errors[] = "Categoria non valida.";
    if ($durata <= 0) $errors[] = "Durata non valida.";
    if ($percentuale < 0 || $percentuale > 100) $errors[] = "Percentuale non valida.";
    if (!strtotime($data_inizio)) $errors[] = "Data inizio non valida.";
    if (!strtotime($data_fine)) $errors[] = "Data fine non valida.";
    if ($referente <= 0) $errors[] = "Referente non valido.";

    if (count($errors) > 0) {
        $_SESSION['error'] = implode('<br>', $errors);
        header("Location: index.php");
        exit;
    }

    // Preparazione query con prepared statement
    $stmt = $connessione->prepare("
        INSERT INTO attività 
        (nome, categoria_id, durata, PERCENTUALE, data_inizio, data_fine, referente, collaboratori) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        $_SESSION['error'] = "Errore nella preparazione della query: " . $connessione->error;
        header("Location: index.php");
        exit;
    }

    $stmt->bind_param("siisssis", 
        $nome, 
        $categoria_id, 
        $durata, 
        $percentuale, 
        $data_inizio, 
        $data_fine, 
        $referente, 
        $collaboratori
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Attività inserita con successo.";
    } else {
        $_SESSION['error'] = "Errore nell'inserimento: " . $stmt->error;
    }

    $stmt->close();
    header("Location: home.php");
    exit;
} else {
    // Se non è POST, torno alla pagina principale
    header("Location: index.php");
    exit;
}
$connessione->close();
?>