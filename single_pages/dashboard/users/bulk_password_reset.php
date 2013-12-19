<?php 
defined('C5_EXECUTE') or die(_("Access Denied"));

/**
 * @author 		Blake Bengtson (bbeng89)
 * @copyright  	Copyright 2013 Blake Bengtson
 * @license     concrete5.org marketplace license
 */
 
$dbh = Loader::helper('concrete/dashboard');
$nh = Loader::helper('navigation');
$fh = Loader::helper('form');

echo $dbh->getDashboardPaneHeaderWrapper(t('Bulk Password Reset'), t('Reset the passwords for all the users in the site or just users in selected groups.'), false, false);
?>

<form id="resetForm" class="form-horizontal" action="<?php echo $this->action('save'); ?>" method="POST">
	<div class="ccm-pane-body">
		<p><?php echo t("This form will reset user passwords for all users in the selected groups."); ?></p>
		<ul>
			<li><?php echo t('Select "All Groups" to reset the password for all users in your site.'); ?></li>
			<li><?php echo t("The super admin's password will never be reset."); ?></li>
		</ul>
		<hr/>
		<div class="control-group">
			<label class="control-label" for="delay"><?php echo t("New Password"); ?></label>
			<div class="controls">
				<?php echo $fh->password('password1', null, array("class" => "span6")); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="delay"><?php echo t("Confirm"); ?></label>
			<div class="controls">
				<?php echo $fh->password('password2', null, array("class" => "span6")); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php echo t("Apply To Users In"); ?></label>
			<div class="controls">
				<?php foreach($groups as $gid => $name): ?>
					<label class="checkbox"><?php echo $fh->checkbox('groups[]', $gid, $gid == 'A', null) . ' ' . $name; ?></label>
				<?php endforeach;?>
			</div>
		</div>
		<?php echo $vth->output('reset_password'); ?>
	</div>
	<div class="ccm-pane-footer">
		<a class="btn pull-left" href="<?php echo $nh->getLinkToCollection(Page::getByPath('/dashboard')); ?>"><?php echo t('Return to Dashboard'); ?></a>
		<button type="submit" class="btn btn-primary pull-right"><?php echo t('Reset Passwords'); ?></button>
	</div>
</form>
<?php echo $dbh->getDashboardPaneFooterWrapper(false);?>

<script>
	$(document).ready(function(){
		//initial checks - selects All Groups and disables all others
		checkCheckboxes();

		//confirmation before submitting
		$('#resetForm').submit(function(){
			return confirm("<?php echo t('Are you sure you want to reset these passwords? This cannot be undone.');?>");
		});

		$('#groups_A').change(checkCheckboxes);
	});
	
	function checkCheckboxes(){
		var checkboxes = $('input[type="checkbox"][name^="groups"][id!=groups_A]');
		var allGroupsCb = $('#groups_A');

		if(allGroupsCb.is(':checked')){
			checkboxes.each(function(){
				$(this).removeAttr('checked');
				$(this).attr('disabled', 'disabled');
			});
		}
		else{
			checkboxes.each(function(){
				$(this).removeAttr('disabled');
			});
		}
	}
</script>