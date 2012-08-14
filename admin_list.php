

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


<p>
  <a class="button-secondary" href="<?php echo $main_link; ?>&action=create" title="<?php _e('Create konfigurator'); ?>"><?php _e('Create konfigurator'); ?></a>
</p>

<table class="widefat">
<thead>
    <tr>
        <th>ID</th>
        <th><?php _e('Name'); ?></th>
        <th><?php _e('Code'); ?></th>
        <th><?php _e('Actions'); ?></th>
    </tr>
</thead>
<tfoot>
    <tr>
        <th>ID</th>
        <th><?php _e('Name'); ?></th>
        <th><?php _e('Code'); ?></th>
        <th><?php _e('Action'); ?></th>
    </tr>
</tfoot>
<tbody>
  <?php
  $c = 0;
  while(list(,$item) = @each($list))
  {
    $c++;
  ?>
   <tr <?php echo $c%2?' class="alternate"':''?>>
     <td><?php echo $item['v_id']; ?></td>
     <td><?php echo $item['v_name']; ?></td>
     <td>[wp_vtpkonfigurator id="<?php echo $item['v_id']; ?>"]</td>
     <td><a href="<?php echo $main_link; ?>&action=edit&vid=<?php echo $item['v_id']; ?>">Edit</a> | 
     <span class="delete"><a href="<?php echo $main_link; ?>&action=delete&vid=<?php echo $item['v_id']; ?>" onclick="return window.confirm('Are you sure?');">Delete</a></span></td>
   </tr>
  <?php
  }
  ?>
</tbody>
</table>