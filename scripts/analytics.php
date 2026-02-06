<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Application\Database;

include '../src/Web/bootstrap.php';

$pdo = Database::getConnection();
$qq  = $pdo->prepare('select path,alias from drupal.path_alias where alias=?');
$ins = $pdo->prepare('insert into analytics set path=?, views=?');
$csv = fopen('./out.csv', 'r');

$pdo->query('truncate table analytics');

while ($d = fgetcsv($csv)) {
    echo "$d[0] - $d[1]\n";

    $qq->execute([$d[0]]);
    $r = $qq->fetchAll(\PDO::FETCH_ASSOC);

    if (count($r)) {
        $ins->execute([ $d[0], $d[1] ]);
    }
}
