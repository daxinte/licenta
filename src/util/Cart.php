<?php
namespace Util;

class Cart {

    //cart items - simple array
    protected $items = [];

    public function addItem( \Entity\Product $item, $quantity = 1) {
        if(array_key_exists( $item->id, $this->items )) {
            $this->items[$item->id]['quantity'] += $quantity;
        } else {
            $this->items[$item->id] = [
                'item'      => $item,
                'quantity'  => $quantity
            ];
        }
    }

    public function removeItem($item) {

    }

    //return all the items in cart
    public function getItems() {
        return $this->items;
    }

    //remove all the items in cart
    public function reset() {
        $this->items = [];

        return $this;
    }

    //return the totals of the current cart
    public function getTotals() {
        $totals = 0;
        foreach( $this->items as $item ) {
            $totals += ($item['item']->price * $item['quantity']);
        }
        return $totals;
    }

    public function getItemsNo() {
        return count( $this->items );
    }

}
