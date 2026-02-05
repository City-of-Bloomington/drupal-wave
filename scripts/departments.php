<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Application\Database;
use Web\Ldap;

include '../src/Web/bootstrap.php';

$ldap = new Ldap($LDAP['COB']);
$pdo  = Database::getConnection();
$upd  = $pdo->prepare('update users set department=? where id=?');

$sql  = 'select * from users where department is null';
$q    = $pdo->query($sql);
foreach ($q as $r) {
    $u = $ldap->findUser($r['username']);
    if ($u) {
        $d = Ldap::department($u['dn']);
        echo "$r[username] - $d\n";
        $upd->execute([$d, $r['id']]);
    }
}
