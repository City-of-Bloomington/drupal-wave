<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\Info;

use Application\Reports\ReportsRepository;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $repo = new ReportsRepository();
        $r    = $repo->loadById((int)$params['id']);
        if ($r) {
            return new View($r);
        }

        return new \Web\Views\NotFoundView();
    }
}
