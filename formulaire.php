<?php 
if(!defined('_PS_VERSION_')){
    exit;
}

class Formulaire extends Module {
    public function __construct() {
        $this->name = 'formulaire';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'abdy';
        $this->logo='logo.png';
        parent::__construct();
        $this->bootstrap=true;
        $this->ps_versions_compliancy = ["min" => "1.7", "max" => _PS_VERSION_];
        $this->displayName = $this->l('Formulaire');
        $this->description = $this->l('Simple formulaire de contact avec gestion Admin');
    }

    public function install() {
        return  parent::install() && 
                $this->installDb() &&
                $this->installTab() && 
                $this->registerHook('displayHome');

   }

    public function uninstall() {
        return parent::uninstall() && $this->uninstallTab(); 
    }
    

    private function installDb()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."formulaire_data` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nom` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `date_add` DATETIME NOT NULL, 
        PRIMARY KEY (`id`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;";

        return Db::getInstance()->execute($sql);
    }

    private function installTab() {
        $tab = new Tab();
        $tab->class_name = 'AdminFormulaire'; 
        $tab->module = $this->name; 
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentCustomerThreads') ;
                

        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = 'Messages Formulaire'; 
        }
        return $tab->add(); 
    }

    private function uninstallTab() {
        $id_tab = (int)Tab::getIdFromClassName('AdminFormulaire'); 
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete(); 
        }
        return false;
    }

    public function getContent() {
    $output = '';

    
    if (Tools::isSubmit('submitConfigure')) {
        $TITLE = strval(Tools::getValue('TITLE'));
        $boutton = (int)Tools::getValue('boutton');

        if(!empty($TITLE)){
            Configuration::updateValue('TITLE', $TITLE);
            Configuration::updateValue('BOUTTON', $boutton);
            
            if (isset($_FILES['FORM_BANNER_IMAGE']) && !empty($_FILES['FORM_BANNER_IMAGE']['tmp_name'])) {
                $file_name = str_replace(' ', '_', $_FILES['FORM_BANNER_IMAGE']['name']);
                $destination = _PS_MODULE_DIR_ . $this->name . '/views/img/' . $file_name;

                if (move_uploaded_file($_FILES['FORM_BANNER_IMAGE']['tmp_name'], $destination)) {
                    Configuration::updateValue('FORM_BANNER_IMAGE', $file_name);
                    $output .= $this->displayConfirmation($this->l('Image et paramètres mis à jour'));
                } else {
                    $output .= $this->displayError($this->l('Erreur lors de l\'upload de l\'image'));
                }
            } else {
                $output .= $this->displayConfirmation($this->l('Paramètres mis à jour'));
            }
        } else {
            $output .= $this->displayError($this->l('veuillez remplir le champ.')); 
        }

        $current_index = AdminController::$currentIndex . '&configure=' . $this->name;
        $token = Tools::getAdminTokenLite('AdminModules');
    
        Tools::redirectAdmin($current_index . '&conf=4&token=' . $token);
    }

    return $output . $this->displayForm();
}

    public function hookDisplayHome($params) {

        if (!Configuration::get('BOUTTON')) {
            return; 
        }

        $this->context->controller->addCSS($this->_path.'views/css/styles.css', 'all');
        $this->context->controller->addJS($this->_path.'views/js/script.js');
        $message_html = ''; 

        

        if(Tools::getValue('form_success')){
            $message_html = $this->displayConfirmation($this->l('Enregistré avec succès'));
       }
        
        if (Tools::isSubmit('submit')) {
            $nom = Tools::getValue('nom'); 
            $email = Tools::getValue('email');
            $msgContent = Tools::getValue('message');
            
            if (!empty($nom) && !empty($email) && !empty($msgContent) && Validate::isEmail($email)) { 
               
                $id_exists = (int)Db::getInstance()->
                            getValue('SELECT `id` FROM `'._DB_PREFIX_.'formulaire_data` WHERE `email` = "'.pSQL($email).'"');
                if($id_exists > 0){
                    Db::getInstance()->update('formulaire_data',[
                        'message'=>pSQL($msgContent),
                        'date_add'=>pSQL(date('Y-m-d H:i:s')),
 
                    ],'id ='.$id_exists);
                }else{
                    Db::getInstance()->insert('formulaire_data', [
                    'nom' => pSQL($nom),
                    'message' => pSQL($msgContent),
                    'email'=> pSQL($email ),
                    'date_add'=>pSQL(date('Y-m-d H:i:s')),
                    ]);
                }

                
                 Tools::redirect($this->context->link->getPageLink('index',true,null,'form_success=formulaire'));

            } else {
                $message_html = $this->displayError($this->l('veuillez rempli tous les champs.'));
            }
        }

        $file_name = Configuration::get('FORM_BANNER_IMAGE');
        $banner_url = $file_name ? $this->_path . 'views/img/' . $file_name : false;

        $this->context->smarty->assign(
            ['message' => $message_html,
            'form_title' => Configuration::get('TITLE'),
            'banner_image' => $banner_url
        ]);
        return $this->display(__FILE__, 'views/templates/front/formulaire.tpl');
    }


    public function displayForm() {
      
        $file_name = Configuration::get('FORM_BANNER_IMAGE');
        $image_url = $file_name ? $this->_path . 'views/img/' . $file_name : '';

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuration du module'),
                    'icon' => 'icon-cogs'
                ],
                'input' => [
                     [
                        'type' => 'file',
                        'label' => $this->l('Image de bannière'),
                        'name' => 'FORM_BANNER_IMAGE',
                        'display_image' => true,
                        'image' => $image_url ? '<img src="'.$image_url.'" class="img-thumbnail" width="200">' : false,
                        'desc' => $image_url ? $this->l('Fichier : ').$file_name : $this->l('Aucune image.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('titre affiché sur le formulaire'),
                        'name' => 'TITLE',
                        'size' => 20,
                        'required' => true
                    ],
                    [
                    'type' => 'switch',
                    'label' => $this->l('Afficher le formulaire ?'),
                    'name' => 'boutton',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Oui')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Non')
                        ]
                        ],
                ],
            ],
                'submit' => [
                    'name'=>'submitConfigure',
                    'title' => $this->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right'
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submitConfigure';
        $helper->show_toolbar = false;
        $helper->fields_value['TITLE'] = Configuration::get('TITLE');
        $helper->fields_value['boutton'] = Configuration::get('BOUTTON');

        return $helper->generateForm([$fields_form]);
    }

    
}