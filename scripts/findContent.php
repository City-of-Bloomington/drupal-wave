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

$year    = 2017;
$linked  = fopen("./{$year}_linked.csv", 'w');
$unused  = fopen("./{$year}_unused.csv", 'w');

$sql     = "select fid,
                   filename,
                   from_unixtime(changed) as changed
            from file_managed
            where year(from_unixtime(changed))=?
              and filemime like ?";
$query   = $drupal->prepare($sql);
$query->execute([$year, 'application%']);
$files   = $query->fetchAll(\PDO::FETCH_ASSOC);

$sql     = 'select nid,vid,path,type,title from site_cache where html like ?';
$query   = $wave->prepare($sql);
foreach ($files as $f) {
    $nodes = search($query, $f['filename']);
    echo "$f[fid] $f[changed] $f[filename] ";

    if (!$nodes) {
        echo "Not Found\n";
        fputcsv($unused, $f);
    }
    else {
        echo "\n";
        foreach ($nodes as $n) {
            $d = array_merge($f, $n);
            fputcsv($linked, $d);
        }
    }
}

function search(\PDOStatement &$query, string $search): array
{
    $query->execute(["%$search%"]);
    $rows = $query->fetchAll(\PDO::FETCH_ASSOC);

    $search  = rawurlencode($search);
    $search  = str_replace('%', '\%', $search);

    $query->execute(["%$search%"]);
    array_merge($rows, $query->fetchAll(\PDO::FETCH_ASSOC));
    return $rows;
}

function db_connect(array $config): \PDO {
    $pdo = new \PDO($config['dsn'], $config['user'], $config['pass']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
