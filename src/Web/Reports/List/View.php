<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\List;

use Web\Ldap;

class View extends \Web\View
{
    public function __construct(array $reports, array $search, int $total, int $itemsPerPage, int $currentPage)
    {
        parent::__construct();

        $this->vars = [
            'reports'     => $reports,
            'search'      => $search,
            'total'       => $total,
            'itemsPerPage'=> $itemsPerPage,
            'currentPage' => $currentPage,
            'DRUPAL_SITE' => DRUPAL_SITE,
            'departments' => self::departments()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/reports/list.twig', $this->vars);
    }

    private static function departments(): array
    {
        $opts = [ ['value'=>''],['value'=>'UNKNOWN'] ];
        foreach (Ldap::$departments as $d=>$ou) { $opts[] = ['value'=>$d]; }
        return $opts;
    }
}
