<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\List;

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
            'currentPage' => $currentPage
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/reports/list.twig', $this->vars);
    }
}
