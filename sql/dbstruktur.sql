create database vokabeltrainer;

use vokabelTrainer;

create table benutzer (
    benutzer_id int auto_increment primary key,
    nutzername varchar(100),
    passworthash varchar(255)
);

create table sprachen (
    sprache_id int auto_increment primary key,
    sprache varchar(256),
    sprache_kuerzel varchar(2)
);

create table vokabel (
    vokabel_id int auto_increment primary key,
    benutzer_id int,
    sprache_id int,
    vokabelwort varchar(256) not null,
    uebersetzung varchar(256),
    foreign key (benutzer_id) references benutzer(benutzer_id),
    foreign key (sprache_id) references sprachen(sprache_id)
);

create table beispielsatz (
    satz_id int auto_increment primary key,
    benutzer_id int,
    vokabel_id int,
    sprache_id int,
    satz text not null,
    uebersetzter_satz text,
    foreign key (benutzer_id) references benutzer(benutzer_id),
    foreign key (vokabel_id) references vokabel(vokabel_id),
    foreign key (sprache_id) references sprachen(sprache_id)
);

create table lernstatistik (
    statistik_id int auto_increment primary key,
    benutzer_id int,
    sprache_id int,
    uebung_id int,
    zeitstempel timestamp,
    punktzahl int,
    foreign key (benutzer_id) references benutzer(benutzer_id),
    foreign key (sprache_id) references sprachen(sprache_id)
);