DROP TABLE IF EXISTS sacoche_devoir;

CREATE TABLE sacoche_devoir (
	devoir_id           MEDIUMINT(8) UNSIGNED                NOT NULL AUTO_INCREMENT,
	prof_id             MEDIUMINT(8) UNSIGNED                NOT NULL DEFAULT 0,
	groupe_id           MEDIUMINT(8) UNSIGNED                NOT NULL DEFAULT 0,
	devoir_date         DATE                                 NOT NULL DEFAULT "0000-00-00",
	devoir_info         VARCHAR(60)  COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
	devoir_visible_date DATE                                 NOT NULL DEFAULT "0000-00-00",
	devoir_partage      TEXT         COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
  	gepi_cn_devoirs_id  int(11) DEFAULT NULL COMMENT 'Id de l evaluation gepi',
	PRIMARY KEY (devoir_id),
	KEY prof_id (prof_id),
	KEY groupe_id (groupe_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
