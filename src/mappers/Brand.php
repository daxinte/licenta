<?php
namespace Mapper;

use 
    Spot\Mapper,
    Symfony\Component\HttpFoundation\Request
    ;

class Brand extends Mapper
{
    use \Traits\GenericMapper;

    function getForSelect() {

        $results = $this->all()->order(['order_no' => 'ASC'])->toArray();

        $ret = [];
        foreach( $results as $r ) {
            $ret[$r['id']] = $r['title'];
        }

        return $ret;

    }

    public function getForDataTable( Request $request ) {

        $limitStart = $request->get('iDisplayStart') ? $request->get('iDisplayStart') : 0;
        $limitLength = $request->get('iDisplayLength') ? $request->get('iDisplayLength') : 12;

        if($limitLength < 0) $limitLength = 100;

        $aColumns = array(
                         array('b', 'title'),
                         array('b', 'slug')
                    );
        $sWhere = $this->getListWhere($request, $aColumns);
        $sOrder = $this->getListOrder($request, array(array('b', 'title'), array('b', 'order_no'), array('b', 'active'), null));

        $columns = "
                    b.id,
                    b.title,
                    b.order_no,
                    b.active

                    ";
        $from    = "
                    brands b
            ";

        $q = "SELECT
                $columns
              FROM 
                $from
                $sWhere ";
        $q .= " 
                $sOrder
                LIMIT $limitStart, $limitLength
              ";

        $cQ = "SELECT
                COUNT(DISTINCT b.id)
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

            $row_arr = array();
            $row_arr[] = $res['title'];
            $row_arr[] = $res['order_no'];
            $row_arr[] = $res['active'];
            $row_arr[] = $res['id'];
            $ret_arr[] = $row_arr;

        }

        return array(
            'totalRowsFound' => $allRecs
            ,'results' => $ret_arr
        );
    }
}
