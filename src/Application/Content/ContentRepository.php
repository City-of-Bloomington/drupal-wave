<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Content;

use Application\Database;
use Application\PdoRepository;

class ContentRepository extends PdoRepository
{
    public function __construct()
    {
        $this->pdo    = Database::getConnection('drupal');
        $this->table = 'node_field_data';
    }

    public function pages(string $search): array
    {
        $sql = "select n.nid,
                       n.type,
                       n.title,
                       a.alias
                from node_field_data n
                join path_alias      a on a.path=concat('/node/', n.nid)
                join (
                    select   entity_id,  body_value               as content from node__body
                    union
                    select   entity_id,  field_aside_value        as content from node__field_aside
                    union
                    select   entity_id,  field_details_value      as content from node__field_details
                    union
                    select   entity_id,  field_related_links_uri  as content from node__field_related_links
                    union
                    select   entity_id,  field_call_to_action_uri as content from node__field_call_to_action
                    union
                    select n.entity_id,l.field_info_link_uri      as content
                    from paragraph__field_info_link l
                    join paragraphs_item_field_data p on p.id=l.entity_id
                    join node__field_info_links     n on p.parent_id=n.field_info_links_target_id
                    union
                    select a.entity_id,f.filename
                    from node__field_attachments a
                    join file_managed            f on f.fid=a.field_attachments_target_id
                    where a.field_attachments_display=1 and a.deleted=0
                ) x on n.nid=x.entity_id
                where (x.content like ? or x.content like ?)";
        $query   = $this->pdo->prepare($sql);
        $encoded = str_replace('%', '\%', rawurlencode($search));
        $query->execute(["%$search%", "%$encoded%"]);
        $result  = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function grackle_results(string $path): array
    {
        $sql = "select g.*,
                    case when left(g.url, 46)='https://bloomington.in.gov/sites/default/files'
                        then if(f.fid, '', 'deleted') else ''
                    end as status
                from      wave.grackle_results g
                left join drupal.file_managed  f on f.uri=replace(g.url, 'https://bloomington.in.gov/sites/default/files', 'public:/')
                where g.path=?";
        $qq  = $this->pdo->prepare($sql);
        $qq->execute([$path]);
        $res = $qq->fetchAll(\PDO::FETCH_ASSOC);
        return $res;
    }
}
