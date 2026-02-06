<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\List;

use Application\Reports\ReportsRepository;
use Web\Ldap;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $repo   = new ReportsRepository();
        $page   = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = self::prepareSearch();
        $list   = $repo->search($search, null, parent::ITEMS_PER_PAGE, $page);

        return new View($list['rows'] ?? [],
                        $search,
                        $list['total'] ?? 0,
                        parent::ITEMS_PER_PAGE,
                        $page);
    }

    private static function prepareSearch(): array
    {
        $s = [];

        if (!empty($_GET['username'])) { $s['username'] =      $_GET['username']; }
        if (!empty($_GET['path'    ])) { $s['path'    ] =      $_GET['path'    ]; }

        if (        isset($_GET['errors'])
            && is_numeric($_GET['errors'])) {

            $s['errors'  ] = (int)$_GET['errors'];
        }

        if (     !empty($_GET['department'])
            && in_array($_GET['department'], array_keys(Ldap::$departments))) {

            $s['department'] = $_GET['department'];
        }

        return $s;
    }
}
