<?php
namespace Services;

//use Forms\Page;
use 
    Symfony\Component\Validator\Constraints as Assert,
    Entity\Category
    ;

class Forms {

    static function getProductForm($app, $data) {

        $data['active'] = empty($data['active']) ? false : true;

        //categories, brands
        //$sections = $app['spot']->mapper('Entity\Category')->getForSelect('section');
        $tree   = $app['spot']->mapper('Entity\NestedCategory')->tree();
        $categories = [];
        foreach( $tree as $node ) {
            $label = empty( $node['depth'] ) ? $node['title'] : str_repeat('__', $node['depth']).$node['title'];
            $categories[ $node['id'] ] = $label;
        }

        $brands = $app['spot']->mapper('Entity\Brand')->getForSelect();

        $form = $app['form.factory']->createBuilder('form', $data)
            ->add('id', 'hidden')
            ->add('title', 'text', array(
                'label' =>  'Denumire',
                'attr'  =>  array('placeholder' => 'Denumirea produsului')
                ,'constraints' => array(
                    new Assert\NotBlank(), 
                    new Assert\Length(array('min' => 2)
                ))
            ))
            ->add('code', 'text', array(
                'label' =>  'Cod',
                'attr'  =>  array('placeholder' => 'Codul produsului')
            ))
            ->add('price', 'money', array(
                'label' =>  'Pret',
                'attr'  =>  array('placeholder' => 'Pretul produsului'),
                'currency' =>  'RON',
            ))
            ->add('stock', 'number', array(
                'label' =>  'Stoc',
                'attr'  =>  array('placeholder' => 'Stocul produsului'),
            ))
            //->add('section_id', 'choice', array(
            //    'label'         =>  'Sectiune',
            //    'placeholder'   =>  '--Selectati--',
            //    'choices'   => $sections
            //))
            ->add('category_id', 'choice', array(
                'label'         =>  'Categorie',
                'placeholder'   =>  '--Selectati--',
                'choices'   => $categories
            ))
            ->add('brand_id', 'choice', array(
                'label'         =>  'Brand / Producator',
                'placeholder'   =>  '--Selectati--',
                'choices'   => $brands
            ))
            ->add('slug', 'text', array(
                'label' =>  'URL Preferential',
                'attr'  =>  array('placeholder' => 'URL produs')
            ))
            ->add('description', 'textarea', array(
                'label' =>  'Descriere',
                'attr'  =>  array('placeholder' => 'Descrierea produsului')
            ))
            ->add('active', 'checkbox', array(
                'label'     => 'Activ',
            ))
            ->add('tags', 'text', array(
                'label' =>  'Taguri',
                'attr'  =>  array('placeholder' => 'Taguri')
            ))
            ->add('meta_title', 'text', array(
                'label' =>  'Meta title',
            ))
            ->add('meta_keywords', 'text', array(
                'label' =>  'Meta keywords',
            ))
            ->add('meta_description', 'textarea', array(
                'label' =>  'Meta description',
            ))
            //->add('_token')
            ->add('submit', 'submit', array(
                'label'     => 'Salveaza'
                ,'attr'     =>  array('class' => 'btn btn-primary')
            ))
            ->getForm();

        return $form;
    }

    public function getSettingsForm($app, $arrSettings) {

        $dataArr = array();
        foreach($arrSettings as $s) {
            $dataArr[$s['name']] = $s['value'];
        }

        $form =
            $app['form.factory']->createBuilder('form', $dataArr);


        foreach($dataArr as $sName => $sVal) {
            $form->add($sName, 'text', array());
        }

        $form
            ->add('_token')
            ->add('submit', 'submit', array(
                'label'     => 'Salveaza'
                ,'attr'     =>  array('class' => 'btn btn-primary')
            ));

        return $form->getForm();

    }

