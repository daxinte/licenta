<?php
namespace Traits;

trait GenericMapper {

    function getListWhere($request, $aColumns, $extraWheres = [])
    {
        /**
         *  columns where we search
         */
        $sWhere = 'WHERE 1 ';

        if( !empty( $extraWheres ) ) {
            $sWhere .= " AND ".implode(' AND ', $extraWheres);
        }

        if($request->get('sSearch'))
        {
            $sWhere .= "AND (";
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                $sWhere .= $aColumns[$i][0].".".$aColumns[$i][1]." LIKE '%".$request->get('sSearch') ."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ') ';
        }
        return $sWhere;
    }

    public function getListOrder($request, $sColumns = array())
    {
        /**
         *  sortable columns in order
         */
        $sOrder = '';
        if(/*$request->get('iSortCol_0') AND */$request->get('sSortDir_0'))
        {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $request->get('iSortingCols') ) ; $i++ )
            {
                if ( $request->get('bSortable_'.intval($request->get('iSortCol_'.$i)) ) == "true" )
                {
                    if(count($sColumns[ intval( $request->get('iSortCol_'.$i) ) ]) > 1)
                    {
                        $sOrder .= (empty($sColumns[ intval( $request->get('iSortCol_'.$i) ) ][0]) ? "" : $sColumns[ intval( $request->get('iSortCol_'.$i) ) ][0].".").$sColumns[ intval( $request->get('iSortCol_'.$i) ) ][1]." ". $request->get('sSortDir_'.$i ) .", ";
                    }
                    else{
                        $sOrder .= $sColumns[ intval( $request->get('iSortCol_'.$i) ) ][0]." ".
                            $request->get('sSortDir_'.$i ) .", ";
                    }
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
        }
        return $sOrder;
    }

    function getNextSlug($table, $slug, $currRecordId = 0) {

        $qb = $this->config()->connection()->createQueryBuilder();
        $qb
            ->select('id', 'slug')
            ->from($table)
            ->where('slug = ?')
            ->setParameter(0, $slug)
            ;

        $records = $qb->execute()->fetchAll();

        if(
            empty($records)
            || ( is_array($records) && count($records) == 1 && $records[0]['id'] == $currRecordId )
        ) return $slug;
        else {
            $qb = $this->config()->connection()->createQueryBuilder();
            $qb
                ->select('id', 'slug')
                ->from($table)
                ->where('slug REGEXP ?')
                ->setParameter(0, $slug.'($|-[0-9]*)$')
                ->orderBy('slug', 'ASC')
                ;

            $records = $qb->execute()->fetchAll();


            $slugLen = strlen($slug);
            $positions = array();

            foreach($records as $rec) {
                $slugPos = substr($rec['slug'], $slugLen+1, strlen($rec['slug']) - $slugLen);
                if (!empty($slugPos)) $positions[] = $slugPos;
            }

            $nextPos = 1;

            for($i = 0; $i < count($positions); $i++) {
                if($nextPos == $positions[$i]) $nextPos ++;
            }

            return $slug.'-'.$nextPos;

        }
    }

}
