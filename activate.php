<?php
if (!defined('PERCH_DB_PREFIX')) exit;

$sql = "CREATE TABLE `__PREFIX__fhd_collecter_profiles` (
  `profileID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profileLabel` varchar(255) NOT NULL DEFAULT '',
  `collectionID` int(10) unsigned NOT NULL,
  `primarySetSlug` varchar(255) NOT NULL DEFAULT '',
  `secondarySetSlug` varchar(255) NOT NULL DEFAULT '',
  `titleField` varchar(255) NOT NULL DEFAULT '',
  `orderField` varchar(255) NOT NULL DEFAULT 'item_order',
  `profileCreated` datetime NOT NULL,
  `profileUpdated` datetime NOT NULL,
  PRIMARY KEY (`profileID`),
  KEY `collectionID` (`collectionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$this->db->execute(str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql));
$API = new PerchAPI(1.0, 'fhd_runway_collecter');
$Privileges = $API->get('UserPrivileges');
$Privileges->create_privilege('fhd_runway_collecter', 'Access FHD Collection Organizer');
$Privileges->create_privilege('fhd_runway_collecter.configure', 'Configure FHD Collection Organizer');

return (bool)$this->db->get_value('SHOW TABLES LIKE '.$this->db->pdb(PERCH_DB_PREFIX.'fhd_collecter_profiles'));
