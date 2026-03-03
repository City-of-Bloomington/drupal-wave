<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Content\Search;

use Application\Content\ContentRepository;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['query'])) {
            $repo = new ContentRepository();
            $res  = $repo->pages($_GET['query']);
            return new View($_GET['query'], $res);
        }

        return new View();
    }
}
