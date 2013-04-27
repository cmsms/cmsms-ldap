<?php

	/*
		Class: LDAPQuery - Interface to do LDAP Queries
	
		Copyrights: Jean-Christophe Cuvelier - 2013 ©
	*/
	
	class LDAPQuery
	{
		private $module;
		public $hostname;
		public $port = 389;
		public $rdn;
		private $password;
		
		private $ldapconn;
		private $errors = array();
		
		public function __construct() {
			$this->module = cms_utils::get_module('LDAP');
	
			try {
				$this->connect();
			} catch (Exception $e) {
				$this->module->Audit(null, $this->module->GetName(), 'Caught exception:' . $e->getMessage());
			}
		}
		
		public function __destruct()	{
			ldap_close($this->ldapconn);
		}
		
		private function connect() {
			$this->hostname = (string)$this->module->GetPreference('hostname', null);
			$this->port = (int)$this->module->GetPreference('port', 389);
			$this->rdn = (string)$this->module->GetPreference('ldap_rdn', null);
			$this->password = (string)$this->module->GetPreference('ldap_password', null);
			
			$this->ldapconn = ldap_connect($this->hostname, $this->port);
			if(!$this->ldapconn) {
				throw new Exception('Cannot connect to LDAP Server');
			} else {
			  ldap_set_option($this->ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3); // TODO: Check why
				if(!ldap_bind($this->ldapconn, $this->rdn, $this->password))
				{
					throw new Exception('LDAP bind failed');
				}
				else
				{
					return $this->ldapconn;
				}
			}
		}
		
		public function getDS()	{
			return $this->ldapconn;
		}
		
		public function search($dn, $filter, Array $attributes)	{
			$search = ldap_search($this->ldapconn, $dn, $filter, $attributes);
			$results = array();
			if(FALSE === $search) {
				$this->errors[] = ldap_error($this->ldapconn);
			} else {
				$entries = ldap_get_entries($this->ldapconn, $search);
				if($entries['count'] > 0) {
					unset($entries['count']);
					foreach($entries as $key => $values) {	
						$entry = array();
						
						foreach($values as $name => $value) {
							if(!is_int($name)) {
								$a = array();
								foreach($value as $k => $v)	{
									if(is_int($k))	$a[] = $v;
								}								
								$entry[$name] = implode(',', $a);
								// $entry[$name] = $value;
							}
						}					
						
						$results[] = $entry;					
					}
				}
			}
			return $results;
		}
		
		public function prepareFilter($sq, $filter)	{		
			$filters = array();
			
			if($prefix = $this->module->getPreference('search_prefix', null))
			{
				$filters[] = $prefix;
			}
			
			if($exclude = $this->module->getPreference('search_exclude', null))
			{
				$filters[] = $exclude;
			}
		
			$sq = preg_replace( "/[^a-zA-Z0-9\ ,]/", "", $sq);
			if (strlen($sq) == 0) $sq = ",";
	 		
			$filters[] = str_replace('{{Q}}', $sq, $filter);
			
			
			return '(&'.implode('', $filters).')';
		}
		
		public function debug()	{
			var_dump($this->errors);
		}
	}