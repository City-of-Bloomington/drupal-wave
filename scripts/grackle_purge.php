<?php
/**
 * Marks files that are no longer linked on the website
 *
 * For webscan, we only care about Drupal files that are actually linked.
 * Drupal files stick around because they were linked in past revisions of pages.
 * Drupal does not delete these file in case someone wants to revert to a previous
 * revision.  Still, we do not want past files to be counted as problems.
 *
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Application\Content\ContentRepository;
use Application\Database;

include '../src/Web/bootstrap.php';

$webscan = Database::getConnection('default');
$content = new ContentRepository();
$update  = $webscan->prepare('update grackle_results set unlinked=1 where path=? and url=?');
$query   = $webscan->prepare('select url from grackle_results where path=?');

/**
 * Grackle results for files that are not linked in the current revision.
 * These files are still linked in past revisions
 */
$sql = "select g.url, g.score, g.path, d.nid
        from   grackle_results      g
        join drupal.path_alias      p on g.path=p.alias
        join drupal.node_field_data d on p.path=concat('/node/', d.nid)
        where g.unlinked=0";
$q   = $webscan->query($sql);
foreach ($q->fetchAll(\PDO::FETCH_ASSOC) as $r) {
    $links = $content->pdf_links((int)$r['nid']);

    $query->execute([$r['path']]);
    $scores  = $query->fetchAll(\PDO::FETCH_COLUMN);
    foreach (array_diff($scores, $links) as $url) {
        echo "\tunlinking $url\n";
        $s = $update->execute([$r['path'], $url]);
        if (!$s) {
            $e = $webscan->errorInfo();
            print_r($e);
            exit();
        }
    }
}
