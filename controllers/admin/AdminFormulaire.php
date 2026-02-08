<?php
require_once(_PS_MODULE_DIR_ . 'formulaire/classes/formulaire.class.php');

class AdminFormulaireController extends ModuleAdminController {
    public function __construct() {
        $this->table = 'formulaire_data';
        $this->className = 'FormulaireClass';
        $this->bootstrap = true;
        $this->identifier = FormulaireClass::$definition['primary'];
        

        $this->fields_list = [
            'id' => ['title' => 'ID', 'align' => 'center', 'class' => 'fixed-width-xs'],
            'nom' => ['title' => 'NOM', 'align' => 'left'],
            'email' => ['title' => 'EMAIL', 'align' => 'left']
        ];

        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        
        parent::__construct();
    }
    

    public function renderView() {
        $tplFile = _PS_MODULE_DIR_ . "formulaire/views/templates/admin/view.tpl";
        $tpl = $this->context->smarty->createTemplate($tplFile);

        $sql = new DbQuery();
        $sql->select('*')->from($this->table)->where('id=' . (int)Tools::getValue('id'));
        
        $data = Db::getInstance()->getRow($sql);
        $tpl->assign(['data' => $data, 'link' => $this->context->link->getAdminLink('AdminFormulaire')]);

        return $tpl->fetch();
    }

    public function renderForm() {
        $this->fields_form = [
            'legend' => ['title' => 'Message', 'icon' => 'icon-envelope'],
            'input' => [
                ['type' => 'text', 'label' => 'Nom', 'name' => 'nom', 'required' => true],
                ['type' => 'text', 'label' => 'Email', 'name' => 'email', 'required' => true],
                ['type' => 'textarea', 'label' => 'Message', 'name' => 'message', 'required' => true],
            ],
            'submit' => ['title' => 'Enregistrer', 'class' => 'btn btn-primary pull-right']
        ];
        return parent::renderForm();
    }
}