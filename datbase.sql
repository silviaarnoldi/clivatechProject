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

CREATE TABLE attività (
  id INT AUTO_INCREMENT PRIMARY KEY,
  COMMESSA_ID VARCHAR(255),
  nome VARCHAR(100),
  categoria_id INT,
  durata INT,
  data_inizio DATE,
  data_fine DATE,
  referente INT,
  COLLABORATORI TEXT,
  PERCENTUALE INT,
  FOREIGN KEY (COMMESSA_ID) REFERENCES COMMESSA(ID),
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

-- Poi le COMMESSE
insert into COMMESSA (ID, NOME, DESCRIZIONE, DATA_INIZIO, DATA_FINE, REFERENTE_ID) 
values ('C001', 'Progetto A', 'Descrizione del Progetto A', '2023-01-01', '2023-12-31', 1);
insert into COMMESSA (ID, NOME, DESCRIZIONE, DATA_INIZIO, DATA_FINE, REFERENTE_ID) 
values ('C002', 'Progetto B', 'Descrizione del Progetto B', '2023-02-01', '2023-11-30', 2);
