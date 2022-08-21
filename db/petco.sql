# creazione db
DROP DATABASE IF EXISTS petco;
CREATE DATABASE IF NOT EXISTS petco;

use petco;

# 12) tabella razza

create table razza (
    ID smallint primary key auto_increment,
    nome varchar(30)
);

# 13) tabella categoria
create table categoria (
    ID smallint primary key auto_increment,
    nome varchar(50) not null
);

# 1) tabella cane
create table cane (
    ID smallint primary key auto_increment,
    nome varchar(50) not null unique,
    sesso enum('M', 'F') not null,
    eta smallint not null,
    razza varchar(50) not null,
    # ID_razza smallint not null,
    taglia enum('piccola', 'media', 'grande') not null,
    presentazione text not null,
    chip char(15) not null unique,
    # bit è utilizzato come booleano: se a 1, allora il cane è adottabile a distanza
    distanza bit not null,
    adottato bit not null
    # constraint articolo_razza foreign key (ID_razza) references razza(ID) on update cascade on delete cascade
);

# 2) tabella utente
create table utente (
    ID smallint primary key auto_increment,
    nickname varchar(25) not null unique,
    nome varchar(50) not null,
    cognome varchar(50) not null,
    passwrd varchar(50) not null,
    email varchar(100) not null unique,
    telefono char(10) not null
);

# 3) tabella ugroup
create table ugroup (
    ID smallint primary key auto_increment,
    nome varchar(50),
    descrizione text
);

# 4) tabella service
create table `service` (
    ID smallint primary key auto_increment,
    script varchar(200),
    descrizione text
);

# 5) tabella articolo
create table articolo (
    ID smallint primary key auto_increment,
    ID_utente smallint,
    # default 1 indica la categoria 'senza categoria'
    ID_categoria smallint default 1,
    titolo varchar(100) not null,
    contenuto text not null,
    autore varchar(100) not null,
    `data` date not null,
    categoria varchar(30) not null,
    constraint articolo_utente foreign key (ID_utente) references utente(ID) on update cascade on delete set null,
    constraint articolo_categoria foreign key (ID_categoria) references categoria(ID) on update cascade on delete set NULL
    # NOTA: se una categoria viene eliminata, va cambiato il nome della categoria in 'no categoria'
);

# 6) tabella immagine
create table immagine (
    ID smallint primary key auto_increment,
    ID_cane smallint,
    ID_articolo smallint,
    `path` varchar(400) not null,
    # indice di visualizzazione dell'immagine
    indice smallint not null,
    constraint immagine_cane foreign key (ID_cane) references cane(ID) on update cascade on delete cascade,
    constraint immagine_articolo foreign key (ID_articolo) references articolo(ID) on update cascade on delete cascade
);

# 7) tabella richiesta_info
create table richiesta_info (
    ID smallint primary key auto_increment,
    ID_utente smallint not null,
    `data` date not null,
    chip char(15) not null,
    messaggio text not null,
    constraint info_utente foreign key (ID_utente) references utente(ID) on update cascade on delete cascade
);

# 8) tabella tag
create table tag (
    ID smallint primary key auto_increment,
    nome varchar(50) not null
);

# 9) tabella faq
create table faq (
    ID smallint primary key auto_increment,
    domanda varchar(300) not null,
    risposta text not null
);

# 10) tabella donazione
create table donazione (
    ID smallint primary key auto_increment,
    importo smallint not null,
    email varchar(100) not null,
    `data` date not null
);

# 11) tabella info_struttura
create table info_struttura (
    ID smallint primary key auto_increment,
    telefono char(10) not null,
    orario_apertura time not null,
    orario_chiusura time not null,
    descrizione text not null
);

# 14) tabella adozione
create table richiesta_adozione (
    ID smallint primary key auto_increment,
    ID_utente smallint not NULL,
    ID_cane smallint not null,
    `data` date not null,
    documento varchar(400) not null,
    constraint adozione_utente foreign key (ID_utente) references utente(ID) on update cascade on delete no action,
    constraint adozione_cane foreign key (ID_cane) references cane(ID) on update cascade on delete no action
);

# 15) tabella preferiti
create table preferiti (
    ID smallint primary key auto_increment,
    ID_utente smallint not NULL,
    ID_cane smallint not null,
    constraint adozione_utente_pref foreign key (ID_utente) references utente(ID) on update cascade on delete no action,
    constraint adozione_cane_pref foreign key (ID_cane) references cane(ID) on update cascade on delete no action

);

# 16) tabella adozione_distanza
create table adozione_distanza (
    ID smallint primary key auto_increment,
    ID_utente smallint not NULL,
    ID_cane smallint not null,
    cadenza tinyint not null,
    `data` date not null,
    importo tinyint not null,
    constraint adozione_utente_dist foreign key (ID_utente) references utente(ID) on update cascade on delete no action,
    constraint adozione_cane_dist foreign key (ID_cane) references cane(ID) on update cascade on delete no action

);

# 17) tabella user_has_group
create table user_has_group (
    ID smallint primary key auto_increment,
    ID_utente smallint not NULL,
    ID_gruppo smallint not NULL,
    constraint user_has_group_utente foreign key (ID_utente) references utente(ID) on update cascade on delete no action,
    constraint user_has_group_gruppo foreign key (ID_gruppo) references ugroup(ID) on update cascade on delete no action
);

