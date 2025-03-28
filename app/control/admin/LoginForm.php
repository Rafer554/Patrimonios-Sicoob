<?php
/**
 * LoginForm
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class LoginForm extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($param)
    {
        parent::__construct();
        
        $ini  = AdiantiApplicationConfig::get();
        
        $this->style = 'clear:both';
        // creates the form
        $this->form = new BootstrapFormBuilder('form_login');
        $this->form->setFormTitle( 'Login' );
        
        // create the form fields
        $login = new TEntry('login');
        $password = new TPassword('password');
        
        $previous_class = new THidden('previous_class');
        $previous_method = new THidden('previous_method');
        $previous_parameters = new THidden('previous_parameters');
        
        if (!empty($param['previous_class']) && $param['previous_class'] !== 'LoginForm')
        {
            $previous_class->setValue($param['previous_class']);
            
            if (!empty($param['previous_method']))
            {
                $previous_method->setValue($param['previous_method']);
            }
            
            $previous_parameters->setValue(serialize($param));
        }
        
        // define the sizes
        $login->setSize('100%', 40);
        $password->setSize('100%', 40);

        $login->style = 'height:35px; font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        $password->style = 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
        
        $login->placeholder = _t('User');
        $password->placeholder = _t('Password');
        
        $login->autofocus = 'autofocus';

        $user   = '<span class="login-avatar"><span class="fa fa-user"></span></span>';
        $locker = '<span class="login-avatar"><span class="fa fa-lock"></span></span>';
        $unit   = '<span class="login-avatar"><span class="fa fa-university"></span></span>';
        $lang   = '<span class="login-avatar"><span class="fa fa-globe"></span></span>';
        
        $row = $this->form->addFields( [$user, $login] );
        $row->layout = ['col-sm-12 display-flex'];
        $row = $this->form->addFields( [$locker, $password] );
        $row->layout = ['col-sm-12 display-flex'];
        $this->form->addFields( [$previous_class, $previous_method, $previous_parameters] );
        
        $multiunit = $ini['general']['multiunit'] ?? false;
        $request_unit_after_login = $ini['general']['request_unit_after_login'] ?? false;
        
        if ($multiunit == '1' && !$request_unit_after_login)
        {
            $unit_id = new TCombo('unit_id');
            $unit_id->setSize('100%');
            $unit_id->style = 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
            $row = $this->form->addFields( [$unit, $unit_id] );
            $row->layout = ['col-sm-12 display-flex'];
            $login->setExitAction(new TAction( [$this, 'onExitUser'] ) );
        }
        
        if (!empty($ini['general']['multi_lang']) and $ini['general']['multi_lang'] == '1')
        {
            $lang_id = new TCombo('lang_id');
            $lang_id->setSize('100%');
            $lang_id->style = 'height:35px;font-size:14px;float:left;border-bottom-left-radius: 0;border-top-left-radius: 0;';
            $lang_id->addItems( $ini['general']['lang_options'] );
            $lang_id->setValue( $ini['general']['language'] );
            $lang_id->setDefaultOption(FALSE);
            $row = $this->form->addFields( [$lang, $lang_id] );
            $row->layout = ['col-sm-12 display-flex'];
        }
        
        $btn = $this->form->addAction('Login', new TAction(array($this, 'onLogin')), '');
        $btn->class = 'btn btn-default';
        $btn->style = 'height: 40px;width: 90%;display: block;margin: auto;font-size:17px;';
        
        $wrapper = new TElement('div');
        $wrapper->style = 'margin:auto; margin-top:100px;max-width:460px;';
        $wrapper->id = 'login-wrapper';
        
        $h3 = new TElement('h1');
        $h3->style = 'text-align:center;';
        $h3->add('Login');
        
        $divLogo = new TElement('div');
        $divLogo->class = 'login-medium-logo';
        
        $wrapper->add($divLogo);
        $wrapper->add($h3);
        $wrapper->add($this->form);
        
        // add the form to the page
        parent::add($wrapper);
    }
    
    /**
     * user exit action
     * Populate unit combo
     */
    public static function onExitUser($param)
    {
        try
        {
            $ini  = AdiantiApplicationConfig::get();
            $multiunit = $ini['general']['multiunit'] ?? false;
            $request_unit_after_login = $ini['general']['request_unit_after_login'] ?? false;
        
            if ($multiunit != '1' || $request_unit_after_login == '1')
            {
                return;
            }
            
            TTransaction::open('permission');
            
            $user = SystemUsers::newFromLogin( $param['login'] );
            if ($user instanceof SystemUsers)
            {
                $units = $user->getSystemUserUnits();
                $options = [];
                
                if ($units)
                {
                    foreach ($units as $unit)
                    {
                        $options[$unit->id] = $unit->name;
                    }
                }
                TCombo::reload('form_login', 'unit_id', $options);
            }
            else
            {
                TCombo::clearField('form_login', 'unit_id', []);
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error',$e->getMessage());
            TTransaction::rollback();
        }
    }
    
    private static function showUnitForm($param, $units, $user)
    {
        try
        {
            if ($units)
            {
                foreach ($units as $unit)
                {
                    $options[$unit->id] = $unit->name;
                }
            }
            
            $unidades = new TCombo('unit_id');
            $unidades->addItems($options, $user);
            $unidades->setDefaultOption(false);
            $unidades->setValue($user->system_unit_id);
            
            $form = new BootstrapFormBuilder('form_unidade');
            $form->addFields([new TLabel(_t('Choose the unit')), $unidades]);
            $form->setFieldSizes('100%');
            
            $form->addAction('Login', new TAction(array(__CLASS__, 'onLogin'), $param), '');
            
            new TInputDialog(_t('Unit'), $form);
        }
        catch(Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Authenticate the User
     */
    public static function onLogin($param)
    {
        $ini  = AdiantiApplicationConfig::get();
        
        try
        {
            $data = (object) $param;
            
            TSession::regenerate();
            
            (new TRequiredValidator)->validate( _t('Login'),    $data->login);
            (new TRequiredValidator)->validate( _t('Password'), $data->password);
            
            $ini  = AdiantiApplicationConfig::get();
            $multiunit = $ini['general']['multiunit'] ?? false;
            $request_unit_after_login = $ini['general']['request_unit_after_login'] ?? false;
            $single_user_session = $ini['permission']['single_user_session'] ?? false;
            
            $user = ApplicationAuthenticationService::authenticate($data->login, $data->password, false);
            
            if ($multiunit == '1' && $request_unit_after_login == '1' && empty($data->unit_id))
            {
                TTransaction::open('permission');
                $units = $user->getSystemUserUnits();
                if(count($units) > 1)
                {
                    return self::showUnitForm($param, $units, $user);
                }
                elseif(!empty($units[0]->id))
                {
                    $data->unit_id = $units[0]->id;
                    $param['unit_id'] = $data->unit_id;
                }
                TTransaction::close();
            }
            
            if ($multiunit == '1')
            {
                (new TRequiredValidator)->validate( _t('Unit'), $data->unit_id ?? null);
            }
            
            if (!empty($ini['general']['require_terms']) && $ini['general']['require_terms'] == '1' && !empty($param['usage_term_policy']) AND empty($data->accept))
            {
                throw new Exception(_t('You need read and agree to the terms of use and privacy policy'));
            }

            TScript::create("__adianti_clear_tabs()");

            $term_policy = SystemPreference::findInTransaction('permission', 'term_policy');

            if (!empty($ini['general']['require_terms']) && $ini['general']['require_terms'] == '1' && $user->accepted_term_policy !== 'Y' && !empty($term_policy) && empty($data->accept))
            {
                TSession::freeSession();
                $param['usage_term_policy'] = 'Y';
                $action = new TAction(['LoginForm', 'onLogin'], $param);
                $form = new BootstrapFormBuilder('term_policy');

                $content = new TElement('div');
                $content->style = "max-height: 45vh; overflow: auto; margin-bottom: 10px;";
                $content->add($term_policy->preference);

                $check = new TCheckGroup('accept');
                $check->addItems(['Y' => _t('I have read and agree to the terms of use and privacy policy')]);

                $form->addContent([$content]);
                $form->addFields([$check]);
                $form->addAction( _t('Accept'), $action, 'fas:check');

                return new TInputDialog(_t('Terms of use and privacy policy'), $form);
            }
            
            if (!empty($ini['general']['require_terms']) && $ini['general']['require_terms'] == '1' && $user->accepted_term_policy !== 'Y' && !empty($term_policy) && !empty($data->accept))
            {
                TTransaction::open('permission');
                $user->accepted_term_policy = 'Y';
                $user->accepted_term_policy_at = date('Y-m-d H:i:s');
                $user->store();
                TTransaction::close();
            }

            if ($user)
            {
                TTransaction::open('permission');
                ApplicationAuthenticationService::loadSessionVars($user, true);
                TTransaction::close();
                
                ApplicationAuthenticationService::setUnit( $data->unit_id ?? null );
                ApplicationAuthenticationService::setLang( $data->lang_id ?? null );
                SystemAccessLogService::registerLogin();
                SystemAccessNotificationLogService::registerLogin();
                
                if($single_user_session == '1')
                {
                    ApplicationAuthenticationService::clearSessions();    
                }
                
                $frontpage = $user->frontpage;
                if (!empty($param['previous_class']) && $param['previous_class'] !== 'LoginForm')
                {
                    AdiantiCoreApplication::gotoPage($param['previous_class'], $param['previous_method'], unserialize($param['previous_parameters'])); // reload
                }
                else if ($frontpage instanceof SystemProgram and $frontpage->controller)
                {
                    AdiantiCoreApplication::gotoPage($frontpage->controller); // reload
                    TSession::setValue('frontpage', $frontpage->controller);
                }
                else
                {
                    AdiantiCoreApplication::gotoPage('EmptyPage'); // reload
                    TSession::setValue('frontpage', 'EmptyPage');
                }
            }
        }
        catch (Exception $e)
        {
            TSession::freeSession();
            new TMessage('error',$e->getMessage());
            sleep(2);
            TTransaction::rollback();
        }
    }
    
    /** 
     * Reload permissions
     */
    public static function reloadPermissions()
    {
        try
        {
            TTransaction::open('permission');
            $user = SystemUsers::newFromLogin( TSession::getValue('login') );
            
            if ($user)
            {
                ApplicationAuthenticationService::loadSessionVars($user, false);
                
                $frontpage = $user->frontpage;
                if ($frontpage instanceof SystemProgram AND $frontpage->controller)
                {
                    TApplication::gotoPage($frontpage->controller); // reload
                }
                else
                {
                    TApplication::gotoPage('EmptyPage'); // reload
                }
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     *
     */
    public function onLoad($param)
    {
    }
    
    /**
     * Logout
     */
    public static function onLogout()
    {
        SystemAccessLogService::registerLogout();
        TSession::freeSession();
        AdiantiCoreApplication::gotoPage('LoginForm', '');
    }
}
