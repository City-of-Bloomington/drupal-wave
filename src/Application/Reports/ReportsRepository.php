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
    public function __construct() { parent::__construct('reports'); }

    public function search(array $fields=[], ?string $order='r.path', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = "select r.*, u.username, u.department
                   from reports r
                   join drupal.node_field_data  n on r.nid=n.nid
                   join drupal.node_revision    v on n.nid=v.nid and n.vid=v.vid
                   left join users              u on u.id=v.revision_uid";
        $joins  = [];
        $where  = [];
        $params = [];

		if ($fields) {
			foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'errors':
                        $where[] = $v
                                 ? '(r.error>0 or  r.contrast>0)'
                                 : '(r.error<1 and r.contrast<1)';
                    break;
                    case 'department':
                        if ($v == 'UNKNOWN') { $where[] = "$k is null"; }
                        else {
                            $where[]    = "$k like :$k";
                            $params[$k] = "$v%";
                        }
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
}