    static function getSectionForm($app, $data) {

        $data['active'] = empty($data['active']) ? false : true;
        $form = $app['form.factory']->createBuilder('form', $data)
            ->add('id', 'hidden')
            ->add('title', 'text', array(
                'label' =>  'Nume',
                'attr'  =>  array('placeholder' => 'Titlu')
                ,'constraints' => array(
                    new Assert\NotBlank(), 
                    new Assert\Length(array('min' => 2)
                ))
            ))
            //->add('Parent', 'choice', array(
            //    'label' =>  'Parinte',
            //))
            ->add('slug', 'text', array(
                'label' =>  'URL',
                'attr'  =>  array('placeholder' => 'URL preferential')
            ))
            ->add('description', 'textarea', array(
                'label' =>  'Descriere',
                'attr'  =>  array('placeholder' => 'Descriere')
            ))
            ->add('order_no', 'text', array(
                'label' =>  'Pozitie',
                'attr'  =>  array('placeholder' => 'Pozitie')
            ))
            ->add('active', 'checkbox', array(
                'label'     => 'Activa',
            ))
            ->add('submit', 'submit', array(
                'label'     => 'Salveaza'
                ,'attr'     =>  array('class' => 'btn btn-primary')
            ))
            ->getForm();

        return $form;
    }

    static function getCategoryForm($app, $data) {

        $data['active'] = empty($data['active']) ? false : true;
        $dbParents      = $app['spot']->mapper('Entity\Category')->parents();

        $parents = [];
        foreach( $dbParents as $arr ) {
            $parents[$arr['id']] = $arr['title'];
        }


        $form = $app['form.factory']->createBuilder('form', $data)
            ->add('id', 'hidden')
            ->add('title', 'text', array(
                'label' =>  'Nume',
                'attr'  =>  ['placeholder' => 'Nume']
                ,'constraints' => array(
                    new Assert\NotBlank(['message'  => 'Acest camp nu poate fi gol!']), 
                    new Assert\Length(['min' => 2, 'minMessage' => 'Minim 2 caractere'])
                )
            ))
            ->add('parent_id', 'choice', array(
                'label'         =>  'Categorie parinte',
                'placeholder'   =>  '--Selectati--',
                'choices'   => $parents
            ))
            ->add('slug', 'text', array(
                'label' =>  'URL',
                'attr'  =>  array('placeholder' => 'URL preferential')
            ))
            ->add('description', 'textarea', array(
                'label' =>  'Descriere',
                'attr'  =>  array('placeholder' => 'Descriere')
            ))
            ->add('order_no', 'text', array(
                'label' =>  'Pozitie',
                'attr'  =>  array('placeholder' => 'Pozitie')
            ))
            ->add('active', 'checkbox', array(
                'label'     => 'Activa',
            ))
            ->add('submit', 'submit', array(
                'label'     => 'Salveaza'
                ,'attr'     =>  array('class' => 'btn btn-primary')
            ))
            ->getForm();

        return $form;
    }

    static function getNestedCategoryForm($app, $data) {

        $data['active'] = empty($data['active']) ? false : true;
        $dbCats = $app['spot']->mapper('Entity\NestedCategory')->tree();

        $tree = [];
        foreach($dbCats as $catArr) {

            $tree[ $catArr['id'] ] = str_repeat('__', $catArr['depth']).$catArr['title'];
        }

        $form = $app['form.factory']->createBuilder('form', $data)
            ->add('id', 'hidden')
            ->add('title', 'text', array(
                'label' =>  'Nume',
                'attr'  =>  ['placeholder' => 'Nume']
                ,'constraints' => array(
                    new Assert\NotBlank(['message'  => 'Acest camp nu poate fi gol!']), 
                    new Assert\Length(['min' => 2, 'minMessage' => 'Minim 2 caractere'])
                )
            ))
            ->add('parent_id', 'choice', array(
                'label'         =>  'Categorie parinte',
                'placeholder'   =>  '--Selectati--',
                'choices'   => $tree
            ))
            ->add('slug', 'text', array(
                'label' =>  'URL',
                'attr'  =>  array('placeholder' => 'URL preferential')
            ))
            ->add('description', 'textarea', array(
                'label' =>  'Descriere',
                'attr'  =>  array('placeholder' => 'Descriere')
            ))
            /*->add('order_no', 'text', array(
                'label' =>  'Pozitie',
                'attr'  =>  array('placeholder' => 'Pozitie')
            ))*/
            ->add('active', 'checkbox', array(
                'label'     => 'Activa',
            ))
            ->add('submit', 'submit', array(
                'label'     => 'Salveaza'
                ,'attr'     =>  array('class' => 'btn btn-primary')
            ))
            ->getForm();

        return $form;
    }

