<?php
namespace Mapper;

use 
    Spot\Mapper,
    Symfony\Component\HttpFoundation\Request
    ;

class Category extends Mapper
{

    use \Traits\GenericMapper;

    public function getSectionsForDataTable( Request $request ) {
        return $this->getForDataTable( $request, 'section' );
    }

    public function getCategoriesForDataTable( Request $request ) {
        return $this->getForDataTable( $request, 'category' );
    }

    public function getForDataTable( Request $request, $categoryType ) {

        $limitStart = $request->get('iDisplayStart') ? $request->get('iDisplayStart') : 0;
        $limitLength = $request->get('iDisplayLength') ? $request->get('iDisplayLength') : 12;

        if($limitLength < 0) $limitLength = 100;

        $aColumns = array(
                         array('c', 'title')
                        ,array('c', 'description')
                    );

        $orderArr = [['c', 'title']];


        if( $categoryType == 'category' ) {

            $aColumns[] = ['pc', 'title'];
            $orderArr[] = ['pc', 'title']; 

        }

        $orderArr = array_merge($orderArr, [ null, ['c', 'order_no'], ['c', 'active']]);

        $extraWheres = ['c.type = \''.$categoryType.'\''];

        $sWhere = $this->getListWhere($request, $aColumns, $extraWheres);
        $sOrder = $this->getListOrder($request, $orderArr);

        $columns = "
                    c.id,
                    c.title,
                    c.description,
                    c.order_no,
                    c.active,
                    pc.title parent_category

                    ";
        $from    = "
                    categories c
                    LEFT JOIN categories pc ON pc.id = c.parent_id
            ";

        $q = "SELECT
                $columns
              FROM 
                $from
                $sWhere ";
        $q .= " 
                GROUP BY c.id
                $sOrder
                LIMIT $limitStart, $limitLength
              ";

        $cQ = "SELECT
                COUNT(DISTINCT c.id)
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

            $description = trim(strip_tags($res['description']));
            if(strlen($description) > 128) $description = substr($description, 0, 128).'...';

            $row_arr = array();
            $row_arr[] = $res['title'];

            if( $categoryType == 'category' ) {
                $row_arr[] = empty($res['parent_category']) ? '-' : $res['parent_category'];
            }

            $row_arr[] = $description;
            $row_arr[] = $res['order_no'];
            $row_arr[] = $res['active'];//empty($res['active']) ? '<span class="label label-default">Nu</span>' : '<span class="label label-primary">Da</span>';
            $row_arr[] = $res['id'];
            $ret_arr[] = $row_arr;

        }

        return array(
            'totalRowsFound' => $allRecs
            ,'results' => $ret_arr
        );
    }

    function parents() {
        $parents = $this
            ->all()
            ->where([ 'parent_id =' => null, 'type' => 'category' ])
            ->order([  'title'  => 'ASC' ])
            ->toArray()
            ;

        return $parents;
    }


    function getForSelect($type = 'category') {

        $q = "
                SELECT
                    IFNULL(c.id, c2.id) id,
                    IF( c2.id IS NULL, c.title, CONCAT_WS(' / ', c2.title, c.title) ) title
                FROM
                    categories c
                    LEFT JOIN categories c2 ON c2.id = c.parent_id
                WHERE
                    c.type = '$type'
                ORDER BY
                    c2.id ASC, c.parent_id, c.order_no, c.title

        ";

        $dbal = $this->config()->connection();
        $stmt = $dbal->prepare($q);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $results = $stmt->fetchAll();

        $ret = [];
        foreach( $results as $r ) {
            $ret[$r['id']] = $r['title'];
        }

        return $ret;

    }
}
