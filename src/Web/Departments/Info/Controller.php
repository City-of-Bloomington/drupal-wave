<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Departments\Info;

use Application\Departments\DepartmentsRepository;
use Application\Users\UsersRepository;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $dept_id = (int)$params['id'];

        $repo    = new DepartmentsRepository();
        $ur      = new UsersRepository();
        $dept    = $repo->loadById($dept_id);
        $users   = $ur->find(['department_id'=>$dept_id]);

        return new View($dept, $users['rows']);
    }
}
