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
    # ID_categoria smallint default 1,
    titolo varchar(100) not null,
    contenuto text not null,
    autore varchar(100) not null,
    `data` date not null,
    categoria varchar(30) not null,
    `path` varchar(400) not null,
    constraint articolo_utente foreign key (ID_utente) references utente(ID) on update cascade on delete set null#,
    #constraint articolo_categoria foreign key (ID_categoria) references categoria(ID) on update cascade on delete set NULL
    # NOTA: se una categoria viene eliminata, va cambiato il nome della categoria in 'no categoria'
);

# 6) tabella immagine
create table immagine (
    ID smallint primary key auto_increment,
    ID_cane smallint,
    `path` varchar(400) not null,
    # indice di visualizzazione dell'immagine
    indice smallint not null,
    constraint immagine_cane foreign key (ID_cane) references cane(ID) on update cascade on delete cascade
);

# 7) tabella richiesta_info
create table richiesta_info (
    ID smallint primary key auto_increment,
    # 'ID_utente smallint not null' non deve essere "not null" perché anche un utente non loggato può fare la richiesta info
    ID_utente smallint,
    `data` date not null,
    # 'chip char(15) not null' non deve essere "not null" perché si possono anche chiedere informazioni non necessariamente
    # riguardanti un cane in particolare
	chip char(15),
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
    ("Adozioni!", "Adotta un cane, anche a distanza! <br> Visita la sezione del sito dedicata!"),
    ("Informati bene!", "Prima di andar via, dai un'occhiata al blog! :)");

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
	(1, "Mafalda", "F", 2, "Bulldog Inglese", "piccola", "Mafalda è una bulldog inglese nata sfortunata; più piccina dei fratelli, troppo chiara per lo standard di razza e, soprattutto, con un grave problema congenito al cuore. 

È stata operata da una equipe specializzata che le ha creato una nuova valvola cardiaca e ed è intervenuta anche su palato e alle narici, in modo da poter respirare meglio.  
La sua qualità di vita è nello standard di qualunque bulldog anche se dovrà fare controlli cardiologici periodici e continuare per il momento con la terapia e potrà essere sterilizzata tra circa un anno.
Nonostante i suoi problemi di salute è una cagnolina allegra e gioiosa, che cerca molto il contatto fisico e le coccole. 

Sta aspettando qualcuno che sappia amarla e una famiglia per la vita.

Un rifugio, un canile non possono sostituirsi all’amore di una famiglia. Regala la vera vita…adotta!", "04837264869", true, false),
	(2, "Calica", "F", 5, "Meticcio", "media", "Una ragazza speciale, dall’olfatto strepitoso, che ama la campagna, correre e passeggiare. Sempre in cerca di novità è super socievole con tutti. Taglia media saprà riempire le vostre giornate di gioia.", "04837264869478", false, false),
	(3, "Miriam", "F", 4, "Meticcio", "grande", "Miriam è una cagnolina che ama interagire con i suoi simili ma è insicura con le persone. 
Ha bisogno di stabilità e di una routine quotidiana che le permetta di instaurare un legame di fiducia duraturo con chi farà parte della sua vita.

E’ una taglia media.

Il nostro team vi aiuterà a trovare insieme il modo migliore per costruire un rapporto stupendo.

Si trova a Vairano (Pavia) a pochi passi da Milano. 

Un rifugio, un canile non possono sostituirsi all’amore di una famiglia. Regala la vera vita…adotta!", "048372648696778", false, false),
	(4, "Ettore", "M", 5, "Meticcio", "piccola", "Un bellissimo incrocio di pastore tedesco, socievole e dolce con le persone, curioso, possessivo e qualche volta un po’ testardo e arrogante con gli altri maschi. Grazie a un breve percorso con i nostri educatori, e a un po’ di pazienza, saprete trovare la giusta sintonia.

Un canile, un rifugio, l'impegno di tutti noi non sostituiscono l'amore di una famiglia.", "068372648694783", true, false),
	(5, "Chica", "F", 6, "Border Collie", "media", "Chica è un border collie che ha trascorso la giovinezza in campagna fra animali domestici di ogni specie, e con la sorella Cindy. Nata nel 2012, è abituata a vivere in un grande spazio, ed è un cane energico e attivo. Nonostante questo presenta qualche problema di salute, di cui ci si occupa con attenzione in rifugio. Nonostante la sua età, ha ancora voglia di giocare e amare una famiglia, che possa ricambiare il suo amore. 

Un rifugio, un canile non possono sostituirsi all’amore di una famiglia. Regala la vera vita…adotta!", "048372649694783", false, false),
	(6, "Olimpia", "F", 5, "Pitbull", "piccola", "Olimpia è un pitbull con lo sguardo attento, un po’ testarda ma che sa farsi volere bene dagli umani, con i quali diventa affettuosa e coccolona. Non ama la compagnia di altri animali, infatti sogna di essere adottata e iniziare una nuova vita in una casa accogliente, in cui essere protetta e amata da nuovi amici umani con cui fare tante passeggiate. É giovane ma con qualche acciacco, tenuto sotto controllo dai veterinari. 

Un rifugio, un canile non possono sostituirsi all’amore di una famiglia. Regala la vera vita…adotta!", "048692648694783", false, false);

# inserimento articoli prova
INSERT INTO articolo(ID_utente, titolo, contenuto, autore, `data`, categoria, `path`) VALUES
    (4, "VACCINI", "Questo è l'articolo 1 di prova. Si parlerà di cose varie e si torverà nella categoria 'Salute&Benessere' e niente, ciao ciao. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.", "admin", "2022-06-12", "Salute&Benessere", "immagini/articolo_1.jpg"),
    (1, "LA STORIA DI STE E ALBERT", "Questo è l'articolo 2 di prova. Si parlerà di cose varie e si torverà nella categoria 'Le Vostre Storie' e racconta la storia dell'adozione del cane Albert da parte di Stefano. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.", "Stefano Pisciella", "2022-07-01", "Le Vostre Storie", "immagini/articolo_2.jpg"),
    (4, "FERIE ESTIVE", "Questo è l'articolo 3 di prova. Si parlerà di cose varie e si torverà nella categoria 'News' e niente, ciao ciao. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.", "admin", "2022-08-08", "News", "immagini/articolo_3.jpg"),
    (4, "Aggiornamenti procedure affido", "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.", "admin", "2022-05-27", "News", "immagini/articolo_4.jpg"),
    (4, "Aggiornamento norme di comportamento COVID-19", "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.", "admin", "2022-04-10", "News", "immagini/articolo_5.jpg");

# popolamento tabella tags
insert into tag (nome) values 
    ("sicurezza"),
    ("vaccino"),
    ("covid-19"),
    ("estate"),
    ("ferie"),
    ("comunicazione"),
    ("lieto fine");

# popolamento tabella articolo_tag
insert into articolo_tag (ID_articolo, ID_tag) VALUES
    (1, 1),
    (1, 2),
    (2, 7),
    (3, 4),
    (3, 5),
    (3, 6),
    (4, 6),
    (5, 1),
    (5, 3),
    (5, 6);

# popolamento tabella immagine
INSERT INTO immagine(ID_cane, `path`, indice) VALUES

	# immagini per lo slider della home
    (null, "immagini/slider_home_1_1.jpg", 1),
	(null, "immagini/slider_home_2_2.jpg", 1),
    (null, "immagini/slider_home_3.jpg", 1),
	
    #immagini cani

    # cane 1
    (1, "immagini/bulldog_inglese_1_1.jpg", 1),
    (1, "immagini/bulldog_inglese_1_2.jpg", 1),

    #cane 2

	(2, "immagini/meticcio_2_1.jpeg", 1),
    (2, "immagini/meticcio_2_2.jpeg", 1),

    # cane 3
	(3, "immagini/meticcio_3_1.jpg", 1),
    (3, "immagini/meticcio_3_2.jpg", 1),

    # cane 4
	(4, "immagini/meticcio_4_1.jpeg", 1),
    (4, "immagini/meticcio_4_2.jpeg", 1),

    # cane 5
	(5, "immagini/border_collie_5_1.jpeg", 1),

    # cane 6
	(6, "immagini/pitbull_6_1.jpeg", 1),
	(6, "immagini/pitbull_6_2.jpeg", 1);

# GESTIONE UTENZA
drop user if exists 'user'@'localhost';
create user 'user'@'localhost' identified by '1234';
grant all on petco.* to 'user'@'localhost';