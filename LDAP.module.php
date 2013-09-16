<?php

/*
	Module: LDAP - Interface to do LDAP Queries
	
	Copyrights: Jean-Christophe Cuvelier - 2013 ©
*/


class LDAP extends CMSModule
{
	public static $frontend_templates = array(
      'search'     => 'search',
      'default'     => 'default (results)'
    );
	
	public function GetName()               { return 'LDAP';	               }
  public function GetFriendlyName() {       return 'LDAP Search';  }
	public function GetVersion()            { return '0.0.2';                    }
	public function GetAuthor()             { return 'Jean-Christophe Cuvelier'; }
	public function GetAuthorEmail()        { return 'cybertotophe@gmail.com';   }
	
  public  function GetHelp() {              return $this->Lang('help');  }
	public  function MinimumCMSVersion()    { return '1.10';  }
  public  function GetDependencies() {      return array('CMSForms' => '1.10.2');  }
  public  function CheckAccess($permission = 'Manage LDAP') {    return $this->CheckPermission($permission);  }
  public  function VisibleToAdminUser()     { return $this->CheckAccess();  }
  public  function IsPluginModule() {       return true;  }
  public  function HasAdmin() {             return true;}
  public  function GetAdminSection() {      return 'extensions';  }


	public function InitializeFrontend()
	{
		$this->RegisterModulePlugin();
		
		$this->RestrictUnknownParams();

		// $this->SetParameterType('pagelimit',CLEAN_INT);
		$this->SetParameterType('q',CLEAN_STRING);
		$this->SetParameterType('cn',CLEAN_STRING);
		$this->SetParameterType('detailpage',CLEAN_STRING);
		$this->SetParameterType('maction',CLEAN_STRING);
		$this->SetParameterType('template',CLEAN_STRING);

		
	}
	
	public function InitializeGlobal() {  
    
	}
	
	// TEMPLATES
  
  public function GetDefaultTemplates()
  {
    $array = unserialize($this->GetPreference('default_templates'));
    if (is_array($array))
    {
      return $array;
    }
    return array();
  } 
  
  public function SetDefaultTemplates($list = array())
  {
    return $this->SetPreference('default_templates', serialize($list));
  }
  
  public function AddDefaultTemplate($action, $template)
  {
    $list = $this->GetDefaultTemplates();
    $list[$action] = $template;
    $this->SetDefaultTemplates($list);
  }
  
  public function GetDefaultTemplate($action)
  {
      $list = $this->GetDefaultTemplates();
      if (!is_array($list)) $list = array();
      if (array_key_exists($action, $list)) // TODO: Possible problem with list
      {
        return $list[$action];
      }
      else
      {
        return false;
      }
  }
  
  public function isDefaultTemplate($template)
  {    
    $list = $this->GetDefaultTemplates();
    $action = array_search($template, $list);
    if($action !== false)
    {
      return $action;
    }
    return false;
  }  
  
  public function removeDefaultTemplate($template)
  {    
    $list = $this->GetDefaultTemplates();
    $action = array_search($template, $list);
    if($action !== false)
    {
      unset($list[$action]);
      $this->SetDefaultTemplates($list);
    }
    return false;
  }
  
  public function ProcessTemplateFor($action, $params = array())
  {
    if (isset($params['template']) && $this->GetTemplate($params['template'])) {
      return $this->ProcessTemplateFromDatabase($params['template']);
    }
    elseif (($template = $this->GetDefaultTemplate($action))  &&  ($this->GetTemplate($template) !== false))
    {
      return $this->ProcessTemplateFromDatabase($template);
    }
    else
    {
      return $this->ProcessTemplate('frontend.'.$action.'.tpl');
    }
  }
}
