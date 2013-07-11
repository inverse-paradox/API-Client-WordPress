<?php 
$general = ip_api_get_config('general');
$user = ip_api_get_config('user');
?>

<?php if($message = $user->message):?>
	<div style="border:1px solid #f16048;background-color: #faebe7;color: #df280a;font-weight:bold;padding: 8px;">
		<?php echo $message;?>
	</div>
	<?php if($general->message):?><br /><?php endif;?>
<?php endif;?>

<?php if($message = $general->message):?>
	<div style="border: 1px solid #fcd344;background-color: #fafaec;color: #3d6611;font-weight:bold;padding: 8px;">
		<?php echo $message;?>
	</div>
<?php endif;?>

<?php echo wpautop(stripslashes($general->info));?>

<p><strong>Your Project Manager:</strong> <a href="mailto:<?php echo $user->manager_email;?>"><?php echo $user->manager;?></a></p>