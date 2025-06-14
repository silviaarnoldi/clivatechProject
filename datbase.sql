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
    ID INTEGER PRIMARY KEY AUTO_INCREMENT,
    nome  VARCHAR(255) NOT NULL,
    INTESTATARIO VARCHAR(255),
    DESCRIZIONE TEXT,
    REFERENTE_ID INT,
    FOREIGN KEY (REFERENTE_ID) REFERENCES UTENTE(ID)
);

CREATE TABLE categoria (
    ID INTEGER PRIMARY KEY AUTO_INCREMENT,
    TIPOCATEGORIA VARCHAR(255)
);

CREATE TABLE tipo (
    ID INTEGER PRIMARY KEY AUTO_INCREMENT,
    tipoattivita VARCHAR(255)
);
CREATE TABLE NOMEATTIVITA (
    ID INTEGER PRIMARY KEY AUTO_INCREMENT,
    nomeattivita VARCHAR(255)
);
CREATE TABLE attivita (
  id INT INTEGER PRIMARY KEY AUTO_INCREMENT,
  COMMESSA_ID VARCHAR(255),
  nomeattivita_id INT,
  descrizione TEXT,
  categoria_id INT,
  tipoattivita_id INT,
  durata INT,
  data_inizio DATE,
  data_fine DATE,
  referente INT,
  COLLABORATORI TEXT,
  PERCENTUALE INT,
  FOREIGN KEY (COMMESSA_ID) REFERENCES COMMESSA(ID),
    FOREIGN KEY (nomeattivita_id) REFERENCES NOMEATTIVITA(ID),
    FOREIGN KEY (tipoattivita_id) REFERENCES tipo(ID),
  FOREIGN KEY (categoria_id) REFERENCES categoria(ID),
  FOREIGN KEY (referente) REFERENCES UTENTE(ID)
);

-- Inserisci prima UTENTI
insert into UTENTE (USERNAME, PASSWORD, NOME, COGNOME, INIZIALI) 
values ('project2', '21232f297a57a5a743894a0e4a801fc3', 'Luca', 'Campanardi', 'LC');
insert into UTENTE (USERNAME, PASSWORD, NOME, COGNOME, INIZIALI) 
values ('project4', '21232f297a57a5a743894a0e4a801fc3', 'Massimo', 'Donadoni', 'MD');
insert into UTENTE (USERNAME, PASSWORD, NOME, COGNOME, INIZIALI)
values ('project3', '21232f297a57a5a743894a0e4a801fc3', 'Andrea', 'Regonesi', 'AR');
insert into UTENTE (USERNAME, PASSWORD, NOME, COGNOME, INIZIALI)
values ('project6', '21232f297a57a5a743894a0e4a801fc3', 'Mauro', 'Rinaldi', 'MR');
insert into UTENTE (USERNAME, PASSWORD, NOME, COGNOME, INIZIALI)
values ('project9', '21232f297a57a5a743894a0e4a801fc3', 'Michael', 'Minuti', 'MM');
insert into UTENTE (USERNAME, PASSWORD, NOME, COGNOME, INIZIALI)
values ('project5', '21232f297a57a5a743894a0e4a801fc3', 'Marco', 'Merighi', 'MM');
insert into UTENTE (USERNAME, PASSWORD, NOME, COGNOME, INIZIALI)
values ('project7', '21232f297a57a5a743894a0e4a801fc3', 'Alessandro', 'Peverelli', 'AP');


-- Poi le CATEGORIE
insert into categoria (TIPOCATEGORIA) values ('Impianti');
insert into categoria (TIPOCATEGORIA) values ('Quadri');
insert into categoria (TIPOCATEGORIA) values ('Automazione');
-- Poi i TIPI
insert into tipo (tipoattivita) values ('Previsione');
insert into tipo (tipoattivita) values ('Vincolante');
insert into tipo (tipoattivita) values ('Consuntivo');
insert into tipo (tipoattivita) values ('Ripianificcata');
-- Poi i NOMEATTIVITA'
insert into nomeattivita (nomeattivita) values ('Inizio Progetto');
insert into nomeattivita (nomeattivita) values ('Raccolta Informazioni e Documenti Cliente');
insert into nomeattivita (nomeattivita) values ('Analisi Documentazione Cliente');
insert into nomeattivita (nomeattivita) values ('Verifica / Riesame');
insert into nomeattivita (nomeattivita) values ('Approvazione Cliente');
insert into nomeattivita (nomeattivita) values ('Documentazione Finale');
insert into nomeattivita (nomeattivita) values ('Validazione');
insert into nomeattivita (nomeattivita) values ('Sviluppo Disegni Quadri Elettrici');
insert into nomeattivita (nomeattivita) values ('Sviluppo Planimetrie');
insert into nomeattivita (nomeattivita) values ('Dimensionamento Linee in Cavo');
insert into nomeattivita (nomeattivita) values ('Calcoli Illuminotecnici');
insert into nomeattivita (nomeattivita) values ('Verifica Circuiti a Sicurezza Intrinseca');
insert into nomeattivita (nomeattivita) values ('Tabelle Cavi');
insert into nomeattivita (nomeattivita) values ('Architettura di Rete');
insert into nomeattivita (nomeattivita) values ('Sviluppo I/O List');
insert into nomeattivita (nomeattivita) values ('Sviluppo Specifiche Funzionali');
insert into nomeattivita (nomeattivita) values ('Sviluppo Hardware Design Specification');
insert into nomeattivita (nomeattivita) values ('Sviluppo Software Design Specification');
insert into nomeattivita (nomeattivita) values ('Sviluppo Data Management System');
insert into nomeattivita (nomeattivita) values ('Sviluppo Protocolli di IQ / OQ');


