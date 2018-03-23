<?php
namespace Services;

class Util {

    static function renderNestedTree($tree, $currDepth = -1, $options) {

        if( empty( $tree ) ) return;

        $currNode = array_shift($tree);
        $result = '';
        // Going down?
        if ($currNode['depth'] > $currDepth) {
            // Yes, prepend <ul>
            $result .= '<ul>';
        }
        // Going up?
        if ($currNode['depth'] < $currDepth) {
            // Yes, close n open <ul>
            $result .= str_repeat('</ul>', $currDepth - $currNode['depth']);
        }

        // Always add the node
        $result .= $options['nodeDecorator']($currNode);
        // Anything left?
        if (!empty($tree)) {
        // Yes, recurse
            $result .=  self::renderNestedTree($tree, $currNode['depth'], $options);
        }
        else {
            // No, close remaining <ul>
            $result .= str_repeat('</ul>', $currNode['depth'] + 1);
        }

        return $result;

    }

}
