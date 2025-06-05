-- Crea database e usa
CREATE DATABASE IF NOT EXISTS progetto_db;
USE progetto_db;

-- Tabelle
CREATE TABLE UTENTE (
    ID INTEGER PRIMARY KEY AUTO_INCREMENT,
    USERNAME VARCHAR(255),
    PASSWORD VARCHAR(255),
    NOME VARCHAR(255),
    COGNOME VARCHAR(255),
    INIZIALI VARCHAR(255)
);

CREATE TABLE COMMESSA (
    ID VARCHAR(255) PRIMARY KEY,
    NOME VARCHAR(255),
    DESCRIZIONE TEXT,
    DATA_INIZIO DATE,
    DATA_FINE DATE,
    REFERENTE_ID INT,
    FOREIGN KEY (REFERENTE_ID) REFERENCES UTENTE(ID)
);

CREATE TABLE categoria (
    ID INTEGER PRIMARY KEY AUTO_INCREMENT,
    TIPOCATEGORIA VARCHAR(255)
);

CREATE TABLE tipo (
    ID INTEGER PRIMARY KEY AUTO_INCREMENT,
    tipoattività VARCHAR(255)
);
CREATE TABLE nomeattività (
    ID INTEGER PRIMARY KEY AUTO_INCREMENT,
    nomeattività VARCHAR(255)
);
CREATE TABLE attività (
  id INT AUTO_INCREMENT PRIMARY KEY,
  COMMESSA_ID VARCHAR(255),
  nomeattività_id INT,
  categoria_id INT,
  tipoattività_id INT,
  durata INT,
  data_inizio DATE,
  data_fine DATE,
  referente INT,
  COLLABORATORI TEXT,
  PERCENTUALE INT,
  FOREIGN KEY (COMMESSA_ID) REFERENCES COMMESSA(ID),
    FOREIGN KEY (nomeattività_id) REFERENCES nomeattività(ID),
    FOREIGN KEY (tipoattività_id) REFERENCES tipo(ID),
  FOREIGN KEY (categoria_id) REFERENCES categoria(ID),
  FOREIGN KEY (referente) REFERENCES UTENTE(ID)
);

-- Inserisci prima UTENTI
insert into UTENTE (USERNAME, PASSWORD, NOME, COGNOME, INIZIALI) 
values ('admin', '21232f297a57a5a743894a0e4a801fc3', 'Mario', 'Rossi', 'MR');
insert into UTENTE (USERNAME, PASSWORD, NOME, COGNOME, INIZIALI) 
values ('user', '21232f297a57a5a743894a0e4a801fc3', 'Luca', 'Bianchi', 'LB');

-- Poi le CATEGORIE
insert into categoria (TIPOCATEGORIA) values ('Impianti');
insert into categoria (TIPOCATEGORIA) values ('Quadri');
insert into categoria (TIPOCATEGORIA) values ('Automazione');
-- Poi i TIPI
insert into tipo (tipoattività) values ('Previsione');
insert into tipo (tipoattività) values ('Vincolante');
insert into tipo (tipoattività) values ('Consuntivo');
insert into tipo (tipoattività) values ('Ripianificcata');
-- Poi i NOMEATTIVITA'
insert into nomeattività (nomeattività) values ('Inizio Progetto');
insert into nomeattività (nomeattività) values ('Recupero Informazioni');
insert into nomeattività (nomeattività) values ('Analisi Documentazione cliente');
insert into nomeattività (nomeattività) values ('riesame e verifica documenti cliente');
insert into nomeattività (nomeattività) values ('Sviluppo disegni quadri elettrici');
insert into nomeattività (nomeattività) values ('Riesame e verifica');

insert into nomeattività (nomeattività) values ('VERIFICA / RIESAME DELLA PROGETTAZIONE PARZIALE');
insert into nomeattività (nomeattività) values ('VERIFICA / RIESAME DELLA PROGETTAZIONE');
insert into nomeattività (nomeattività) values ('CALCOLI E DIMENSIONAMENTI');
insert into nomeattività (nomeattività) values ('VERIFICA / RIESAME DELLA PROGETTAZIONE');
insert into nomeattività (nomeattività) values ('SCHEMI QUADRI ELETTRICI');
insert into nomeattività (nomeattività) values ('VERIFICA / RIESAME DELLA PROGETTAZIONE QUADRI P.');
insert into nomeattività (nomeattività) values ('VERIFICA / RIESAME DELLA PROGETTAZIONE QUADRI');
insert into nomeattività (nomeattività) values ('CALCOLI SICUREZZA INTRINSECA');
insert into nomeattività (nomeattività) values ('TABELLE CAVI');
insert into nomeattività (nomeattività) values ('RIUNIONE DI COORDINAMENTO');
insert into nomeattività (nomeattività) values ('DEFINIZIONE P&I + URS REATTORI RI90 E RH91');
insert into nomeattività (nomeattività) values ('INGEGNERIA DI DETTAGLIO RI90 E RH91');
insert into nomeattività (nomeattività) values ('EMISSIONE DOCUMENTI DI PROGETTO');
insert into nomeattività (nomeattività) values ('APPROVAZIONE INGEGNERIA');
insert into nomeattività (nomeattività) values ('SVILUPPO SOFTWARE RI90 E RH91');
insert into nomeattività (nomeattività) values ('FAT RI90 E RH91');
insert into nomeattività (nomeattività) values ('SISTEMAZIONE COMMENTI AL FAT');
insert into nomeattività (nomeattività) values ('SAT RI90 E RH91');
insert into nomeattività (nomeattività) values ('DEFINIZIONE P&I + URS RS92-RI93-RS94 E CH31');
insert into nomeattività (nomeattività) values ('INGEGNERIA DI DETTAGLIO RS92-RI93-RS94 E CH31');
insert into nomeattività (nomeattività) values ('EMISSIONE DOCUMENTI DI PROGETTO');
insert into nomeattività (nomeattività) values ('SVILUPPO SOFTWARE RS92-RI93-RS94 E CH31');
insert into nomeattività (nomeattività) values ('FAT RS92-RI93-RS94 E CH31');
insert into nomeattività (nomeattività) values ('SISTEMAZIONE COMMENTI AL FAT');
insert into nomeattività (nomeattività) values ('SAT RS92-RI93-RS94 E CH31');
insert into nomeattività (nomeattività) values ('DOCUMENTAZIONE FINALE');
insert into nomeattività (nomeattività) values ('MODIFICHE / VALIDAZIONE');
insert into nomeattività (nomeattività) values ('GESTIONE DELLE MODIFICHE');
insert into nomeattività (nomeattività) values ('VALIDAZIONE');


-- Poi le COMMESSE
insert into COMMESSA (ID, NOME, DESCRIZIONE, DATA_INIZIO, DATA_FINE, REFERENTE_ID) 
values ('C001', 'Progetto A', 'Descrizione del Progetto A', '2023-01-01', '2023-12-31', 1);
insert into COMMESSA (ID, NOME, DESCRIZIONE, DATA_INIZIO, DATA_FINE, REFERENTE_ID) 
values ('C002', 'Progetto B', 'Descrizione del Progetto B', '2023-02-01', '2023-11-30', 2);