    static function getBrandForm($app, $data) {

        $data['active'] = empty($data['active']) ? false : true;
        $form = $app['form.factory']->createBuilder('form', $data)
            ->add('id', 'hidden')
            ->add('title', 'text', array(
                'label' =>  'Nume',
                'attr'  =>  array('placeholder' => 'Nume')
                ,'constraints' => array(
                    new Assert\NotBlank(), 
                    new Assert\Length(array('min' => 2)
                ))
            ))
            //->add('Parent', 'choice', array(
            //    'label' =>  'Parinte',
            //))
            ->add('slug', 'text', array(
                'label' =>  'URL',
                'attr'  =>  array('placeholder' => 'URL preferential')
            ))
            ->add('order_no', 'text', array(
                'label' =>  'Pozitie',
                'attr'  =>  array('placeholder' => 'Pozitie')
            ))
            ->add('active', 'checkbox', array(
                'label'     => 'Activa',
            ))
            ->add('submit', 'submit', array(
                'label'     => 'Salveaza'
                ,'attr'     =>  array('class' => 'btn btn-primary')
            ))
            ->getForm();

        return $form;
    }

    static function getUserRegisterForm($app, $data) {

        $mapper = $app['spot']->mapper('Entity\County');
        $counties = $mapper->getForSelect();

        $form = $app['form.factory']->createBuilder('form', $data, ['csrf_protection' => false])
                ->add('name', 'text', [
                    'label'         => 'Nume',
                    'required'      => false,
                    'constraints'   => [
                        new Assert\NotBlank(['message' => 'Va rugam sa introduceti un nume']), 
                        new Assert\Length(['min' => 3, 'minMessage' => 'Trebuie sa contina cel putin {{ limit }} caractere'])
                    ]
                ])
                ->add('email', 'email', [
                    'label' => 'Email',
                    'required'  => false,
                    'constraints'   => [
                        new Assert\NotBlank(['message' => 'Va rugam sa introduceti adresa de email']), 
                        new Assert\Email(['message' => 'Adresa de e-mail nu este valida'])
                    ]
                ])
                ->add('address', 'text', [
                    'label'     => 'Adresa',
                    'required'  => false,
                    'constraints'   => [
                        new Assert\NotBlank(['message' => 'Va rugam sa introduceti adresa']), 
                    ]
                ])
                ->add('postal_code', 'text', [
                    'required'  => false,
                    'label' => 'Cod Postal',
                    'constraints'   => [
                        new Assert\NotBlank(['message' => 'Va rugam sa introduceti codul postal']), 
                    ]
                ])
                ->add('county_id', 'choice', array(
                    'label'         =>  'Judet',
                    'required'      => false,
                    'placeholder'   =>  '--Selectati--',
                    'choices'       => $counties,
                    'constraints'   => [
                        new Assert\NotBlank(['message' => 'Va rugam sa alegeti un judet']), 
                    ]
                ))
                ->add('password', 'password', [
                    'label' => 'Parola',
                    'required'      => false,
                    'constraints'   => [
                        new Assert\NotBlank(['message' => 'Va rugam sa introduceti o parola']), 
                        new Assert\Length(['min' => 6, 'minMessage' => 'Parola trebuie sa contina cel putin {{ limit }} caractere']),
                        new Assert\Regex(['pattern' => '/\d/', 'message' => 'Parola aleasa trebuie sa contina cel putin un numar'])
                    ]
                ])
                ->add('save', 'submit', [
                    'label' => 'Inregistrare'
                ])
                ->getForm()
                ;

        return $form;

    }
}
