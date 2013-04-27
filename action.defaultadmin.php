<?php

if (!cmsms()) exit;

if (!($this->CheckAccess() || $this->CheckAccess('Manage LDAP'))) {
	return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

echo $this->StartTabHeaders();
	echo $this->SetTabHeader('templates', $this->Lang('tab_templates'));
	echo $this->SetTabHeader('options', $this->Lang('tab_options'));
echo $this->EndTabHeaders();

echo $this->StartTabContent();

// TEMPLATES
echo $this->StartTab('templates');

	$list_templates = $this->ListTemplates();
	$templates = array();
	foreach($list_templates as $template) {
		$row = array(
			'titlelink' => $this->CreateLink($id, 'template_edit', $returnid, $template, array('template' => $template), '', false, false, 'class="itemlink"'),
			'deletelink' => $this->CreateLink($id, 'template_delete', $returnid, cmsms()->get_variable('admintheme')->DisplayImage('icons/system/delete.gif', $this->Lang('delete'), '', '', 'systemicon'), array('template' => $template), $this->lang('are you sure you want to delete this template')),
			'editlink' => $this->CreateLink($id, 'template_edit', $returnid, cmsms()->get_variable('admintheme')->DisplayImage('icons/system/edit.gif', $template, '', '', 'systemicon'), array('template' => $template))
		);

		if ($this->isDefaultTemplate($template) !== false)
		{
			$row['default'] = $this->lang('default template for', $this->isDefaultTemplate($template));
		}
		else
		{
			$row['default'] = '';
		}

		$templates[] = $row;
	}
	$this->smarty->assign('templates', $templates);
	$this->smarty->assign('add_templates_link', $this->CreateLink($id, 'template_edit', $returnid, $this->Lang('add template')));
	$this->smarty->assign('add_templates_icon', $this->CreateLink($id, 'template_edit', $returnid, cmsms()->get_variable('admintheme')->DisplayImage('icons/system/newobject.gif', $this->Lang('add template'), '', '', 'systemicon')));

	echo $this->ProcessTemplate('admin.templates.tpl');
	
echo $this->EndTab();
// END TEMPLATES

// OPTIONS
echo $this->StartTab('options');

	$form = new CMSForm($this->GetName(), $id,'defaultadmin',$returnid);

	$form->setButtons(array('submit'));
	$form->setLabel('submit', $this->Lang('save'));

	$form->setWidget('hostname', 'text', array('preference' => 'hostname'));
	$form->setWidget('port', 'text', array('preference' => 'port'));

	$form->setWidget('ldap_rdn', 'text', array('preference' => 'ldap_rdn'));
	$form->setWidget('ldap_password', 'password', array('preference' => 'ldap_password'));

	$form->setWidget('search_dn', 'text', array('preference' => 'search_dn'));
	$form->setWidget('search_prefix', 'text', array('preference' => 'search_prefix'));
	$form->setWidget('search_pattern', 'text', array('preference' => 'search_pattern', 'tips' => $this->lang('tips_search_pattern')));
	$form->setWidget('search_numeric_pattern', 'text', array('preference' => 'search_numeric_pattern', 'tips' => $this->lang('tips_search_pattern')));
	$form->setWidget('search_exclude', 'text', array('preference' => 'search_exclude'));


	if($form->isSubmitted())
	{
		$form->process();
		$this->Audit(null, $this->GetName(), 'Preferences updated');
	}

	echo $form->render();
	
echo $this->EndTab();
// END OPTIONS

echo $this->EndTabContent();