<?php
defined('C5_EXECUTE') or die(_("Access Denied"));

/**
 * @author 		Blake Bengtson (bbeng89)
 * @copyright  	Copyright 2013 Blake Bengtson
 * @license     concrete5.org marketplace license
 */

class DashboardUsersBulkPasswordResetController extends Controller{

	public function on_start(){
		$this->set('vth', Loader::helper('validation/token'));
		$this->set('vh', Loader::helper('concrete/validation'));
		$this->error = Loader::helper('validation/error');
	}

	public function view(){
		$this->set('groups', $this->getGroups());
	}

	public function save(){

		$vth = Loader::helper('validation/token');

		if($this->isPost() && $vth->validate('reset_password')){

			$newpass = $this->post('password1');
			$confirm = $this->post('password2');
			$groups = $this->post('groups');

			if(empty($newpass)){
				$this->error->add(t('New password cannot be empty'));
			}
			if(empty($confirm)){
				$this->error->add(t('Please confirm your new password'));
			}
			if($newpass != $confirm){
				$this->error->add(t('New password and confirmation do not match'));
			}
			if(count($groups) == 0){
				$this->error->add(t('Please select at least one group or choose "All Groups"'));
			}

			if(!$this->error->has()){
				$db = Loader::db();
				//encrypt password
				$passEncrypted = User::encryptPassword($newpass);
				$data = array($passEncrypted);

				//don't change the superadmin password (uID 1)
				$query = "UPDATE Users u LEFT JOIN UserGroups ug ON u.uID = ug.uID SET u.uPassword = ? WHERE u.uID != 1";

				if(count($groups > 0) && !in_array('A', $groups)){
					$groupStr = implode(', ', $groups);
					$query .= " AND ug.gID IN (" . $groupStr . ")";
				}

				$db->Execute($query, $data);
				$count = $db->Affected_Rows();

				$msg = t2('User passwords have been successfully reset. %d user was affected.', 'User passwords have been successfully reset. %d users were affected', $count, $count);
				$this->set('message',  $msg);
				$this->view();
			}
			else{
				$this->set('error', $this->error);
				$this->view();
			}
		}
		else{
			$this->redirect('/dashboard/users/bulk_password_reset');
		}
	}

	//helper function - returns array of user groups
	private function getGroups(){
		Loader::model('search/group');
		$gs = new GroupSearch();
		//ignore the Guest group
		$gs->filter('gID', 1, '!=');
		//ignore the Registered Users group
		$gs->filter('gID', 2, '!=');
		$groupArr = $gs->get(9999, 0);
		$groups = array("A" => t("All Groups"));
		foreach($groupArr as $ga){
			$groups[$ga['gID']] = $ga['gName'];
		}
		return $groups;
	}
}