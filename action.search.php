<?php
	if (!cmsms()) exit;

	$ldap_query = new LDAPQuery();
	
	if (isset($params['detailpage'])) {
	    $params['origid'] = $returnid;

	    $manager = cmsms()->GetHierarchyManager();
	    $node = $manager->sureGetNodeByAlias($params['detailpage']);
	    if ($node) {
	        $content = $node->GetContent();
	        if ($content)
	        {
	            $returnid = $content->Id();
	        }
	    } else {
	        $node = $manager->sureGetNodeById($params['detailpage']);
	        if ($node) {
	            $returnid = $params['detailpage'];
	        }
	    }
	}
	
	$form = new CMSForm($this->GetName(), $id,'default',$returnid);
	
	$form->setButtons(array('search'));
	$form->setLabel('search', $this->Lang('search'));
	
	$form->setWidget('q', 'text', array('label' => $this->lang('search')));
	
	$this->smarty->assign('ldap_search', $form);
	
	echo $this->ProcessTemplateFor('search', $params);
	