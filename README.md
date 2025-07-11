# clivatechProject
docker run --name myXampp -p 41061:22 -p 41062:80 -d -v /workspaces/clivatechProject:/www tomsik68/xampp:8


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

$categorie = $connessione->query("SELECT * FROM categoria ORDER BY TIPOCATEGORIA");
$utenti = $connessione->query("SELECT * FROM UTENTE ORDER BY COGNOME, NOME");

$progetti_query = $connessione->query("
    SELECT 
        attività.*, 
        categoria.TIPOCATEGORIA, 
        CONCAT(UTENTE.NOME, ' ', UTENTE.COGNOME) AS referente_nome
    FROM attività
    JOIN categoria ON attività.categoria_id = categoria.ID
    JOIN UTENTE ON attività.referente = UTENTE.ID
    ORDER BY data_inizio DESC
");

$progetti_array = [];
while ($row = $progetti_query->fetch_assoc()) {
    $progetti_array[] = $row;
}

$oggi = new DateTime('now');
$oggi->setDate((int)$oggi->format('Y'), (int)$oggi->format('m'), 1);
$mese_inizio = (clone $oggi)->modify('-1 month');
$mese_fine = (clone $mese_inizio)->modify('+6 month');
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Gestione Attività</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .container-flex {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
    }
    .form-area {
      background-color: #f8f9fa;
      border-left: 5px solid #0d6efd;
      padding: 2rem;
      border-radius: 0.5rem;
    }
    .table-area {
      overflow-x: auto;
      width: 100%;
    }
    td.calendar-cell {
      white-space: nowrap;
      padding: 0.5rem;
    }
    table.calendar {
      border-collapse: collapse;
      font-size: 14px;
      user-select: none;
      width: max-content;
    }
    table.calendar th {
      padding: 5px;
      border: 1px solid #ccc;
      background: #ddd;
      min-width: 160px;
      text-align: center;
    }
    table.calendar th.weekdays {
      background: #eee;
      padding: 3px 2px;
    }
    table.calendar td {
      padding: 0;
      border: 1px solid #ccc;
      vertical-align: top;
      min-width: 160px;
    }
    .day-box {
      width: 22px;
      height: 22px;
      text-align: center;
      font-size: 13px;
      line-height: 22px;
      border-radius: 3px;
      border: 1px solid #ddd;
      margin-right: 2px;
      display: inline-block;
      user-select: none;
      cursor: default;
    }
    .day-active {
      background-color: #a3d5ff;
      border-color: #3399ff;
    }
    .day-inactive {
      background-color: #f0f0f0;
      border-color: #ddd;
    }
    .modal-header {
      background-color: #0d6efd;
      color: white;
    }
    @media (max-width: 768px) {
      .container-flex {
        flex-direction: column;
      }
      .table-area {
        max-width: 100%;
        overflow-x: auto;
      }
    }
  </style>
</head>
<body class="bg-light">
<div class="container py-5">

  <div class="d-flex align-items-center mb-4">
    <h1 class="me-auto">📁 Gestione Attività</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuovaAttivita">+ Nuova Attività</button>
  </div>

  <!-- MODALE -->
  <div class="modal fade" id="modalNuovaAttivita" tabindex="-1" aria-labelledby="modalNuovaAttivitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNuovaAttivitaLabel">➕ Nuova Attività</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <form action="inserimento_controller.php" method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-12"><input type="text" name="nome" class="form-control" placeholder="Nome Attività" required></div>
            <div class="col-12">
              <label for="categoria">Categoria</label>
              <select name="categoria" id="categoria" class="form-select" required>
                <option value="">— Seleziona Categoria —</option>
                <?php while ($cat = $categorie->fetch_assoc()): ?>
                  <option value="<?= $cat['ID'] ?>"><?= htmlspecialchars($cat['TIPOCATEGORIA']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-6"><input type="number" name="durata" class="form-control" placeholder="Durata (giorni)" required></div>
            <div class="col-6"><input type="number" name="percentuale" class="form-control" placeholder="Completamento (%)" required></div>
            <div class="col-6">
              <label for="data_inizio">Inizio</label>
              <input type="date" name="data_inizio" class="form-control" required>
            </div>
            <div class="col-6">
              <label for="data_fine">Fine</label>
              <input type="date" name="data_fine" class="form-control" required>
            </div>
            <div class="col-12">
              <label for="referente">Referente</label>
              <select name="referente" id="referente" class="form-select" required>
                <option value="">— Seleziona Referente —</option>
                <?php $utenti->data_seek(0); while ($u = $utenti->fetch_assoc()): ?>
                  <option value="<?= $u['ID'] ?>"><?= htmlspecialchars($u['NOME'] . ' ' . $u['COGNOME']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-12"><input type="text" name="collaboratori" class="form-control" placeholder="Collaboratori (separati da virgola)"></div>
            <div class="col-12 d-grid"><button type="submit" class="btn btn-primary">Inserisci</button></div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- TABELLA -->
  <div class="table-area">
    <h4 class="mb-3">📋 Elenco Attività</h4>
    <table class="table table-striped table-bordered bg-white shadow-sm w-100" style="min-width: 1500px;">
      <thead class="table-light">
        <tr>
          <th>Nome</th>
          <th>Durata</th>
          <th>Inizio</th>
          <th>Fine</th>
          <th>Referente</th>
          <th>Completamento</th>
          <th>Modifica</th>
          <th>Elimina</th>
          <th>Calendario (mese prec. + 6 succ.)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($progetti_array as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['nome']) ?></td>
            <td><?= $row['durata'] ?> gg</td>
            <td><?= $row['data_inizio'] ?></td>
            <td><?= $row['data_fine'] ?></td>
            <td><?= htmlspecialchars($row['referente_nome']) ?></td>
            <td><?= $row['PERCENTUALE'] ?>%</td>
            <td><a href="modifica.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Modifica</a></td>
            <td>
              <form action="elimina.php" method="POST" class="d-inline">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Elimina</button>
              </form>
            </td>
            <td class="calendar-cell">
              <table class="calendar">
                <thead>
                  <tr>
                    <?php $tmpDate = clone $mese_inizio; for ($m=0; $m < 7; $m++): ?>
                      <th><?= ucfirst(strftime('%B %Y', $tmpDate->getTimestamp())) ?></th>
                      <?php $tmpDate->modify('+1 month'); endfor; ?>
                  </tr>
                  <tr>
                    <?php $weekdays = ['L', 'M', 'M', 'G', 'V', 'S', 'D']; for ($m=0; $m < 7; $m++): ?>
                      <th class="weekdays"><?= implode(' ', $weekdays) ?></th>
                    <?php endfor; ?>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <?php
                      $tmpDate = clone $mese_inizio;
                      for ($m=0; $m < 7; $m++) {
                        $daysInMonth = (int)$tmpDate->format('t');
                        $firstDayOfWeek = (int)$tmpDate->format('N');
                        echo '<td><div style="display:flex; gap:2px; flex-wrap: nowrap;">';
                        for ($i=1; $i < $firstDayOfWeek; $i++) {
                          echo '<div class="day-box day-inactive" style="background:transparent; border:none;"></div>';
                        }
                        for ($day=1; $day <= $daysInMonth; $day++) {
                          $currentDay = new DateTime($tmpDate->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT));
                          $startAct = new DateTime($row['data_inizio']);
                          $endAct = new DateTime($row['data_fine']);
                          $class = ($currentDay >= $startAct && $currentDay <= $endAct) ? 'day-active' : 'day-inactive';
                          echo '<div title="' . $currentDay->format('d-m-Y') . '" class="day-box ' . $class . '">' . $day . '</div>';
                        }
                        echo '</div></td>';
                        $tmpDate->modify('+1 month');
                      }
                    ?>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
