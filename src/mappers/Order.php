<?php
namespace Mapper;

use 
    Spot\Mapper,
    Symfony\Component\HttpFoundation\Request
    ;

class Order extends Mapper
{
    public function getFullWithImages($id) {

        $q = "
            SELECT
                o.date,
                p.title,
                p.code,
                #p.description,
                oi.price,
                oi.quantity,
                i.name as img_name
            FROM
                orders o
                JOIN order_items oi ON oi.order_id = o.id
                LEFT JOIN products p ON p.id = oi.product_id
                LEFT JOIN images i ON i.product_id = i.product_id
            WHERE
                o.id = $id

            ";

        $dbal = $this->config()->connection();
        $stmt = $dbal->prepare($q);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $results = $stmt->fetchAll();

        return $results;
    }
}
