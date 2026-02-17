<?php
/**
 * Saves the HTML for all website pages to a site_cache table
 *
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
define('SITE_HOME', $_SERVER['SITE_HOME']);
include SITE_HOME.'/site_config.php';

$drupal  = db_connect($DATABASES['drupal' ]);
$wave    = db_connect($DATABASES['default']);

$delete = $wave->prepare('delete from site_cache where nid=?');

$fields = ['nid','vid','path','type','title','html'];
$col    = implode(',', $fields);
$par    = implode(',', array_map(fn($f): string => ":$f", $fields));
$insert = $wave->prepare("insert into site_cache ($col) values($par)");

$sql    = "select n.nid, n.vid, n.type, n.title, p.alias as path
           from node_field_data n
           join path_alias      p on p.path=concat('/node/', n.nid)
           left join wave.site_cache c on n.nid=c.nid
           where n.status=1
             and (c.nid is null or from_unixtime(n.changed) > c.created)";
$query   = $drupal->query($sql);
foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $n) {
    $url  = "https://bloomington.in.gov$n[path]";
    echo "$url\n";
    $n['html'] = mb_strcut(get($url), 0, 65534);

    $delete->execute([$n['nid']]);
    $insert->execute($n);
}

function db_connect(array $config): \PDO {
    $pdo = new \PDO($config['dsn'], $config['user'], $config['pass']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function get(string $url): ?string
{
    $request = curl_init($url);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
    $res     = curl_exec($request);
    return $res ? $res : null;
}
