<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
define('SITE_HOME', $_SERVER['SITE_HOME']);
include SITE_HOME.'/site_config.php';


$api_key = WAVE_API_KEY;
$drupal  = db_connect($DATABASES['drupal' ]);
$wave    = db_connect($DATABASES['default']);
$del     = $wave->prepare('delete from reports where nid=?');
$ins     = $wave->prepare("insert reports (nid,path,error,contrast,alert,report) values(?,?,?,?,?,?)");

$sql     = "select n.nid,
                   n.type,
                   d.title,
                   from_unixtime(d.changed) as changed,
                   d.uid,
                   p.alias,
                   r.error, r.created,
                   c.field_coordinates_lat
            from      node                    n
                 join node_field_data         d on n.nid=d.nid and n.vid=d.vid
                 join path_alias              p on p.path=concat('/node/', n.nid)
            left join wave.reports            r on r.nid=n.nid
            left join node__field_coordinates c on n.nid=c.entity_id
            where (r.nid is null or from_unixtime(d.changed) > r.created)";
$query   = $drupal->query($sql);
foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $r) {
    echo "$r[nid] $r[alias] ";
    $webpage = "https://bloomington.in.gov$r[alias]";
    $url     = "https://wave.webaim.org/api/request?key=$api_key&url=$webpage";
    $res     = get($url);
    $json    = json_decode($res, true);
    echo "Credits remaining: {$json['statistics']['creditsremaining']}\n";

    // Google Maps always trigger an error in WAVE
    // Do not count the Google Map error against the page
    if ($r['field_coordinates_lat'] &&
        $json['categories']['error']['count'] > 0) {

        $json['categories']['error']['count']--;
    }

    $del->execute([$r['nid']]);
    $ins->execute([
        $r['nid'  ],
        $r['alias'],
        $json['categories']['error'   ]['count'],
        $json['categories']['contrast']['count'],
        $json['categories']['alert'   ]['count'],
        json_encode($json)
    ]);
}

function get(string $url): ?string
{
    $request = curl_init($url);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
    $res     = curl_exec($request);
    return $res ? $res : null;
}

function db_connect(array $config): \PDO {
    $pdo = new \PDO($config['dsn'], $config['user'], $config['pass']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
