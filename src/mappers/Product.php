<?php
namespace Mapper;

use 
    Spot\Mapper,
    Symfony\Component\HttpFoundation\Request
    ;

class Product extends Mapper
{

    use \Traits\GenericMapper;

    public function getBySlug($slug) {
        return $this->where(['slug' => $slug])->with('Images')->first();
    }

    public function getForDataTable( Request $request ) {

        $limitStart = $request->get('iDisplayStart') ? $request->get('iDisplayStart') : 0;
        $limitLength = $request->get('iDisplayLength') ? $request->get('iDisplayLength') : 12;

        if($limitLength < 0) $limitLength = 100;

        $aColumns = [
                         ['p', 'title']
                        ,['p', 'description']
                        ,['p', 'tags']
                    ];
        $sWhere = $this->getListWhere($request, $aColumns);
        $sOrder = $this->getListOrder($request, [ ['p', 'title'], null, ['p', 'active'], null, ['p', 'price'], null]);

        $columns = "
                    p.id,
                    p.title,
                    (
                        SELECT 
                            GROUP_CONCAT(ancestor.title SEPARATOR ' / ') path
                        FROM `nested_categories` child, `nested_categories` ancestor 
                        WHERE 
                            child.lft >= ancestor.lft 
                            AND child.lft <= ancestor.rgt 
                            AND child.id = p.category_id 
                        ORDER BY ancestor.lft ) category, 
                    CAST(p.price AS DECIMAL (6, 2)) price,
                    p.active,
                    p.tags

                    ";
        $from    = "
                    products p
                    LEFT JOIN brands b ON b.id = p.brand_id
            ";

        $q = "SELECT
                $columns
              FROM 
                $from
                $sWhere ";
        $q .= " 
                GROUP BY p.id
                $sOrder
                LIMIT $limitStart, $limitLength
              ";

        $cQ = "SELECT
                COUNT(DISTINCT p.id)
              FROM 
                $from
                $sWhere 
                ";

        $dbal = $this->config()->connection();
        $stmt = $dbal->prepare($cQ);
        $stmt->execute();
        $allRecs  = $stmt->fetchColumn(0);

        $stmt = $dbal->prepare($q);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $results = $stmt->fetchAll();


        $ret_arr = array();

        foreach ($results as $res)
        {

            //$description = trim(strip_tags($res['description']));
            //if(strlen($description) > 128) $description = substr($description, 0, 128).'...';

            $row_arr = array();
            $row_arr[] = $res['title'];
            $row_arr[] = empty($res['category']) ? '-' : $res['category'];
            $row_arr[] = $res['active'];//empty($res['active']) ? '<span class="label label-default">Nu</span>' : '<span class="label label-primary">Da</span>';
            $row_arr[] = empty($res['tags']) ? '-' : $res['tags'];
            $row_arr[] = $res['price'];
            $row_arr[] = $res['id'];
            $ret_arr[] = $row_arr;

        }

        return array(
            'totalRowsFound' => $allRecs
            ,'results' => $ret_arr
        );
    }

}
