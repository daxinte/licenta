<?php 
namespace Util;

class DataTable {
    /* columns of the grid */
    public $columns, $ajaxUrl, $tableTitle, $order, $customFilterFields, $funcJs;

    public function __construct($config = array()) {

        $this->customFilterFields = array();
        $this->setColumns($config['columns']);
        $this->ajaxUrl      = $config['ajax_url'];
        $this->tableTitle   = $config['table_title'];
        $this->order		= empty($config['order']) ? null : $config['order'];
    }

    public function setColumns($columns) {
        $this->columns = $columns;
    }

    public function renderTable() {
        $table = "
                    <h3>".$this->tableTitle."</h3>
                    <div class='panel-body'>
                    ";

        $filtersStr = '';
        if(!empty($this->customFilterFields)) {
            $filtersStr .= "<div class='row'>";
            $filtersStr .= "<div class='col-xs-6'>";
            $filtersStr .= "<form class='form-horizontal' role='form' onsubmit='javascript:return false'>";
            foreach($this->customFilterFields as $cfArr) {
                $filtersStr .= $this->renderFilterField($cfArr);
            }
            $filtersStr .= '</form>';
            $filtersStr .= '</div>';
            $filtersStr .= '</div>';
        }

        $table .= $filtersStr;
        $table .= "
                        <table class='table table-bordered table-striped table-responsive datatable' id='datatable' cellspacing='0' width='100%'>
                            <thead>
                            <tr>";

        foreach($this->columns as $colTitle => $settings) {
            $table .= "<th><div class='th'>$colTitle</div></th>";
        }

        $table .= "</tr>
            </thead>
            <tbody></tbody>
        </table> ";

        if(!empty($this->customButtons)) {
            foreach($this->customButtons as $btnArr) {
                $table .= "<button class='btn ".($btnArr['cssClasses'])." ".($btnArr['handlerClass'])."' ><i class='".$btnArr['iClasses']."'></i>&nbsp;".$btnArr['value']."</button>";
            }
        }

        $table .="</div><!-- end panel body //-->
        ";

        return $table;
    }

    public function renderJsSettings() {
        $js = "
            <script language=\"javascript\">
                var oTable = null;
                $(document).ready(function() {
                    var oTable = $('#datatable').DataTable({
                        'language': {
                            'url': '/js/dataTables.romanian.json'
                        }
                        ,'aoColumns': [";
                $counter = 0;
                $count = count($this->columns);
                foreach($this->columns as $colTitle => $settings) {
                    $js .= "{'bSortable' : ".(empty($settings['sortable']) ? 'false': 'true')."}";

                    if($counter < $count-1) $js .= ", ";

                    $counter ++;
                }
                        $js .= "]
                        ,'bServerSide' : true
                        ,'bProcessing' : true
                        ,'sAjaxSource' : '".$this->ajaxUrl."'";
		 if(!empty($this->order)) {
                $js .=  ",'order': [[".$this->order[0].", '".$this->order[1]."']]";
		 }
        if(!empty($this->hasCustomFilter)) {
            $js .= "
                        ,'fnServerParams': customFilter 
                ";
        }
            $js .= " }); });";


        if(!empty($this->funcJs)) {
            $js .= $this->funcJs;
        }


        if(!empty($this->customButtons)) {
            foreach($this->customButtons as $btnArr) {
                if(!empty($btnArr['eventHandler'])) {
                    $js .= " $('.{$btnArr['handlerClass']}').on('click', {$btnArr['eventHandler']}); ";
                }
            }
        }
        $js .= "</script>
        ";

        return $js;
    }

    /**
     * add item to filter
     **/
    function addFilterField($fieldArr) {
        $this->customFilterFields[] = $fieldArr;
    }

    function renderFilterField($cfArr) {
        $filtersStr  = "<div class='form-group'>";
        $filtersStr .= "<label for='".$cfArr['inputId']."' class='col-sm-4 control-label'>".$cfArr['label']."</label>";
        $filtersStr .= "<div class='col-sm-8'>";
        switch($cfArr['type']) {
            case 'text':
                $filtersStr .= "<input class='form-control' id='".$cfArr['inputId']."'/>";
            break;
            case 'choices': 
                $filtersStr .= "<select id='".$cfArr['inputId']."'>";
                $filtersStr .= "<option value=''>--Nici o valoare--</option>";
                foreach($cfArr['choices'] as $obj) {
                    $filtersStr .= "<option value='".$obj->getId()."'>".$obj."</option>";
                }
                $filtersStr .= "</select>";
            break;
        }
        $filtersStr .= "</div>";
        $filtersStr .= "</div>";

        return $filtersStr;
    }

    function setFuncJs($funcJs) {
        $this->funcJs = $funcJs;
    }
}
