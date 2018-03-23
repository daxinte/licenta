<?php
namespace Mapper;

use 
    Spot\Mapper,
    Symfony\Component\HttpFoundation\Request,
    Util\Util
    ;

class NestedCategory extends Mapper
{

    use \Traits\GenericMapper;

    public function tree() {
        //get the tree
        $q = "
            SELECT node.id, node.lft, node.rgt, COUNT(parent.title) - 1 depth, /*CONCAT( REPEAT( '_', (COUNT(parent.title) - 1) ),*/ node.title/*)*/ AS title
            FROM nested_categories AS node,
                    nested_categories AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            GROUP BY node.slug
            ORDER BY node.lft
        ";

        $conn = $this->connection();
        $arr = $conn->fetchAll( $q );

        $ret = [];
        foreach( $arr as $a ) {
            $ret[ $a['id'] ] = [
                'id' => $a['id'], 
                'depth' => $a['depth'], 
                'title' => $a['title'],
                'lft' => $a['lft'],
                'rgt' => $a['rgt'],
            ];
        }

        return $ret;

    }

    public function storeCategory( $data ) {

        $conn = $this->connection();


        $slug = Util::slugify( empty( $data['slug'] ) ? $data['title'] : $data['slug'] );
        $slug = $this->getNextSlug('nested_categories', $slug, empty($data['id']) ? 0 : $data['id']);

        if( empty( $data['parent_id'] ) ) {

            $conn->transactional( function( $conn ) use( $data, $slug ) {

                //get the max right values

                $width = $conn->fetchColumn( "SELECT COALESCE(MAX(`rgt`), 0) FROM nested_categories" );

                $q = "INSERT INTO nested_categories(`title`, `lft`, `rgt`, `slug`, `description`, `active`) VALUES ('".$data['title']."', '".( $width + 1 )."', '".( $width + 2)."', '$slug', '".$data['description']."', '".$data['active']."')";

                $conn->executeQuery( $q );

            } );

        } else {
            //get left and right values for the parent
            //get the parent lft and rght
            $conn->transactional( function( $conn ) use ( $data, $slug ) {

                $procedure = "
                    SELECT @myRight := rgt FROM nested_categories WHERE id = '{$data['parent_id']}';
                    UPDATE nested_categories SET rgt = rgt + 2 WHERE rgt >= @myRight;
                    UPDATE nested_categories SET lft = lft + 2 WHERE lft > @myRight;
                    INSERT INTO nested_categories(title, lft, rgt, slug, description, active) VALUES('{$data['title']}', @myRight , @myRight + 1, '$slug', '".$data['description']."', '".$data['active']."');
                    ";

                $conn->executeQuery( $procedure );
            } );
        }
    }

    public function deleteNode( $node ) {

        //delete the child nodes ( lft > nodeLft AND lft < nodeRgt )
        //delete the node
        //update the nodes after deleted node ( lft = lft - width, rgt = rgt - width )
        $this->config()->connection()->transactional( function( $conn ) use ($node) {

            $distance = $node->getWidth() + 1;

            //delete children
            $sql = "DELETE FROM nested_categories WHERE lft > :nodeLft AND rgt < :nodeRgt";
            $sth = $conn->prepare($sql);
            $sth->bindValue(':nodeLft', $node->lft);
            $sth->bindValue(':nodeRgt', $node->rgt);
            $sth->execute();

            //delete node
            $conn->delete('nested_categories', ['id' => $node->id]);

            //update the rest of the nodes
            $conn->executeUpdate("UPDATE nested_categories SET lft = lft - $distance WHERE lft > ?", [ $node->rgt ]);
            $conn->executeUpdate("UPDATE nested_categories SET rgt = rgt - $distance WHERE rgt > ?", [ $node->rgt ]);
            //echo "UPDATE nested_categories SET lft = lft - $distance, rgt = rgt - $distance WHERE lft > ?", $node->rgt;
            //die;
        });
    }

    //move 1 pos right
    public function moveRight( $node ) {

        //get the node to be replaced
        $destNode = $this->where(['lft' => $node->rgt + 1])->first();

        if( !empty( $destNode ) ) {

            return $this->moveLeft( $destNode );

        }
        return false;
    }

    public function moveLeft( $node ) {

        //get the node to be replaced
        $destNode = $this->where(['rgt' => $node->lft - 1])->first();

        if( !empty( $destNode ) ) {

            $conn = $this->config()->connection();

            $conn->transactional(function( $conn ) use ( $node, $destNode ) {

                //update node lft = destLft, rgt = lft + width
                $leftDist   = $node->lft - $destNode->lft;
                $rightDist  = $node->rgt - $destNode->rgt;

                $procedure = "
                    UPDATE nested_categories SET lft = 0 - lft, rgt = 0 - rgt WHERE lft >= {$destNode->lft} AND rgt <= {$destNode->rgt};
                    UPDATE nested_categories SET lft = lft - {$leftDist}, rgt = rgt - {$leftDist} WHERE lft >= {$node->lft} AND rgt <= {$node->rgt};
                    UPDATE nested_categories SET lft = (0 - lft + {$rightDist}), rgt = (0 - rgt + {$rightDist}) WHERE lft <= -{$destNode->lft} AND rgt >= -{$destNode->rgt};
                    ";

                $conn->executeQuery( $procedure );

            });

            return true;

        }
        return false;
    }

    private function getMaxCoords() {
        $q = "SELECT MIN(`lft`) minLft, MAX(`rgt`) maxRgt FROM nested_categories";
        $conn = $this->connection();

        return $conn->fetchAssoc($q);
    }
}
