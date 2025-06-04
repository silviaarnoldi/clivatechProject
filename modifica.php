<?php
include "connessione.php";
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID attività mancante.");
}

$id = intval($_GET['id']);

$query = "
    SELECT * FROM attività WHERE id = $id
";
$result = $connessione->query($query);
if (!$result || $result->num_rows === 0) {
    die("Attività non trovata.");
}

$attivita = $result->fetch_assoc();

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
  <h1 class="mb-4">✏️ Modifica Attività</h1>
  <form action="modifica_controller.php" method="POST" class="row g-3">
    <input type="hidden" name="id" value="<?= $attivita['id'] ?>">

    <div class="col-12">
      <label>Nome</label>
      <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($attivita['nome']) ?>" required>
    </div>

    <div class="col-6">
      <label>Durata (giorni)</label>
      <input type="number" name="durata" class="form-control" value="<?= $attivita['durata'] ?>" required>
    </div>

    <div class="col-6">
      <label>% Completamento</label>
      <input type="number" name="percentuale" class="form-control" value="<?= $attivita['PERCENTUALE'] ?>" required>
    </div>

    <div class="col-6">
      <label>Data Inizio</label>
      <input type="date" name="data_inizio" class="form-control" value="<?= $attivita['data_inizio'] ?>" required>
    </div>

    <div class="col-6">
      <label>Data Fine</label>
      <input type="date" name="data_fine" class="form-control" value="<?= $attivita['data_fine'] ?>" required>
    </div>

    <div class="col-6">
      <label>Categoria</label>
      <select name="categoria" class="form-select" required>
        <option value="">— Seleziona —</option>
        <?php while ($cat = $categorie->fetch_assoc()): ?>
          <option value="<?= $cat['ID'] ?>" <?= $cat['ID'] == $attivita['categoria_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['TIPOCATEGORIA']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="col-6">
      <label>Referente</label>
      <select name="referente" class="form-select" required>
        <option value="">— Seleziona —</option>
        <?php while ($u = $utenti->fetch_assoc()): ?>
          <option value="<?= $u['ID'] ?>" <?= $u['ID'] == $attivita['referente'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['NOME'] . ' ' . $u['COGNOME']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="col-12">
      <label>Collaboratori (separati da virgola)</label>
      <input type="text" name="collaboratori" class="form-control" value="<?= htmlspecialchars($attivita['collaboratori']) ?>">
    </div>

    <div class="col-12 d-grid">
      <button type="submit" class="btn btn-warning">💾 Salva Modifiche</button>
    </div>
  </form>
</div>
</body>
</html>