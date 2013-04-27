<?php
	if (!cmsms()) exit;

	$ldap_query = new LDAPQuery();
	
	// $ds = $ldap_query->getDS();
	
	if(isset($params['dn']))	{
		$dn = $params['dn'];
	}	else {
		// $dn = "ou=group,dc=morris-chapman,dc=net";
		$dn = $this->GetPreference('search_dn', null);
	}
	
	// $params['cn'] = 'jcc';
	// $params['q'] = 'phe';

	if(isset($params['cn'])) {
		$filter = $ldap_query->prepareFilter($params['cn'], '(|(cn=*{{Q}}*))');
	}	else {
		if(isset($params['q']))	{
			if (preg_match("/^\d+/", $params['q'])) {
				$filter = $ldap_query->prepareFilter($params['q'], $this->GetPreference('search_numeric_pattern', null));
			} else {
				$filter = $ldap_query->prepareFilter($params['q'], $this->GetPreference('search_pattern', null));
			}
		}
	}
	
	if(isset($filter))
	{
		$results = $ldap_query->search($dn, $filter, array());

		$this->smarty->assign('results', $results);

		echo $this->ProcessTemplateFor('default', $params);
	}
	
	// $ldap_query->debug();