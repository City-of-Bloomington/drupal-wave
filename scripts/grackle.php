<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Application\Database;

$SCAN_DATE = '2026-02-05';

include '../src/Web/bootstrap.php';
$csv = fopen('./grackle.csv', 'r');
define('FILENAME', 0);
define('SCORE',    1);
define('URL',      2);
define('PATH',     3);

$pdo = Database::getConnection();
$pdo->query('truncate table grackle_results');
$ins = $pdo->prepare('insert into grackle_results values(?,?,?,?,?)');

while ($d = fgetcsv($csv)) {
    $data = [
        str_replace('https://bloomington.in.gov', '', urldecode($d[PATH])),
        urldecode($d[FILENAME]),
        urldecode($d[URL]),
             (int)$d[SCORE],
             $SCAN_DATE
    ];
    echo $data[1]."\n";
    $ins->execute($data);
}