# 18) tabella ugroup_has_service
create table ugroup_has_service (
    ID smallint primary key auto_increment,
    ID_servizio smallint not NULL,
    ID_gruppo smallint not NULL,
    constraint ugroup_has_service_servizio foreign key (ID_servizio) references `service`(ID) on update cascade on delete no action,
    constraint ugroup_has_service_gruppo foreign key (ID_gruppo) references ugroup(ID) on update cascade on delete no action
);

# 19) tabella articolo_tag
create table articolo_tag (
    ID smallint primary key auto_increment,
    ID_articolo smallint not null,
    ID_tag smallint not NULL,
    constraint articolo_tag_articolo foreign key (ID_articolo) references articolo(ID) on update cascade on delete no action,
    constraint articolo_tag_tag foreign key (ID_tag) references tag(ID) on update cascade on delete no action
);

#20) tabella slider-home
create table slider_home (
    ID smallint primary key auto_increment,
    titolo varchar(50),
    sottotitolo varchar(200)
);

# popolamento tabella slider_home
insert into slider_home(titolo, sottotitolo) values
    ("Migliori amici per la zampa.", "La tua nuova dose giornaliera di serotonina. <br> Trova il tuo futuro amico a quattro zampe!"),
    ("Adozioni!", "Adotta un cane, anche a distanza! Visita la sezione del sito dedicata!"),
    ("titolo 3", "sottotitolo 3");

# popolamento tabella ugroup
INSERT INTO ugroup(nome, descrizione) VALUES ('admin', 'gruppo utente/i amministratori del sito web');
INSERT INTO ugroup(nome, descrizione) VALUES ('utente', 'gruppo utenti fruitori del sito web, non amministratori');

# popolamento tabella utente
INSERT INTO utente (ID, nickname, nome, cognome, passwrd, email, telefono) VALUES
	(1, "stefano23", "Stefano", "Pisciella", md5(md5(md5(md5(md5("stefano"))))), "stefano@gmail.com", "3880581680"),
    (2, "beatrice2", "Beatrice", "Tomassi", md5(md5(md5(md5(md5("beatrice"))))), "beatrice@gmail.com", "3880581681"),
    (3, "nicola3", "Nicola", "Rossi", md5(md5(md5(md5(md5("nicola"))))), "nicola@gmail.com", "3880581682"),
    (4, "admin", "nome_admin", "cognome_admin", md5(md5(md5(md5(md5("admin"))))), "admin@mail.com", "3880581683");

# popolamento tabella user_has_ugroup
INSERT INTO user_has_group(ID_utente, ID_gruppo) VALUES 
    (4, 1),
    (1, 2),
    (2, 2),
    (3, 2);

# popolamento tabella service
INSERT INTO `service`(ID, script, descrizione) VALUES
	(1, "admin/index", "home della dashboard dedicata all'Admin"),
	(2, "admin/faq", "gestisce l'inserimento delle faq"); 

    
# popolamento tabella ugroup_has_service 
INSERT INTO ugroup_has_service(ID, ID_servizio, ID_gruppo) VALUES
	(1, 1, 1),
    (2, 2, 1);
    
# popolamento tabella categoria

# popolamento tabella razza
INSERT INTO razza(nome) VALUES 
    ("Meticcio"),
    ("Bulldog Inglese"),
    ("Pitbull"),
    ("Border Collie");
    
# popolamento tabella cane
INSERT INTO cane(ID, nome, sesso, eta, razza, taglia, presentazione, chip, distanza, adottato) VALUES
	(1, "Mafalda", "F", 2, "Bulldog Inglese", "piccola", "", "04837264869", true, false),
	(2, "Clica", "F", 5, "Meticcio", "media", "", "04837264869478", false, false),
	(3, "Miriam", "F", 4, "Meticcio", "grande", "", "048372648696778", false, false),
	(4, "Ettore", "M", 5, "Meticcio", "piccola", "", "068372648694783", true, false),
	(5, "Chica", "F", 6, "Border Collie", "media", "", "048372649694783", false, false),
	(6, "Olimpia", "F", 5, "Pitbull", "piccola", "", "048692648694783", false, false);

# popolamento tabella immagine
INSERT INTO immagine(ID, ID_cane, ID_articolo, `path`, indice) VALUES

	# immagini per lo slider della home
    (1, null, null, "immagini/slider_home_1_1.jpg", 1),
	(2, null, null, "immagini/slider_home_2_2.jpg", 1),
	
    #immagini cani

    # cane 1
    (3, 1, null, "immagini/bulldog_inglese_1_1.jpg", 1),
    (9, 1, null, "immagini/immagini/bulldog_inglese_1_1.jpg", 1),

    #cane 2

	(4, 2, null, "immagini/meticcio_2_1.jpeg", 1),
    (10, 2, null, "immagini/meticcio_2_2.jpeg", 1),

    # cane 3
	(5, 3, null, "immagini/meticcio_3_1.jpg", 1),
    (11, 3, null, "immagini/meticcio_3_2.jpg", 1),

    # cane 4
	(6, 4, null, "immagini/meticcio_4_1.jpeg", 1),
    (12, 4, null, "immagini/meticcio_4_2.jpeg", 1),

    # cane 5
	(7, 5, null, "immagini/border_collie_5_1.jpeg", 1),

    # cane 6
	(8, 6, null, "immagini/pitbull_6_1.jpeg", 1),
	(14, 6, null, "immagini/pitbull_6_2.jpeg", 1);

# GESTIONE UTENZA
drop user if exists 'user'@'localhost';
create user 'user'@'localhost' identified by '1234';
grant all on petco.* to 'user'@'localhost';