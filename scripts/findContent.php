<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
define('SITE_HOME', $_SERVER['SITE_HOME']);
include SITE_HOME.'/site_config.php';

$drupal  = db_connect($DATABASES['drupal' ]);
$wave    = db_connect($DATABASES['default']);

$search  = '2026 Senior RESOURCE Guide 2.11.26.pdf';
$search  = rawurlencode($search);
$search  = str_replace('%', '\%', $search);

$sql     = 'select * from site_cache where html like ?';
$query   = $wave->prepare($sql);
$query->execute(["%$search%"]);
foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $r) {
    echo "$r[nid] $r[path]\n";
}




function db_connect(array $config): \PDO {
    $pdo = new \PDO($config['dsn'], $config['user'], $config['pass']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
