<?php
$status = true;

$sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}activity` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `appId` varchar(255) NOT NULL,
    `type` varchar(32) NOT NULL,
    `data` text NOT NULL,
    `permission` int(11) DEFAULT NULL,
    `dateCreated` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`,`owner`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
$status = $status && mysql_1_3_3($sql);

$sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}album` (
    `id` varchar(6) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `extra` text,
    `count` int(10) unsigned NOT NULL DEFAULT '0',
    `permission` tinyint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`,`owner`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
$status = $status && mysql_1_3_3($sql);

$sql = <<<SQL
  CREATE TABLE IF NOT EXISTS `{$this->mySqlTablePrefix}elementAlbum` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `owner` varchar(255) NOT NULL,
    `type` enum('photo') NOT NULL,
    `element` varchar(6) NOT NULL DEFAULT 'photo',
    `album` varchar(255) NOT NULL,
    `order` smallint(11) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`owner`,`type`,`element`,`album`),
    KEY `element` (`element`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
$status = $status && mysql_1_3_3($sql);

$sql = <<<SQL
  ALTER TABLE  `{$this->mySqlTablePrefix}elementTag` ADD INDEX (  `element` )
SQL;
$status = $status && mysql_1_3_3($sql);

$sql = <<<SQL
  ALTER TABLE  `{$this->mySqlTablePrefix}photo` ADD  `filenameOriginal` VARCHAR( 255 ) NULL AFTER  `dateUploadedYear`
SQL;
$status = $status && mysql_1_3_3($sql);

$sql = <<<SQL
  ALTER TABLE  `{$this->mySqlTablePrefix}elementGroup` CHANGE  `type`  `type` ENUM(  'photo',  'album' )  NOT NULL
SQL;
$status = $status && mysql_1_3_3($sql);

$sql = <<<SQL
  SELECT `id`, `owner`, `pathOriginal` from `{$this->mySqlTablePrefix}photo`
SQL;
$photos = getDatabase()->all($sql);
foreach($photos as $photo)
{
  $filename = basename($photo['pathOriginal']);
  $filenameOriginal = substr($filename, strpos($filename, '-')+1);
  $sql = <<<SQL
    UPDATE `{$this->mySqlTablePrefix}photo` SET `filenameOriginal`=:filenameOriginal WHERE `owner`=:owner AND `id`=:id
SQL;
  getDatabase()->execute($sql, array(':filenameOriginal' => $filenameOriginal, ':owner' => $photo['owner'], ':id' => $photo['id']));
}

$sql = <<<SQL
  UPDATE `{$this->mySqlTablePrefix}admin` SET `value`=:version WHERE `key`=:key
SQL;
$status = $status && mysql_1_3_3($sql, array(':key' => 'version', ':version' => '1.3.3'));


function mysql_1_3_3($sql, $params = array())
{
  try
  {
    getDatabase()->execute($sql, $params);
    getLogger()->info($sql);
  }
  catch(Exception $e)
  {
    getLogger()->crit($e->getMessage()); 
    return false;
  }
  return true;
}

return $status;

