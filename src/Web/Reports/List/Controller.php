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
        $sort   = self::prepareSort();
        $search = self::prepareSearch();
        $list   = $repo->search(fields:$search,
                                 order:$sort,
                          itemsPerPage:parent::ITEMS_PER_PAGE,
                           currentPage:$page);

        return new View($list['rows'] ?? [],
                        $search,
                        $sort,
                        $list['total'] ?? 0,
                        parent::ITEMS_PER_PAGE,
                        $page,
                        $repo->creditsRemaining());
    }

    private static function prepareSearch(): array
    {
        // defaults
        $s = ['errors'=>1];

        if (!empty($_GET['username'])) { $s['username'] =      $_GET['username']; }
        if (!empty($_GET['path'    ])) { $s['path'    ] =      $_GET['path'    ]; }

        if (isset($_GET['errors'])) {
            $s['errors'] = is_numeric($_GET['errors']) ? (int)$_GET['errors'] : 'both';
        }
        if (isset($_GET['pdf'])) {
            $s['pdf'   ] = is_numeric($_GET['pdf'   ]) ? (int)$_GET['pdf'   ] : 'both';
        }

        if (     !empty($_GET['department'])
            && in_array($_GET['department'], array_keys(Ldap::$departments))) {

            $s['department'] = $_GET['department'];
        }

        return $s;
    }

    private static function prepareSort(): ?string
    {
        if (!empty($_GET['sort'])) {
            $s = explode(' ', $_GET['sort']);
            if (in_array($s[0], ReportsRepository::$sortable_columns)) {
                return (isset($s[1]) && $s[1]=='desc')
                        ? "$s[0] desc"
                        : "$s[0] asc";
            }
        }
        return 'error desc';
    }
}
