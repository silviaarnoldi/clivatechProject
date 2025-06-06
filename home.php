<?php
include "connessione.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php?err=Accesso negato");
    exit;
}

$id_commessa = $_GET['id_commessa'] ?? $_SESSION['id_commessa'] ?? null;
$commessa = null;

if ($id_commessa !== null) {
    $id_commessa_int = intval($id_commessa);
    $result_commessa = $connessione->query("SELECT * FROM COMMESSA WHERE ID = $id_commessa_int LIMIT 1");
    if ($result_commessa && $result_commessa->num_rows > 0) {
    $commessa = $result_commessa->fetch_assoc();
    $_SESSION['id_commessa'] = $commessa['ID'];
    
    // Imposta un cookie che dura 30 giorni
    setcookie('id_commessa', $commessa['ID'], time() + (86400 * 30), "/");
} else {
    die("Commessa non trovata.");
}
}

if (!$connessione) {
    die("Connessione fallita: " . mysqli_connect_error());
}

$tipi = $connessione->query("SELECT * FROM tipo ORDER BY ID");
$nomi_attivita = $connessione->query("SELECT * FROM nomeattività ORDER BY nomeattività");
$categorie = $connessione->query("SELECT * FROM categoria ORDER BY TIPOCATEGORIA");
$utenti = $connessione->query("SELECT * FROM UTENTE WHERE ID = " . intval($_SESSION['id']));
$u = $utenti->fetch_assoc();

$filtro_categoria = $_GET['filtro_categoria'] ?? '';

$query = "
SELECT 
    attività.ID,
    nomeattività.nomeattività AS nome_attivita,
    attività.commessa_id,
    attività.durata,
    attività.data_inizio,
    attività.data_fine,
    attività.referente,
    attività.collaboratori,
    attività.PERCENTUALE,
    attività.descrizione,
    categoria.TIPOCATEGORIA, 
    CONCAT(UTENTE.NOME, ' ', UTENTE.COGNOME) AS referente_nome,
    tipo.ID as tipo_id,
    tipo.tipoattività as tipo_nome
FROM attività
    JOIN categoria ON attività.categoria_id = categoria.ID
    JOIN UTENTE ON attività.referente = UTENTE.ID
    LEFT JOIN tipo ON attività.tipoattività_id = tipo.ID
    JOIN nomeattività ON attività.nomeattività_id = nomeattività.ID
";

$whereClauses = [];

if ($id_commessa !== null) {
    $whereClauses[] = "attività.commessa_id = " . intval($id_commessa);
}

if ($filtro_categoria !== '') {
    $whereClauses[] = "categoria.ID = " . intval($filtro_categoria);
}

// Filtro per l'utente loggato come referente
$whereClauses[] = "attività.referente = " . intval($_SESSION['id']);

if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$query .= " ORDER BY attività.data_inizio DESC";

$progetti_query = $connessione->query($query);
$progetti_array = [];
while ($row = $progetti_query->fetch_assoc()) {
    $progetti_array[] = $row;
}

// Calcolo mesi e funzioni calendario
$oggi = new DateTime('now');
$oggi->setDate((int)$oggi->format('Y'), (int)$oggi->format('m'), 1);

function getAllMonths($start, $end) {
    $months = [];
    $current = clone $start;
    $current->modify('first day of this month');
    $end = clone $end;
    $end->modify('first day of next month');

    while ($current < $end) {
        $months[] = clone $current;
        $current->modify('+1 month');
    }
    return $months;
}

function getClosestMonthIndex($months, $today) {
    $minDiff = PHP_INT_MAX;
    $closestIndex = 0;
    foreach ($months as $i => $month) {
        $diff = abs($month->getTimestamp() - $today->getTimestamp());
        if ($diff < $minDiff) {
            $minDiff = $diff;
            $closestIndex = $i;
        }
    }
    return $closestIndex;
}

$minDate = null;
$maxDate = null;
foreach ($progetti_array as $row) {
    $inizio = new DateTime($row['data_inizio']);
    $fine = new DateTime($row['data_fine']);
    if (!$minDate || $inizio < $minDate) $minDate = $inizio;
    if (!$maxDate || $fine > $maxDate) $maxDate = $fine;
}

if ($minDate === null || $maxDate === null) {
    echo "Nessuna attività trovata.";
    $mesiTotali = [];
    $startIndex = null;
} else {
    $mesiTotali = getAllMonths($minDate, $maxDate);
    $oggi = new DateTime();
    $startIndex = getClosestMonthIndex($mesiTotali, $oggi);
}

$mesiOrdinati = array_merge(
    array_slice($mesiTotali, $startIndex),
    array_slice($mesiTotali, 0, $startIndex)
);

