<?php
include "connessione.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

$id = intval($_GET['id'] ?? 0);

$query = $connessione->query("SELECT * FROM attività WHERE ID = $id");
if (!$query) {
    die("Errore query attività: " . $connessione->error);
}
$attivita = $query->fetch_assoc();

if (!$attivita) {
    die("Attività non trovata.");
}

$nomi_attivita = $connessione->query("SELECT * FROM nomeattività ORDER BY nomeattività");
if (!$nomi_attivita) {
    die("Errore query nomeattività: " . $connessione->error);
}
$categorie = $connessione->query("SELECT * FROM categoria ORDER BY TIPOCATEGORIA");
if (!$categorie) {
    die("Errore query categoria: " . $connessione->error);
}
$tipi = $connessione->query("SELECT * FROM tipo ORDER BY ID");
if (!$tipi) {
    die("Errore query tipo: " . $connessione->error);
}
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
    <h1 class="mb-4">✏️ Modifica Attività</h1>
    <form action="modifica_controller.php" method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <div class="col-12">
            <label>Nome Attività</label>
            <select name="nomeattivita" class="form-select" required>
                <?php while ($r = $nomi_attivita->fetch_assoc()): ?>
                    <option value="<?= $r['ID'] ?>" <?= isset($attivita['nomeattività_id']) && $r['ID'] == $attivita['nomeattività_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nomeattività']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <?php if (!empty($attivita['descrizione'])): ?>
        <div class="col-12">
            <label>Descrizione</label>
            <input type="text" name="descrizione" class="form-control" value="<?= htmlspecialchars($attivita['descrizione']) ?>">
        </div>
        <?php endif; ?>

        <div class="col-12">
            <label>Categoria</label>
            <select name="categoria" class="form-select" required>
                <?php while ($c = $categorie->fetch_assoc()): ?>
                    <option value="<?= $c['ID'] ?>" <?= isset($attivita['categoria_id']) && $c['ID'] == $attivita['categoria_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['TIPOCATEGORIA']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-12">
            <label>Tipo</label>
            <select name="tipo" class="form-select" required>
                <?php while ($t = $tipi->fetch_assoc()): ?>
                    <option value="<?= $t['ID'] ?>" <?= isset($attivita['tipoattività_id']) && $t['ID'] == $attivita['tipoattività_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['tipoattività']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label>Durata</label>
            <input type="number" name="durata" class="form-control" value="<?= htmlspecialchars($attivita['durata']) ?>" required>
        </div>
        <div class="col-md-6">
            <label>Percentuale</label>
            <input type="number" name="percentuale" class="form-control" value="<?= htmlspecialchars($attivita['PERCENTUALE']) ?>" required>
        </div>

        <div class="col-md-6">
            <label>Data Inizio</label>
            <input type="date" name="data_inizio" class="form-control" value="<?= htmlspecialchars($attivita['data_inizio']) ?>" required>
        </div>
        <br>

        <?php if (!empty($attivita['collaboratori'])): ?>
        <div class="col-12">
            <label>Collaboratori</label>
            <input type="text" name="collaboratori" class="form-control" value="<?= htmlspecialchars($attivita['collaboratori']) ?>">
        </div>
        <?php endif; ?>

        <div class="col-12 d-grid">
            <button type="submit" class="btn btn-primary">💾 Salva modifiche</button>
        </div>
    </form>

    <div class="mt-3">
        <a href="home.php" class="btn btn-secondary">← Torna</a>
    </div>
</div>
</body>
</html>
