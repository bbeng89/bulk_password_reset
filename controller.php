<?php
defined('C5_EXECUTE') or die(_("Access Denied"));

/**
 * @author 		Blake Bengtson (bbeng89)
 * @copyright  	Copyright 2013 Blake Bengtson
 * @license     concrete5.org marketplace license
 */

class BulkPasswordResetPackage extends Package {
	protected $pkgHandle = 'bulk_password_reset';
	protected $appVersionRequired = '5.6.0';
	protected $pkgVersion = '0.9.3';

	public function getPackageDescription(){
		return t('Allows you to update user passwords in bulk.');
	}

	public function getPackageName(){
		return t('Bulk Password Reset');
	}

	public function install(){
		$pkg = parent::install();
		$this->installSinglePages($pkg);
	}

	public function installSinglePages($pkg){
		$dashboardIcons = array();

		//base site notifications page (just redirects to list)
		$path = '/dashboard/users/bulk_password_reset';
		$cID = Page::getByPath($path)->getCollectionID();
		if (intval($cID) > 0 && $cID !== 1) {
			// the single page already exists, so we want to update it to use our package elements
			Loader::db()->execute('update Pages set pkgID = ? where cID = ?', array($pkg->pkgID, $cID));
		} else {
			// it doesn't exist, so now we add it
			$p = SinglePage::add($path, $pkg);
			if (is_object($p) && $p->isError() !== false) {
				$p->update(array('cName' => t('Bulk Password Reset')));
			}
		}
		$dashboardIcons[$path] = "icon-lock";

		$this->setupDashboardIcons($dashboardIcons);
	}

	private function setupDashboardIcons($iconArray) {
		$cak = CollectionAttributeKey::getByHandle('icon_dashboard');
		if (is_object($cak)) {
			foreach($iconArray as $path => $icon) {
				$sp = Page::getByPath($path);
				if (is_object($sp) && (!$sp->isError())) {
					$sp->setAttribute('icon_dashboard', $icon);
				}
			}
		}
	}
}