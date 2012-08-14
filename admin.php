<div class="wrap">
<div id="icon-plugins" class="icon32"></div>
<h2>WP-VTP Konfigurator</h2>
<br />
<?php
if (($_GET['action'] == 'edit')||($_GET['action'] == 'create'))
{
  if($_GET['action'] == 'edit')
    require(str_replace('admin.php', '', __FILE__).'admin_edit.php');
  else
    require(str_replace('admin.php', '', __FILE__).'admin_create.php');
}
else
{
  require(str_replace('admin.php', '', __FILE__).'admin_list.php');
}
?>

</div>
