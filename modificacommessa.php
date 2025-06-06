<?php
include "connessione.php";
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID commessa non specificato.");
}

$id_commessa = intval($_GET['id']);
$query = "SELECT * FROM COMMESSA WHERE ID = $id_commessa";
$result = $connessione->query($query);

if ($result->num_rows === 0) {
    die("Commessa non trovata.");
}

$commessa = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Modifica Commessa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h1 class="mb-4">✏️ Modifica Commessa</h1>

  <form action="modificacommessa_controller.php" method="POST" class="row g-3">
    <input type="hidden" name="id" value="<?= htmlspecialchars($commessa['ID']) ?>">
    
    <div class="col-12">
      <label for="nome" class="form-label">Codice Commessa</label>
      <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($commessa['nome']) ?>" required>
    </div>
    <div class="col-12">
      <label for="intestatario" class="form-label">Intestatario</label>
      <input type="text" name="intestatario" id="intestatario" class="form-control" value="<?= htmlspecialchars($commessa['INTESTATARIO']) ?>" required>
    </div>
    <div class="col-12">
      <label for="descrizione" class="form-label">Descrizione</label>
      <textarea name="descrizione" id="descrizione" class="form-control" rows="3"><?= htmlspecialchars($commessa['DESCRIZIONE']) ?></textarea>
    </div>

    <div class="col-12 d-grid">
      <button type="submit" class="btn btn-primary">💾 Salva Modifiche</button>
    </div>
  </form>

  <div class="mt-3">
    <a href="commesse.php" class="btn btn-secondary">🔙 Torna alle Commesse</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
