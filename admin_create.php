<h3>
<?php
if ($_GET['action'] == 'edit') _e('Edit configurator');
else _e('Create configurator');
?> 
</h3>
<div style="float: left; width: 470px;">
<form name="edit_form" method="POST" action="<?php echo $main_link; ?>" enctype="multipart/form-data">
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
      <td><label for="f_rpm"><?php _e('Speed'); ?></label></td><td><input id="f_rpm" type="text" size="10" name="f_rpm" title="Speed of the rotation." value="<?php echo empty($item['v_rpm']) ? '30' : $item['v_rpm']; ?>" /></td>
    </tr>
    <tr>
      <td></td>
      <td>
        <input class="button-primary" type="submit" name="f_ok" title="<?php _e('Ok'); ?>" value="<?php _e('Save & go to upload'); ?>" id="submitbutton" />
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