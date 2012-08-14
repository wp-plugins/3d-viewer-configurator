<h3>
<?php
if ($_GET['action'] == 'edit') _e('Edit configurator');
else _e('Create configurator');
?> 
</h3>
<div style="float: left; width: 470px;">
<form name="edit_form" method="POST" action="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>" enctype="multipart/form-data">
  <input type="hidden" name="subaction" value="edit" />
  <input type="hidden" name="f_id" value="<?php echo $item['v_id']; ?>" />
  <table cellspacing="6" cellpadding="6">
  	<tr>
  		<td colspan="2"><b><?php echo _e('Configurator settings:'); ?></b></td>
  	</tr>
    <tr>
      <td width="120"><label for="f_name"><?php _e('Name'); ?></label></td><td><input id="f_name" size="40" maxlength="255" type="text" name="f_name" title="Internal name for konfigurator." value="<?php echo $item['v_name']; ?>" /></td>
    </tr>
    <tr>
      <td valign="top"><label for="f_options"><?php _e('Options'); ?></label></td>
      <td>
        <input type="checkbox" id="f_invert" name="f_options[]" value="invert"<?php echo @in_array('invert', $item['options'])?' checked':''; ?> /> <label for="f_invert"><?php _e('Invert order of images'); ?></label><br />
        <input type="checkbox" id="f_play" name="f_options[]" value="play"<?php echo @in_array('play', $item['options'])?' checked':''; ?> />  <label for="f_play"><?php _e('Start rotation automatically'); ?></label>
      </td>
    </tr>
    <tr>
      <td><label for="f_rpm"><?php _e('Speed'); ?></label></td><td><input id="f_rpm" type="text" size="10" name="f_rpm" title="Speed of the rotation." value="<?php echo $item['v_rpm']; ?>" /></td>
    </tr>
    <tr>
      <td></td>
      <td>
        <input class="button-primary" type="submit" name="f_ok" title="<?php _e('Ok'); ?>" value="<?php _e('Ok'); ?>" id="submitbutton" />
        <a class="button-secondary" href="<?php echo $main_link; ?>" title="<?php _e('Cancel'); ?>"><?php _e('Cancel'); ?></a>
      </td>
    </tr>
    <tr>
    	<td colspan="2">
    		<h3><?php echo _e('Option 1: Upload the whole file structure'); ?></h3>
    	</td>
    </tr>
    <tr>
      <td valign="top">
        <label for="f_file"><?php _e('File'); ?></label></td><td><input id="f_file" type="file" name="f_files[]" title="You can select one zip file." />
        <br />
        <a href="#" onclick="jQuery('#conf-fileupload-complete-help').toggle('fast'); return false;"><b>Show/hide example</b></a>
        <div id="conf-fileupload-complete-help" style="display: none;">
          Zip archive, file structure example:<br />
          <b>configuration1/</b> <i>(1st configuration main folder)</i><br />
          -- <b>view/</b> <i>(pictures of 1st configuration)</i><br />
          -- -- <b>zoom/</b> <i>(zoom pictures of 1st configuration)</i><br />
          -- -- -- img0.jpg<br />
          -- -- -- img1.jpg<br />
          -- -- img0.jpg<br />
          -- -- img1.jpg<br />
          -- thumb.jpg<br />
          <b>configuration2/</b> <i>(2nd configuration main folder)</i><br />
          -- ...
        </div>
      </td>
    </tr>
    <tr>
      <td></td>
      <td>
        <input class="button-primary" type="submit" name="f_ok" title="<?php _e('Ok'); ?>" value="<?php _e('Ok'); ?>" id="submitbutton" />
        <a class="button-secondary" href="<?php echo $main_link; ?>" title="<?php _e('Cancel'); ?>"><?php _e('Cancel'); ?></a>
      </td>
    </tr>
  </table>  
</form>
</div>



<!-- Free version -->
<p>Here you can find examples of how the 3D configurator can be used:<br/>
<a href="http://www.3d-moebel-konfigurator.de/die-loesung">3D Configuration Examples</a></p>

<p>Find out how affordable is our visualization and photography service:<br/>
<a href="http://www.3d-moebel-konfigurator.de/so-gehts/anfrageformular">Prices for 3D visualization and photography</a></p>

<p>Here you can buy the PRO version of the visualtektur-Logo to remove:<br/>
<a href="http://www.3d-eshop.eu">3D Viewer PRO</a><br/>
<a href="http://www.3d-eshop.eu">3D configurator PRO</a></p>

<p>With a small donation you can help us further develop the 3D configurator:<br/>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="SW8D4VEGBG34N">
<input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form>
</p>



<br style="clear: both;" />

<h3 style="margin-left: 6px;"><?php echo _e('Option 2: Manage configurations directly'); ?></h3>

