<?php
class FormulaireClass extends ObjectModel {
     public $id;
     public $nom;
     public $email;
     public $message;
     public $date_add;

     
    public static $definition = [
        'table' => 'formulaire_data',
        'primary' => 'id',
        'fields' => [
            'nom' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 255],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 255],
            'message' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
        ],
    ];
}