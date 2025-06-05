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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID attività non valido");
}

$id = intval($_GET['id']);

$query = "
SELECT 
    attività.ID,
    attività.nomeattività_id,
    attività.durata,
    attività.data_inizio,
    attività.data_fine,
    attività.referente,
    attività.collaboratori,
    attività.PERCENTUALE,
    attività.categoria_id,
    attività.tipoattività_id
FROM attività
WHERE attività.ID = $id
LIMIT 1";

$result = $connessione->query($query);

if (!$result || $result->num_rows === 0) {
    die("Attività non trovata");
}

$attivita = $result->fetch_assoc();

// Preleva dati per select (come in index)
$tipi = $connessione->query("SELECT * FROM tipo ORDER BY ID");
$nomi_attivita = $connessione->query("SELECT * FROM nomeattività ORDER BY nomeattività");
$categorie = $connessione->query("SELECT * FROM categoria ORDER BY TIPOCATEGORIA");
$utenti = $connessione->query("SELECT * FROM UTENTE ORDER BY COGNOME, NOME");

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Attività</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Modifica Attività</h1>
    <form action="modifica_controller.php" method="POST" class="row g-3">
        <input type="hidden" name="id" value="<?= htmlspecialchars($attivita['ID']) ?>">
        
        <div class="col-12">
            <label for="nomeattivita" class="form-label">Nome Attività</label>
            <select name="nomeattivita" id="nomeattivita" class="form-select" required>
                <option value="">— Seleziona Nome Attività —</option>
                <?php while ($na = $nomi_attivita->fetch_assoc()): ?>
                    <option value="<?= $na['ID'] ?>" <?= ($na['ID'] == $attivita['nomeattività_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($na['nomeattività']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-12">
            <label for="categoria" class="form-label">Categoria</label>
            <select name="categoria" id="categoria" class="form-select" required>
                <option value="">— Seleziona Categoria —</option>
                <?php while ($cat = $categorie->fetch_assoc()): ?>
                    <option value="<?= $cat['ID'] ?>" <?= ($cat['ID'] == $attivita['categoria_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['TIPOCATEGORIA']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-12">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-select" required>
                <option value="">— Seleziona Tipo —</option>
                <?php while ($tipo = $tipi->fetch_assoc()): ?>
                    <option value="<?= $tipo['ID'] ?>" <?= ($tipo['ID'] == $attivita['tipoattività_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tipo['tipoattività']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-6">
            <label for="durata" class="form-label">Durata (giorni)</label>
            <input type="number" name="durata" id="durata" class="form-control" required value="<?= htmlspecialchars($attivita['durata']) ?>">
        </div>

        <div class="col-6">
            <label for="percentuale" class="form-label">Completamento (%)</label>
            <input type="number" name="percentuale" id="percentuale" class="form-control" required value="<?= htmlspecialchars($attivita['PERCENTUALE']) ?>">
        </div>

        <div class="col-6">
            <label for="data_inizio" class="form-label">Data Inizio</label>
            <input type="date" name="data_inizio" id="data_inizio" class="form-control" required value="<?= htmlspecialchars($attivita['data_inizio']) ?>">
        </div>

        <div class="col-6">
            <label for="data_fine" class="form-label">Data Fine</label>
            <input type="date" name="data_fine" id="data_fine" class="form-control" required value="<?= htmlspecialchars($attivita['data_fine']) ?>">
        </div>

        <div class="col-12">
            <label for="referente" class="form-label">Referente</label>
            <select name="referente" id="referente" class="form-select" required>
                <option value="">— Seleziona Referente —</option>
                <?php while ($u = $utenti->fetch_assoc()): ?>
                    <option value="<?= $u['ID'] ?>" <?= ($u['ID'] == $attivita['referente']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['NOME'] . ' ' . $u['COGNOME']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-12">
            <label for="collaboratori" class="form-label">Collaboratori</label>
            <input type="text" name="collaboratori" id="collaboratori" class="form-control" placeholder="Separati da virgola" value="<?= htmlspecialchars($attivita['collaboratori']) ?>">
        </div>

        <div class="col-12 d-grid">
            <button type="submit" class="btn btn-warning">Salva Modifiche</button>
        </div>
    </form>
    <a href="home.php" class="btn btn-secondary mt-3">Annulla</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