<form action="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>" method="post">
  <input type="hidden" name="subaction" value="create_configuration" />
  <input type="hidden" name="f_id" value="<?php echo $item['v_id']; ?>" />
  <div class="metabox-holder" style="margin-left: 6px;">
      <div class="stuffbox">
      <h3><label for="confname"><?php echo _e('Configuration name:'); ?></label></h3>
      <div class="inside" style="padding: 5px;">
      	<input type="text" id="confname" value="" name="confname" /> <input type="submit" value="<?php echo _e('Create configuration');?>" class="button-primary" />
          <p>Use <code>lower case characters</code>, <code>numbers</code>, <code>dashes (-)</code> and <code>underscores (_)</code> only.</p>
      </div>
    	</div>
  </div>
</form>

<div style="margin-left: 6px;">
  	<table class="widefat fixed configurations" id="configurations-table">
  	  <thead>
  	  	<tr>
  	  		<th colspan="4">
  	  			<?php echo _e('Current Configurations:'); ?>
  	  		</th>
  	  	</tr>
  	  </thead>
  	  <tbody>
          <?php if(count($configurations) == 0): ?>
            <tr>
              	<td colspan="4">
          	<?php echo _e('No configurations found.'); ?>
          		</td>
          	</tr>
          <?php endif; ?>
          
          <?php foreach($configurations AS $configuration): ?>
            <tr>
            	<td><b><?php echo $configuration['name']; ?></b><br />
            	<div class="row-actions"><span class='delete'><a class='submitdelete' href='<?php echo $main_link.'&action=edit&vid='.$item['v_id'].'&confname='.$configuration['name'].'&subaction=delete_configuration'; ?>' onclick="if ( confirm( 'You are about to delete this configuration\n  \'Cancel\' to stop, \'OK\' to delete.' ) ) { return true;}return false;">Delete</a></span></div></td>
            	<td>
            		<input type="button" value="<?php echo _e('Zoom pictures'); ?>" class="button-secondary" />
            		<?php if(isset($_POST['subaction']) && $_POST['subaction'] == 'upload_zoom' && $_POST['confname'] == $configuration['name'] && count($errors) > 0): ?>
            		<div>
            			<b>Upload errors:</b><br />
            			<ul>
            				<?php foreach($errors AS $error): ?>
            				  <li><?php echo $error; ?></li>
            				<?php endforeach; ?>
            			</ul>
            		</div>
            		<?php endif; ?>
            		<div style="display: <?php echo isset($_REQUEST['confname']) && $_REQUEST['confname'] == $configuration['name'] && isset($_REQUEST['subaction']) && $_REQUEST['subaction'] == 'delete_zoom_picture' ? 'block' : 'none'; ?>;">
            			<form action="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>" method="post" enctype="multipart/form-data">
            				<input type="hidden" name="vid" value="<?php echo $item['v_id']; ?>" />
            				<input type="hidden" name="confname" value="<?php echo $configuration['name']; ?>" />
            				<input type="hidden" name="subaction" value="upload_zoom" />
            				<input type="file" name="files[]" multiple="multiple" /> <input type="submit" class="button-primary" value="Upload" />
            			</form>
            		  	<ul>
            		  	<?php if(count($configuration['zoom_pictures']) == 0): ?>
            		  		<li><?php echo _e('No pictures found.'); ?></li>
            		  	<?php else: ?>
            		  	<li><span class="delete"><a class="deletesubmit" href="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>&subaction=delete_zoom_picture&confname=<?php echo $configuration['name']; ?>&all=1" onclick="return confirm('You are about to delete all pictures. \n \'Cancel\' to stop, \'OK\' to delete.');">Delete all pictures</a></span></li>
            		  	<?php endif; ?>
            			<?php foreach($configuration['zoom_pictures'] AS $pic): ?>
            			  	<li><span class="delete"><a class="submitdelete" href="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>&subaction=delete_zoom_picture&confname=<?php echo $configuration['name']; ?>&picture=<?php echo urlencode($pic); ?>" onclick="return confirm('You are about to delete this picture. \n \'Cancel\' to stop, \'OK\' to delete.');">X</a></span> <a href="<?php echo $this->plugin_url.'/data/'.$item['v_id'].'/'.$configuration['name'].'/view/zoom/'.$pic; ?>" onclick="window.open(this.href); return false;"><?php echo $pic; ?></a></li> 
            			<?php endforeach; ?>
            			</ul>
            		</div>
            	</td>
            	<td>
            		<input type="button" value="<?php echo _e('360° pictures'); ?>" class="button-secondary" />
            		<?php if(isset($_POST['subaction']) && $_POST['subaction'] == 'upload_normal' && $_POST['confname'] == $configuration['name'] && count($errors) > 0): ?>
            		<div>
            			<b>Upload errors:</b><br />
            			<ul>
            				<?php foreach($errors AS $error): ?>
            				  <li><?php echo $error; ?></li>
            				<?php endforeach; ?>
            			</ul>
            		</div>
            		<?php endif; ?>
            		<div style="display: <?php echo (isset($_REQUEST['confname']) && $_REQUEST['confname'] == $configuration['name'] && isset($_REQUEST['subaction']) && $_REQUEST['subaction'] == 'delete_normal_picture') ? 'block' : 'none'; ?>;">
            			<form action="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>" method="post" enctype="multipart/form-data">
            				<input type="hidden" name="vid" value="<?php echo $item['v_id']; ?>" />
            				<input type="hidden" name="confname" value="<?php echo $configuration['name']; ?>" />
            				<input type="hidden" name="subaction" value="upload_normal" />
            				<input type="file" name="files[]" multiple="multiple" /> <input type="submit" class="button-primary" value="Upload" />
            			</form>
            		  	<ul>
            		  	<?php if(count($configuration['normal_pictures']) == 0): ?>
          		  			<li><?php echo _e('No pictures found.'); ?></li>
          		  		<?php else: ?>
            		  		<li><span class="delete"><a class="deletesubmit" href="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>&subaction=delete_normal_picture&confname=<?php echo $configuration['name']; ?>&all=1" onclick="return confirm('You are about to delete all pictures. \n \'Cancel\' to stop, \'OK\' to delete.');">Delete all pictures</a></span></li>
            		  	<?php endif; ?>
            			<?php foreach($configuration['normal_pictures'] AS $pic): ?>
            			  	<li><span class="delete"><a class="submitdelete" href="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>&subaction=delete_normal_picture&confname=<?php echo $configuration['name']; ?>&picture=<?php echo urlencode($pic); ?>" onclick="return confirm('You are about to delete this picture. \n \'Cancel\' to stop, \'OK\' to delete.');">X</a></span> <a href="<?php echo $this->plugin_url.'/data/'.$item['v_id'].'/'.$configuration['name'].'/view/'.$pic; ?>" onclick="window.open(this.href); return false;"><?php echo $pic; ?></a></li> 
            			<?php endforeach; ?>
            			</ul>
            		</div>
            	</td>
            	<td>
            		<input type="button" value="<?php echo _e('Thumbnail'); ?>" class="button-secondary" />
            		<?php if(isset($_POST['subaction']) && $_POST['subaction'] == 'upload_thumbnail' && $_POST['confname'] == $configuration['name'] && count($errors) > 0): ?>
            		<div>
            			<b>Upload errors:</b><br />
            			<ul>
            				<?php foreach($errors AS $error): ?>
            				  <li><?php echo $error; ?></li>
            				<?php endforeach; ?>
            			</ul>
            		</div>
            		<?php endif; ?>
            		<div style="display: <?php echo isset($_REQUEST['confname']) && $_REQUEST['confname'] == $configuration['name'] && isset($_REQUEST['subaction']) && $_REQUEST['subaction'] == 'delete_thumbnail' ? 'block' : 'none'; ?>;">
            			<form action="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>" method="post" enctype="multipart/form-data">
            				<input type="hidden" name="vid" value="<?php echo $item['v_id']; ?>" />
            				<input type="hidden" name="confname" value="<?php echo $configuration['name']; ?>" />
            				<input type="hidden" name="subaction" value="upload_thumbnail" />
            				<input type="file" name="files[]" /> <input type="submit" class="button-primary" value="Upload" />
            			</form>
            		  	<ul>
            		  		<?php if( ! empty($configuration['thumbnail'])): ?>
            			  	<li><span class="delete"><a class="submitdelete" href="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>&subaction=delete_thumbnail&confname=<?php echo $configuration['name']; ?>" onclick="return confirm('You are about to delete this thumbnail. \n \'Cancel\' to stop, \'OK\' to delete.');">X</a></span> <a href="<?php echo $this->plugin_url.'/data/'.$item['v_id'].'/'.$configuration['name'].'/'.$configuration['thumbnail']; ?>" onclick="window.open(this.href); return false;"><?php echo $configuration['thumbnail']; ?></a></li> 
            				<?php else: ?>
            				<li><?php echo _e('No thumbnail found.'); ?></li>
            				<?php endif; ?>
            			</ul>
            		</div>
            	</td>
            </tr>
          <?php endforeach; ?>
      </tbody>
	</table>
</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#configurations-table tbody tr td input[type=button]').click(function() {
			// Scope: clicked button
			// Go up, fetch the div and toggle it
			jQuery('div', this.parentNode).toggle('fast');
		});
	});
</script>