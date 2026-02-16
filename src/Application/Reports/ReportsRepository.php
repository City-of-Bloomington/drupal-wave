<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Reports;

use Application\PdoRepository;

class ReportsRepository extends PdoRepository
{
    public const SORT_DEFAULT = 'created desc';
    public function __construct() { parent::__construct('reports'); }
    public static $sortable_columns = ['r.path', 'created', 'error', 'contrast', 'username', 'department', 'views'];

    public function search(array $fields=[], string $order=self::SORT_DEFAULT, ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = "select r.*, u.username, u.department, a.views
                   from reports r
                   join drupal.node_field_data  n on r.nid=n.nid
                   join drupal.node_revision    v on n.nid=v.nid and n.vid=v.vid
                   left join users              u on u.id=v.revision_uid
                   left join analytics          a on r.path=a.path";
        $joins  = [];
        $where  = [];
        $params = [];

		if ($fields) {
			foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'errors':
                        if (is_numeric($v)) {
                            $where[] = $v
                                     ? '(r.error>0 or  r.contrast>0)'
                                     : '(r.error<1 and r.contrast<1)';
                        }
                    break;
                    case 'department':
                        if ($v == 'UNKNOWN') { $where[] = "$k is null"; }
                        else {
                            $where[]    = "u.$k like :$k";
                            $params[$k] = "$v%";
                        }
                    break;
                    case 'path':
                        $where[] = "r.$k like :$k";
                        $params[$k] = "$v%";
                    break;
                    default:
                        $where[]    = "$k like :$k";
                        $params[$k] = "$v%";
                }
			}
		}
        $sql = self::buildSql($select, $joins, $where, null, $order);
		return $this->performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public function creditsRemaining(): int
    {
        $sql  = 'select report from reports order by created desc limit 1';
        $q    = $this->pdo->query($sql);
        $r    = $q->fetchAll(\PDO::FETCH_ASSOC);
        $json = json_decode($r[0]['report'], true);
        return (int)$json['statistics']['creditsremaining'];
    }
}