$mesiOrdinati = array_slice($mesiOrdinati, 0, 7);
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
    .tipo-previsione { background-color: #a3d5ff; border-color: #3399ff; }  /* azzurro */
    .tipo-vincolante { background-color: #fff3b0; border-color: #f7d948; }  /* giallo */
    .tipo-consuntivo { background-color: #ff8a8a; border-color: #d73a3a; }  /* rosso */
    .tipo-ripianificata { background-color: #c39bd3; border-color: #7e57c2; } /* viola */

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
  <a href="commesse.php" class="btn btn-secondary">← Torna alle Commesse</a>
</div>

<div class="mb-4">
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

            <div class="col-12">
              <input type="hidden" id="numero_commessa" name="custId" value="<?php 
$id_commessa = $_GET['id_commessa'] 
    ?? $_SESSION['id_commessa'] 
    ?? $_COOKIE['id_commessa'] 
    ?? null;

if ($id_commessa === null) {
    die("Commessa non specificata.");
} ?>">
            </div>
              
              <div class="col-12">
              <label for="nome_attivita">Nome Attività</label>
              <select name="nomeattivita" id="nome_attivita" class="form-select" required>
                <option value="">— Seleziona Nome Attività —</option>
                <?php while ($attivita = $nomi_attivita->fetch_assoc()): ?>
                  <option value="<?= $attivita['ID'] ?>"><?= htmlspecialchars($attivita['nomeattività']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="mb-3" style="display: flex; align-items: center;">
            <input type="checkbox" id="noDescrizione" name="noDescrizione" onchange="toggleDescrizione()" style="margin-right: 10px; width: 18px; height: 18px;" />
            <label for="noDescrizione" style="margin: 0; font-weight: 500;">
              Nessuna Descrizione
            </label>
          </div>
          <div class="col-12">
            <input type="text" id="descrizione" name="descrizione" class="form-control" placeholder="Descrizione">
          </div>
            <div class="col-12">
              <label for="categoria">Categoria</label>
              <select name="categoria" id="categoria" class="form-select" required>
                <option value="">— Seleziona Categoria —</option>
                <?php
                // Riporto il puntatore a inizio per riutilizzare $categorie
                $categorie->data_seek(0);
                while ($cat = $categorie->fetch_assoc()): ?>
                  <option value="<?= $cat['ID'] ?>"><?= htmlspecialchars($cat['TIPOCATEGORIA']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-12">
              <label for="tipo">Tipo</label>
              <select name="tipo" id="tipo" class="form-select" required>
                <option value="">— Seleziona Tipo —</option>
                <?php
                $tipi->data_seek(0);
                while ($tipo = $tipi->fetch_assoc()): ?>
                  <option value="<?= $tipo['ID'] ?>"><?= htmlspecialchars($tipo['tipoattività']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-6">
              <label for="durata">Durata</label>
              <input type="number" name="durata" class="form-control" placeholder="Durata (giorni)" required>
            </div>
            <div class="col-6">
              <label for="data_inizio">Inizio</label>
              <input type="date" name="data_inizio" class="form-control" required>
            </div>
            <div class="col-12">
               <input type="hidden" name="referente" value="<?= intval($u['ID']) ?>">
               <input type="hidden" name="commessa_id" value="<?= intval($id_commessa) ?>"> <!-- Aggiunto per passare l'ID della commessa -->
               <p><strong>Referente:</strong> <?= htmlspecialchars($u['NOME'] . ' ' . $u['COGNOME']) ?></p>
            </div>
            <div class="mb-3" style="display: flex; align-items: center;">
              <input type="checkbox" id="noCollaboratori" name="noCollaboratori" onchange="toggleCollaboratori()" style="margin-right: 10px; width: 18px; height: 18px;" />
              <label for="noCollaboratori" style="margin: 0; font-weight: 500;">
                Nessun Collaboratore
              </label>
            </div>
            <div class="col-12">
              <input type="text" id="collaboratori" name="collaboratori" class="form-control" placeholder="Collaboratori (separati da virgola)">
            </div>
            <div class="col-12 d-grid"><button type="submit" class="btn btn-primary">Inserisci</button></div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- FILTRO CATEGORIA -->
  <?php
  // Riporto puntatore $categorie a inizio per riutilizzo
  $categorie->data_seek(0);
  ?>
  <form method="GET" class="mb-4 d-flex align-items-center gap-3">
    <label for="filtro_categoria" class="mb-0">Filtra per Settore:</label>
    <select name="filtro_categoria" id="filtro_categoria" class="form-select" style="width: 250px;">
      <option value="">— Tutti i settori —</option>
      <?php while ($cat = $categorie->fetch_assoc()): ?>
        <option value="<?= $cat['ID'] ?>" <?= ($filtro_categoria == $cat['ID']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['TIPOCATEGORIA']) ?>
        </option>
      <?php endwhile; ?>
    </select>
    <button type="submit" class="btn btn-primary">Applica</button>
  </form>

  <!-- TABELLA -->
  <div class="table-area">
    <h4 class="mb-3">📋 Elenco Attività</h4>
    <table class="table table-striped table-bordered bg-white shadow-sm w-100" style="min-width: 1500px;">
      <thead class="table-light">
        <tr>
          <th>Nome</th>
          <th>Descrizione</th>
          <th>Durata</th>
          <th>Inizio</th>
          <th>Fine</th>
          <th>Referente</th>
          <th>Collaboratori</th>
          <th>Completamento</th>
          <th>Modifica</th>
          <th>Elimina</th>
          <th colspan="<?= count($mesiOrdinati) ?>" style="text-align:center;">Calendario (<?= count($mesiOrdinati) ?> mesi)</th>
        </tr>
        <!-- Riga mesi calendario -->
        <tr>
          <th colspan="10"></th> <!-- Vuoto per colonne non calendario -->
          <?php foreach ($mesiOrdinati as $mese): ?>
            <th style="text-align:center;"><?= ucfirst(strftime('%B %Y', $mese->getTimestamp())) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($progetti_array as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['nome_attivita']) ?></td>
            <td><?= htmlspecialchars($row['descrizione'] ?? 'Nessuna descrizione') ?></td>
            <td><?= $row['durata'] ?> g</td>
            <td><?= $row['data_inizio'] ?></td>
            <td><?= $row['data_fine'] ?></td>
            <td><?= htmlspecialchars($row['referente_nome']) ?></td>
            <td><?= htmlspecialchars($row['collaboratori'] ?? 'Nessuno') ?></td>
            <td><?= htmlspecialchars($row['PERCENTUALE']) ?>%</td>
            <td><a href="modifica.php?id=<?= intval($row['ID']) ?>" class="btn btn-warning btn-sm">Modifica</a></td>
            <td><a href="elimina.php?id=<?= intval($row['ID']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro di voler eliminare questa attività?');">Elimina</a></td>

            <?php 
            $tmpDate = clone $mesiOrdinati[0];
            for ($m=0; $m < count($mesiOrdinati); $m++):
              $daysInMonth = (int)$tmpDate->format('t');
              $firstDayOfWeek = (int)$tmpDate->format('N');
                            echo '<td><div style="display:flex; gap:2px; flex-wrap: nowrap;">';

              // Spazi vuoti prima del primo giorno
              for ($i=1; $i < $firstDayOfWeek; $i++) {
                echo '<div class="day-box day-inactive" style="background:transparent; border:none; width:18px; height:18px;"></div>';
              }

              // Giorni mese con numeri
              for ($day=1; $day <= $daysInMonth; $day++) {
                $currentDay = new DateTime($tmpDate->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT));
                $currentDay->setTime(0,0,0);

                $startAct = new DateTime($row['data_inizio']);
                $startAct->setTime(0,0,0);

                $endAct = new DateTime($row['data_fine']);
                $endAct->setTime(0,0,0);

                if ($currentDay >= $startAct && $currentDay <= $endAct) {
                  switch ($row['tipo_id']) {
                    case 1: $class = 'day-box tipo-previsione'; break;
                    case 2: $class = 'day-box tipo-vincolante'; break;
                    case 3: $class = 'day-box tipo-consuntivo'; break;
                    case 4: $class = 'day-box tipo-ripianificata'; break;
                    default: $class = 'day-box day-active';
                  }
                } else {
                  $class = 'day-box day-inactive';
                }
                echo '<div title="' . $currentDay->format('d-m-Y') . '" class="' . $class . '" style="width:18px; height:18px; font-size:10px; text-align:center; line-height:18px;">' . $day . '</div>';
              }

              echo '</div></td>';
              $tmpDate->modify('+1 month');
            endfor;
            ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<script>
function toggleDescrizione() {
  const checkbox = document.getElementById('noDescrizione');
  const descrizione = document.getElementById('descrizione');
  if (checkbox.checked) {
    descrizione.value = '';
    descrizione.disabled = true;
  } else {
    descrizione.disabled = false;
  }
}

function toggleCollaboratori() {
  const checkbox = document.getElementById('noCollaboratori');
  const collaboratori = document.getElementById('collaboratori');
  if (checkbox.checked) {
    collaboratori.value = '';
    collaboratori.disabled = true;
  } else {
    collaboratori.disabled = false;
  }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
