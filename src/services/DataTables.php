<?php
namespace Services;

use Util\DataTable;

class DataTables {

    static function getProductsGrid($router, $gridParams = array(), $gridExtraParams = array()) {
        $gridO = new DataTable(array(
                 'columns'          => array(
                    'Denumire'      => array('sortable' => true)
                    ,'Categorie'    => array('sortable' => false)
                    ,'Activ'        => array('sortable' => true)
                    ,'Taguri'       => array('sortable' => false)
                    ,'Pret'         => array('sortable' => true)
                    ,'&nbsp;'       => array('sortable' => false)
                 )
                ,'ajax_url'     =>  $router->generate('admin_products', $gridExtraParams)
                ,'table_title'  =>  $gridParams['pageTitle']
                ,'order'        =>  array(0, 'DESC')
            ));

        return $gridO;
    }

    static function getSectionsGrid($router, $gridParams = array(), $gridExtraParams = array()) {
        $gridO = new DataTable(array(
                 'columns'          => array(
                    'Titlu'         => array('sortable' => true)
                    ,'Descriere'    => array('sortable' => false)
                    ,'Pozitie'      => array('sortable' => true)
                    ,'Activ'        => array('sortable' => true)
                    ,'&nbsp;'       => array('sortable' => false)
                 )
                ,'ajax_url'     =>  $router->generate('admin_sections', $gridExtraParams)
                ,'table_title'  =>  $gridParams['pageTitle']
                ,'order'        =>  array(0, 'DESC')
            ));

        return $gridO;
    }

    static function getBrandsGrid($router, $gridParams = array(), $gridExtraParams = array()) {
        $gridO = new DataTable(array(
                 'columns'          => array(
                    'Titlu'         => array('sortable' => true),
                    'Nr. Ordine'    => array('sortable' => true),
                    'Activ'         => array('sortable' => true),
                    '&nbsp;'       => array('sortable' => false)
                 )
                ,'ajax_url'     =>  $router->generate('admin_brands', $gridExtraParams)
                ,'table_title'  =>  $gridParams['pageTitle']
                ,'order'        =>  array(0, 'DESC')
            ));

        return $gridO;
    }

    static function getCategoriesGrid($router, $gridParams = array(), $gridExtraParams = array()) {
        $gridO = new DataTable(array(
                 'columns'          => array(
                    'Titlu'         => array('sortable' => true)
                    ,'Parinte'      => array('sortable' => true)
                    ,'Descriere'    => array('sortable' => false)
                    ,'Pozitie'      => array('sortable' => true)
                    ,'Activ'        => array('sortable' => true)
                    ,'&nbsp;'       => array('sortable' => false)
                 )
                ,'ajax_url'     =>  $router->generate('admin_categories', $gridExtraParams)
                ,'table_title'  =>  $gridParams['pageTitle']
                ,'order'        =>  array(0, 'DESC')
            ));

        return $gridO;
    }
}
