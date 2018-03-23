<?php
namespace Mapper;

use 
    Spot\Mapper,
    Symfony\Component\HttpFoundation\Request
    ;

class Section extends Mapper
{

    use \Traits\GenericMapper;

    public function getForDataTable( Request $request ) {

        $limitStart = $request->get('iDisplayStart') ? $request->get('iDisplayStart') : 0;
        $limitLength = $request->get('iDisplayLength') ? $request->get('iDisplayLength') : 12;

        if($limitLength < 0) $limitLength = 100;

        $aColumns = array(
                         array('s', 'title')
                        ,array('s', 'description')
                    );
        $sWhere = $this->getListWhere($request, $aColumns);
        $sOrder = $this->getListOrder($request, array(array('s', 'title'), null, array('s', 'order_no'), array('s', 'active'), null));

        $columns = "
                    s.id,
                    s.title,
                    s.description,
                    s.order_no,
                    s.active

                    ";
        $from    = "
                    sections s
            ";

        $q = "SELECT
                $columns
              FROM 
                $from
                $sWhere ";
        $q .= " 
                GROUP BY s.id
                $sOrder
                LIMIT $limitStart, $limitLength
              ";

        $cQ = "SELECT
                COUNT(DISTINCT s.id)
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
            $row_arr[] = $description;
            $row_arr[] = $res['active'];//empty($res['active']) ? '<span class="label label-default">Nu</span>' : '<span class="label label-primary">Da</span>';
            $row_arr[] = empty($res['tags']) ? '-' : $res['tags'];
            $row_arr[] = $res['id'];
            $ret_arr[] = $row_arr;

        }

        return array(
            'totalRowsFound' => $allRecs
            ,'results' => $ret_arr
        );
    }

}
