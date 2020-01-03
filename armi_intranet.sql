-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Darbinė stotis: localhost
-- Atlikimo laikas:  2010 m. Birželio 15 d.  05:46
-- Serverio versija: 5.1.36
-- PHP versija: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Duombazė: `armi_intranet`
--

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `l_gr_nariai`
--

CREATE TABLE IF NOT EXISTS `l_gr_nariai` (
  `grn_nario_id` int(11) NOT NULL COMMENT 'nario id',
  `grn_grupes_id` int(11) NOT NULL COMMENT 'grupes id',
  KEY `grn_nario_id` (`grn_nario_id`),
  KEY `grn_nario_id_2` (`grn_nario_id`),
  KEY `grn_nario_id_3` (`grn_nario_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Sukurta duomenų kopija lentelei `l_gr_nariai`
--


-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `l_ivykiu_tipai`
--

CREATE TABLE IF NOT EXISTS `l_ivykiu_tipai` (
  `logt_id` int(11) NOT NULL AUTO_INCREMENT,
  `logt_aprasymas` text COLLATE utf8_lithuanian_ci NOT NULL,
  PRIMARY KEY (`logt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci COMMENT='ivykiu tipu sarasas' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `l_log`
--

CREATE TABLE IF NOT EXISTS `l_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `log_vartotojas` int(11) NOT NULL,
  `log_ivykioId` int(11) NOT NULL,
  `log_sql` text COLLATE utf8_lithuanian_ci,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci COMMENT='ivykiu sarasas' AUTO_INCREMENT=6 ;


-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `l_manogrupes`
--

CREATE TABLE IF NOT EXISTS `l_manogrupes` (
  `mgr_id` int(11) NOT NULL AUTO_INCREMENT,
  `mgr_pavadinimas` text COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'grupes pavadinimas',
  `mgr_vartotojas` int(11) NOT NULL COMMENT 'grupe suskures vartotojas',
  `mgr_aprasymas` text COLLATE utf8_lithuanian_ci COMMENT 'grupes aprasymas',
  `mgr_logotipas` blob COMMENT 'grupes logotipas',
  PRIMARY KEY (`mgr_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci COMMENT='vartotoju sukurtos grupes' AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `l_miestai`
--

CREATE TABLE IF NOT EXISTS `l_miestai` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'miesto id',
  `m_pavadinimas` varchar(30) COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'miesto pavadinimas',
  PRIMARY KEY (`m_id`),
  UNIQUE KEY `m_pavadinimas` (`m_pavadinimas`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci COMMENT='miestu sarasas' AUTO_INCREMENT=2 ;


-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `l_pagr_grupes`
--

CREATE TABLE IF NOT EXISTS `l_pagr_grupes` (
  `pGrupes_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'vartotoju grupes id',
  `pGrupes_pavadinimas` varchar(30) COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'grupes pavadinimas',
  `pGrupes_teises` char(10) COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'grupes teises',
  PRIMARY KEY (`pGrupes_id`),
  UNIQUE KEY `pGrupes_pavadinimas` (`pGrupes_pavadinimas`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci COMMENT='pagrindines vartotoju grupes (admin, user ir pan.)' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `l_pareigos`
--

CREATE TABLE IF NOT EXISTS `l_pareigos` (
  `par_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'pareigu id',
  `par_pavadinimas` varchar(50) COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'pereigu pavadinimas',
  `par_lygis` int(11) DEFAULT NULL COMMENT 'lygis?',
  PRIMARY KEY (`par_id`),
  UNIQUE KEY `par_pavadinimas` (`par_pavadinimas`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci COMMENT='pareigos' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `l_skyriai`
--

CREATE TABLE IF NOT EXISTS `l_skyriai` (
  `sk_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sk_pavad` varchar(10) COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'skyriaus pavadinimas',
  `sk_miestoID` int(11) NOT NULL COMMENT 'miesto ID',
  `sk_adresas` varchar(50) COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'skyriaus adresas',
  `sk_tel` varchar(12) COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'kontaktinis telefonas',
  `sk_mail` varchar(30) COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'el. pastas',
  `sk_tinklas` int(11) NOT NULL COMMENT 'skyriaus tinklo id',
  PRIMARY KEY (`sk_id`),
  UNIQUE KEY `sk_pavad` (`sk_pavad`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci COMMENT='skyriu sarasas' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `l_tinklai`
--

CREATE TABLE IF NOT EXISTS `l_tinklai` (
  `tink_id` int(11) NOT NULL AUTO_INCREMENT,
  `tink_pavadinimas` text COLLATE utf8_lithuanian_ci NOT NULL,
  PRIMARY KEY (`tink_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci COMMENT='tinklu sarasas' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `l_vartotojai`
--

CREATE TABLE IF NOT EXISTS `l_vartotojai` (
  `vart_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'vartotojo ID',
  `vart_login` varchar(20) COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'prisijungimo vardas',
  `vart_vardas` varchar(30) COLLATE utf8_lithuanian_ci DEFAULT NULL COMMENT 'vardas',
  `vart_pavarde` varchar(30) COLLATE utf8_lithuanian_ci DEFAULT NULL COMMENT 'pavarde',
  `vart_skyrius` int(11) DEFAULT NULL COMMENT 'vartotojo skyrius',
  `vart_grupe` int(11) NOT NULL COMMENT 'vartotojo grupe',
  `vart_pastas` varchar(30) COLLATE utf8_lithuanian_ci DEFAULT NULL COMMENT 'el. pasto adresaas',
  `vart_tel` char(9) COLLATE utf8_lithuanian_ci DEFAULT NULL COMMENT 'kontaktinis tel. nr',
  `vart_foto` blob COMMENT 'foto, avatar',
  `vart_password` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL COMMENT 'slaptazodis',
  `vart_pareigos` int(11) DEFAULT NULL COMMENT 'pareigos',
  PRIMARY KEY (`vart_id`),
  UNIQUE KEY `vart_login` (`vart_login`),
  FULLTEXT KEY `vart_password` (`vart_password`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci AUTO_INCREMENT=22 ;

