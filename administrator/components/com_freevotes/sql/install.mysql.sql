CREATE TABLE `#__free_votes_domande` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`nome` varchar(255) NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__free_votes_risposte` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`nome` varchar(255) NOT NULL,
	`colore` varchar(7) NOT NULL,
	`domanda` int(11) NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__free_votes_risposte_user` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_user` int(11) NOT NULL,
	`risposta` int(11) NOT NULL,
	`voto` int(11) NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;