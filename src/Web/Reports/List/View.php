<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\List;

use Application\Reports\ReportsRepository;
use Web\Ldap;

class View extends \Web\View
{
    public function __construct(array  $reports,
                                array  $search,
                                string $sort,
                                int    $total,
                                int    $itemsPerPage,
                                int    $currentPage,
                                int    $credits)
    {
        parent::__construct();

        $this->vars = [
            'reports'     => $reports,
            'search'      => $search,
            'sort'        => $sort,
            'total'       => $total,
            'itemsPerPage'=> $itemsPerPage,
            'currentPage' => $currentPage,
            'DRUPAL_SITE' => DRUPAL_SITE,
            'departments' => self::departments(),
            'yesno'       => self::yesno(),
            'sorts'       => self::sorts(),
            'credits'     => $credits
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/reports/list.twig', $this->vars);
    }

    private static function departments(): array
    {
        $opts = [['value'=>'']];
        foreach (Ldap::$departments as $d=>$ou) { $opts[] = ['value'=>$d]; }
        return $opts;
    }

    private static function yesno(): array
    {
        return [
            ['value'=>''],
            ['value'=>1, 'label'=>'Yes'],
            ['value'=>0, 'label'=>'No' ]
        ];
    }

    private static function sorts(): array
    {
        $o    = [];
        $cols = [
            'r.path'     => 'Page',
            'error'      => 'Errors',
            'contrast'   => 'Contrast',
            'username'   => 'User',
            'department' => 'Department',
            'created'    => 'Scanned',
            'views'      => 'Views'
        ];

        foreach ($cols as $k=>$v) {
            $o[] = ['value' => "$k asc",  'label'=>"$v Asc" ];
            $o[] = ['value' => "$k desc", 'label'=>"$v Desc"];
        }
        return $o;
    }
}
