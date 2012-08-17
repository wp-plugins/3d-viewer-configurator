<?php
/*
Plugin Name: 3D Viewer Configurator
Plugin URI: http://www.3d-eshop.eu/
Description: Wordpress 3D Viewer Configurator
Author: visualtektur and ProNego
Version: 1.5.2
Author URI: http://www.visualtektur.net/
*/

// Huge file sizes lead to a massive execution time...
set_time_limit(86400);

// Pre-2.6 compatibility
if (!defined('WP_CONTENT_URL'))
{
  define('WP_CONTENT_URL', get_option( 'siteurl' ).'/wp-content');
}
if (!defined('WP_CONTENT_DIR'))
{
  define('WP_CONTENT_DIR', ABSPATH.'wp-content');
}
if (!defined('WP_PLUGIN_URL'))
{
  define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
}
if (!defined( 'WP_PLUGIN_DIR'))
{
  define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
}
         
// main plugin class         
if (!class_exists('wp_vtpkonfigurator'))
{
  class wp_vtpkonfigurator
  {
  
    public $localizationDomain = "wp_vtpkonfigurator";
    
    protected $plugin_url;
    protected $plugin_path;
    protected $table_name;
    
    protected $db;
    
    protected $items_per_page = 20;
  

    // compatibility with old constructor
    function wp_vtpkonfigurator()
    {
      $this->__construct();
    }

    // constructor
    function __construct()
    {    
      // language setup
      $locale = get_locale();
      $mo = dirname(__FILE__)."/languages/".$this->localizationDomain."-".$locale.".mo";
      load_textdomain($this->localizationDomain, $mo);
      
      // paths
      $this->plugin_url = WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__));
      $this->plugin_path = WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__));
     
      global $wpdb;
      $this->db = &$wpdb;
      
      // database table name
      $this->table_name = $this->db->prefix.'vtpkonfigurator';
      
      // add actions
      if (is_admin())
      {
        add_action("admin_menu", array(&$this, "admin_menu_link"));
      }
      else
      {
        // script
        add_action('wp_print_scripts', array(&$this, 'add_js'));
        
        // style
        add_action('wp_print_styles', array(&$this, 'add_css'));
                    
        // content filter
        add_filter('the_content', array(&$this, 'the_content'), 0);
      }
       
    
      // install and uninstall hook
      register_activation_hook(__FILE__, array(&$this, "install"));
      register_uninstall_hook(__FILE__, array(&$this, "uninstall"));            
    }

    // install
    function install()
    {
      $sql = "CREATE TABLE IF NOT EXISTS `".$this->table_name. "` (
              `v_id` int(12) unsigned NOT NULL AUTO_INCREMENT,
              `v_name` varchar(255) COLLATE utf8_bin NOT NULL,
              `v_options` varchar(255) COLLATE utf8_bin NOT NULL,
              `v_rpm` varchar(255) COLLATE utf8_bin NOT NULL,
              PRIMARY KEY (`v_id`)
            );
            ";       
      require_once(ABSPATH.'wp-admin/includes/upgrade.php');
      dbDelta($sql);
    }

    // uninstall from DB
    function uninstall()
    {
      $sql = "DROP TABLE IF EXISTS " . $this->tablename;
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
    }
    

    // filter content
    function the_content($content)
    {
      $wp_tag = '/(\[wp_vtpkonfigurator)[^\]]*\]/';
      // get wp_vtpviewer elements
      if (preg_match_all($wp_tag, $content, $a))
      {
        while(list(, $block) = @each($a[0]))
        {
          // get parameters from block
          preg_match_all('#([^\s=]+)\s*=\s*(\'[^<\']*\'|"[^<"]*")#', $block, $matches, PREG_SET_ORDER);
          $params = array();
          foreach($matches as $attr)
          {
            $params[$attr[1]] = str_replace(array('"', "'"), array('', ''), $attr[2]);
          }
        
          // get object by ID
          if ($params['id'])
          {
            $item = $this->db->get_row("
                SELECT * FROM ".$this->table_name."
                WHERE v_id = '".$this->db->escape($params['id'])."'          
              ", ARRAY_A);
              
            if ($item['v_id'])
            {     
              $dir = $this->plugin_path.'/data/'.$item['v_id'];
              $url = $this->plugin_url.'/data/'.$item['v_id'];
              $options = $item['v_options'];
              $rpm = $item['v_rpm'];
            }
            else
            {
              $content = preg_replace($wp_tag, "WP_VTPKONFIGURATOR ERROR: Invalid ID.", $content, 1);
              continue;                    
            }
          }
          else
          if ($params['path'])
          {            
            $dir = $params['path'];
            $url = home_url($params['path']);
            $options = $params['options'];
            $rpm = $params['rpm'];
          }
          else
          {
            $content = preg_replace($wp_tag, "WP_VTPKONFIGURATOR ERROR: Invalid options.", $content, 1);
            continue;        
          }      
          
          if (!is_dir($dir))
          {
            $content = preg_replace($wp_tag, "WP_VTPKONFIGURATOR ERROR: Directory not found.", $content, 1);
            continue;                  
          }

          // Get Data (data/id/<folderN>/<images>)
          $data = $this->get_data($dir);
   	 		
   	 	  // Output vtpconfigurator
          $el = '<div class="vtpkonfigurator" data="'.htmlspecialchars(json_encode($data)).'"';
          
          if ($options || $rpm)
          {  
            $el.= ' options="'.$options.($options !== '' ? ',' : '').'rpm:'.$rpm.'"';
          }
          $el.= '><img src="'.$files[0].'" /></div>';
          
          $content = preg_replace($wp_tag, $el, $content, 1);
        } 
      }                  
      return $content;
    }
    
    function get_images($path)
    {
      $imgs = array();
      $relpath = str_replace(ABSPATH.'/', '', $path);
      foreach(preg_grep('/\.(jpe?g|gif|png)$/i', glob($path.'*')) as $l)
        $imgs[] = get_option('siteurl').'/'.$relpath.'/'.basename($l);
      
      return $imgs;
    }  

    function get_data($dir)
    {
      if( ! is_dir($dir))
        return array();
      
      // Make sure we got a relative path
      $dir = str_replace(ABSPATH, '', $dir);

      $dh = opendir(ABSPATH.'/'.$dir);
      $data = array();

      while($file = readdir($dh))
      {
      	if($file{0} == '.')
      	  continue;
      
      	$file = $dir.'/'.$file;
      	$view_path = $file.'/view/';
      
      	// Entry is a dir, fetch its name, put it into our data array and fetch its images
      	if(is_dir(ABSPATH.'/'.$file) && is_dir(ABSPATH.'/'.$view_path))
      	{
      		$images = $this->get_images(ABSPATH.'/'.$view_path);
      
      		// No images found, skip configuration
      		if(count($images) == 0)
      		  continue;
      
      		$thumb = $this->get_thumb($file);
      
      		if($thumb === FALSE)
      		  $thumb = $images[0];
      
      		$data[basename($file)] = array(
              'imgs'  => $images,
              'thumb' => $thumb,
      		);
      	}
      }
      
      return $data;
    }
    
    function get_thumb($path)
    {
      foreach(array('jpg', 'png', 'gif', 'jpeg') AS $ext)
      {
        if(file_exists($path.'/thumb.'.$ext))
          return get_option('siteurl').'/'.$path.'/thumb.'.$ext;
      }
      
      return FALSE;
    }
    
    // js enqueue
    function add_js()
    {
      wp_enqueue_script('mootools', $this->plugin_url.'/js/mootools.js');
      wp_enqueue_script('wp_vtpkonfigurator_pviewer', $this->plugin_url.'/js/pviewer.min.js', array('mootools'));
      wp_enqueue_script('wp_vtpkonfigurator', $this->plugin_url.'/js/vtpkonfigurator.min.js', array('mootools'));
    }
    
    // css enqueue
    function add_css()
    {
      wp_enqueue_style('wp_vtpkonfigurator', $this->plugin_url.'/js/vtpkonfigurator.css');
      wp_enqueue_style('wp_vtpkonfigurator_wpfix', $this->plugin_url.'/js/vtpkonfigurator_wpfix.css');
    }
        
    // admin menu link
    function admin_menu_link()
    {
      //add_options_page('WP-VTP Viewer', 'WP-VTP Viewer', 10, basename(__FILE__), array(&$this, 'admin_options_page'));
      //add_filter('plugin_action_links_'.plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2);
      add_menu_page('3D Viewer Configurator', '3D Viewer Configurator', 10, basename(__FILE__), array(&$this, 'admin_page')); 
      add_filter('contextual_help', array(&$this, 'admin_help'), 10, 3);
    }
        
    // plugins page - plugin actions
    function filter_plugin_actions($links, $file)
    {
      $settings_link = '<a href="options-general.php?page='.basename(__FILE__) . '">' . __('Settings') . '</a>';
      array_unshift($links, $settings_link); 
      return $links;
    }
        
    // plugin options page
    function admin_options_page()
    {           
      //require_once $this->thispluginpath.'admin_settings.php';
    }
    
    
    function rrmdir($dir)
    { 
      if(substr($dir, -1) != '/' && substr($dir, -1) != '\\')
        $dir .= '/';
      
      if (is_dir($dir))
      { 
        $objects = scandir($dir); 
        foreach ($objects as $object)
        { 
          if ($object != "." && $object != "..")
          { 
            if (is_dir($dir.$object)) $this->rrmdir($dir.$object); else unlink($dir.$object); 
          } 
        } 
        reset($objects); 
         
        rmdir($dir);
      } 
    }         
    
    
    // plugin admin page
    function admin_page()
    {
      $main_link = admin_url('admin.php?page='.basename(__FILE__));
      
      if ($_GET['action'] == 'delete')
      {
        $this->db->query("
            DELETE FROM ".$this->table_name." WHERE v_id = ".$this->db->escape($_GET['vid'])."
            ");

        $path = $this->plugin_path.'/data/'.$_GET['vid'];                              
        if (is_dir($path))
        {
          $this->rrmdir($path);
        }
      }
      
      if ($_POST['f_ok'])
      {
        if (!$_POST['f_id'])
        {
          $this->db->query("
              INSERT INTO ".$this->table_name."
              SET v_name = '".$this->db->escape($_POST['f_name'])."',
                  v_options = '".$this->db->escape(@implode(',', $_POST['f_options']))."',
                  v_rpm = '".$this->db->escape($_POST['f_rpm'])."' 
          ");
          
          $id = $this->db->insert_id;
          
          die('<script type="text/javascript">location.href="'.$main_link.'&action=edit&vid='.$id.'";</script>');
        }
        else
        {
          $this->db->query("
              UPDATE ".$this->table_name."
              SET v_name = '".$this->db->escape($_POST['f_name'])."',
                  v_options = '".$this->db->escape(@implode(',', $_POST['f_options']))."',
                  v_rpm = '".$this->db->escape($_POST['f_rpm'])."'
              WHERE v_id = ".$this->db->escape($_POST['f_id'])." 
          ");
          $id = $_POST['f_id'];          
        }
        
        // prepare path
        $path = $this->plugin_path.'/data/'.$id;                              
        $files = $_FILES['f_files'];
        
        if ($files['size'][0] > 0)
        {
          // remove if exists
          if (is_dir($path))
          {
           # $this->rrmdir($path);
          }
          else
           mkdir($path);        
        }
        
        // one zip file
        if ((count($files['name']) == 1)&&($files['size'][0] > 0)&&(stripos($files['name'][0], '.zip') !== false))
        {
          $zip = new ZipArchive;
          if ($zip->open($files['tmp_name'][0]))
          {
            $zip->extractTo($path.'/');
            $zip->close();
            
            // Remove hidden dirs / mac things
            $dh = opendir($path.'/');
            
            if($dh)
            {
              while($file = readdir($dh))
              {
                if(is_dir($path.'/'.$file) && $file != '..' && $file != '.' && ($file{0} == '.' || $file == '__MACOSX'))
                  rec_rmdir($path.'/'.$file);
              }
              closedir($dh);
            }
          }        
        }
        /*else
        { // more images         
          for($i=0,$n=count($files['name']);$i<$n;$i++)
          {
            if ($files['size'][$i] > 0)
            {
              move_uploaded_file($files['tmp_name'][$i], $path.'/'.$files['name'][$i]);
            }
          }
        }*/                           
      }
      
      
      if (($_GET['action'] == 'create')||($_GET['action'] == 'edit'))
      {              
        if ($_GET['action'] == 'edit')
        {
          $subact = isset($_REQUEST['subaction']) ? $_REQUEST['subaction'] : 'edit';
          
          $item = $this->db->get_row("
                    SELECT * FROM ".$this->table_name."
                    WHERE v_id = '".$this->db->escape($_GET['vid'])."'          
                ", ARRAY_A);
          
          if($subact == 'edit')
          {
            $item['options'] = explode(',', $item['v_options']);
            
            $dir = $this->plugin_path.'/data/'.$_GET['vid'];
            $url = $this->plugin_url.'/data/'.$_GET['vid'];
            
            $files = $this->get_data($dir);
            
            if($files === array())
            $files = FALSE;
          }
          elseif($subact == 'create_configuration')
          {
            if($item !== NULL)
            {
              // Validation
              if(preg_match('/^[a-z0-9\-_]+$/', $_POST['confname']))
              {
                mkdir($this->plugin_path.'/data/'.$_GET['vid'].'/'.$_POST['confname'].'/view/zoom', 0777, true);
              }
            }
          }
          elseif($subact == 'delete_configuration')
          {
             $this->rrmdir($this->plugin_path.'/data/'.$_GET['vid'].'/'.$_GET['confname']);
             echo '<script type="text/javascript">location.href="'.$main_link.'&action=edit&vid='.$_GET['vid'].'";</script>';
          }
          elseif($subact == 'upload_zoom')
          {
            $errors = $this->_handle_partly_upload($this->plugin_path.'/data/'.$_POST['vid'].'/'.$_POST['confname'].'/view/zoom/');
          }
          elseif($subact == 'upload_normal')
          {
            $errors = $this->_handle_partly_upload($this->plugin_path.'/data/'.$_POST['vid'].'/'.$_POST['confname'].'/view/');
          }
          elseif($subact == 'upload_thumbnail')
          {
            $errors = $this->_handle_partly_upload($this->plugin_path.'/data/'.$_POST['vid'].'/'.$_POST['confname'], FALSE);
          }
          elseif($subact == 'delete_thumbnail')
          {
            $path = $this->plugin_path.'/data/'.$_GET['vid'].'/'.$_GET['confname'].'/';
            $thumb = $this->_get_thumbnail($path);
            
            if($thumb)
              unlink($path.$thumb);

            #echo '<script type="text/javascript">location.href="'.$main_link.'&action=edit&vid='.$_GET['vid'].'";</script>';
          }
          elseif($subact == 'delete_normal_picture' || $subact == 'delete_zoom_picture')
          {
          	$path = $this->plugin_path.'/data/'.$_GET['vid'].'/'.$_GET['confname'].'/view/';
          
          	if($subact == 'delete_zoom_picture')
          	  $path .= 'zoom/';

          	if(isset($_GET['picture']))
          	{
            	if(file_exists($path.$_GET['picture']))
            	  unlink($path.$_GET['picture']);
          	}
          	elseif(isset($_GET['all']) && $_GET['all'] == '1')
          	{
          	    $files = scandir($path);
          	    
          	    foreach($files AS $file)
          	    {
          	      if( ! is_dir($path.$file))
          	        unlink($path.$file);
          	    }
          	}
          	
          	#echo '<script type="text/javascript">location.href="'.$main_link.'&action=edit&vid='.$_GET['vid'].'";</script>';
          }
        }
        else
        {
          $item = array();
        }      
      }
      else
      {
        $create_konfigurator_link = $main_link.'&action=form';        
                
        /*
        // get items
        $page = $_GET['pg'];
        if (!$page) $page = 1;
        
        // get viewers count
        $count = $this->db->get_var($this->db->prepare( "SELECT COUNT(v_id) FROM ".$this->table_name));
        
        $it = ceil($count/$this->items_per_page);
        if ($page > $it) $page = 1;
        LIMIT ".(($page-1)*$this->items_per_page).",".$this->items_per_page."              
        */
        $list = $this->db->get_results("
                      SELECT * FROM ".$this->table_name."                                            
                      ", ARRAY_A);

      }
      
      $configurations = $this->_get_configurations($_GET['vid']);
    
      require_once $this->plugin_path.'/admin.php';
      
    }
    
    function _handle_partly_upload($target, $multiple=TRUE)
    {
      if( ! is_dir($target))
        mkdir($target, 0777, true);
      
      if(substr($target, -1) != '/' && substr($target, -1) != '\\')
        $target .= '/';
      
      $errors = array();
      
      if( ! isset($_FILES['files']) || ! is_array($_FILES['files']['error']))
      {
        $errors[] = 'No files were uploaded.';
        return $errors;
      }
      
      foreach($_FILES['files']['error'] AS $key => $err)
      {
        if($err ==  UPLOAD_ERR_OK)
        {
          $name = $_FILES['files']['name'][$key];
          
          if( ! $multiple)
          {
            if( ! preg_match('/(\.jpe?g|\.gif|\.png)$/i', $name, $matches))
            {
              $errors[] = 'Invalid file format for single upload: '.$name;
              continue;
            }
            else
            {
              // If single upload is required, we got a thumb.xxx
              // Remove a previous thumb in case we got a thumb with a different extension
              $contents = scandir($target);
              
              foreach($contents AS $content)
              {
                if(in_array(strtolower($content), array('thumb.jpg', 'thumb.png', 'thumb.gif', 'thumb.jpeg')))
                  unlink($target.$content);
              }
              
              move_uploaded_file($_FILES['files']['tmp_name'][$key], $target.'thumb'.$matches[1]);
              return array();
            }
          }
          else
          {
            // Simple image file?
            if(preg_match('/\.jpe?g|\.gif|\.png$/i', $name))
            {
              move_uploaded_file($_FILES['files']['tmp_name'][$key], $target.$name);
            }
            elseif(preg_match('/\.zip$/i', $name))
              $errors = array_merge($errors, $this->_handle_zip_upload($_FILES['files']['tmp_name'][$key], $target));
            else
              $errors[] = 'Invalid file format: '.$name;
          }
        }
      }
      
      return $errors;
    }
    
    function _handle_zip_upload($file, $target)
    {
      $errors = array();
      
      // Create tmp file
      $tmpfile = tempnam('asdfg', ''); // if asdfg doesnt exist, it will fetch the tmpdir
      
      if($tmpfile === FALSE)
      {
        $errors[] = 'Could not create temporary directory to unzip the files.';
        return $errors;
      }
      
      // Delete tmp file and create a folder with its name
      unlink($tmpfile);
      
      mkdir($tmpfile, 0777, true);
      
      $zip = new ZipArchive;
      if ($zip->open($file))
      {
        // Unzip the zip to our tmp dir
      	$zip->extractTo($tmpfile);
      	$zip->close();
      	
      	// Move all image files in this zip to the target destination
      	$errros = $this->_move_unzipped_images($tmpfile, $target, $errors);
      	
      	// Remove tmp folder
      	#$this->rrmdir($tmpfile);
      	rec_rmdir($tmpfile);
      }
      else
        $errors[] = 'Could not extract zip archive.';
      
      return $errors;
    }
    
    function _move_unzipped_images($tmpfile, $target, $errors=array())
    {
      if( ! is_dir($tmpfile))
        return $errors;

      if(substr($tmpfile, -1) != '/' && substr($tmpfile, -1) != '\\')
        $tmpfile .= '/';
      
      if(substr($target, -1) != '/' && substr($target, -1) != '\\')
        $target .= '/';
      
      $dh = opendir($tmpfile);
      
      if( ! $dh)
      {
        $errors[] = 'Could not open temp folder to read the unzipped images';
        return $errors;
      }
      
      while($file = readdir($dh))
      {
        if($file{0} == '.')
          continue;
        
        $path = $tmpfile.$file;
        
        if(is_dir($path))
        {
          $errors = $this->_move_unzipped_images($path, $target, $errors);
          continue;
        }
        
        if( ! preg_match('/\.jpe?g|\.gif|\.png$/i', $file))
        {
          $errors[] = 'Invalid file format in Zip archive: '.$file;
          continue;
        }
        
        if(file_exists($target.$file))
          unlink($target.$file);
        
        rename($path, $target.$file);
      }
      
      return $errors;
    }
    
    function _get_configurations($id)
    {
      $confs = array();
      
      $dir = $this->plugin_path.'/data/'.$_GET['vid'];
      
      if( ! file_exists($dir))
        return $confs;
      
      $dh = opendir($dir);
      while($file = readdir($dh))
      {
        $path = $dir.'/'.$file;
        
        if($file{0} == '.')
          continue;
        
        if(is_dir($path))
        {
          $conf = array(
            'name' => $file,
            'zoom_pictures' => $this->_get_image_names($path.'/view/zoom'),
            'normal_pictures' => $this->_get_image_names($path.'/view'),
            'thumbnail'	=> $this->_get_thumbnail($path),
          );
          
          $confs[] = $conf;
        }
      }
      
      closedir($dh);
      return $confs;
    }
    
    function _get_thumbnail($path)
    {
      if( ! is_dir($path))
        return null;
      
      $dh = opendir($path);
      
      while($file = readdir($dh))
      {
        if(preg_match('/^thumb\.jpe?g|\.gif|\.png$/i', $file))
        {
          closedir($dh);
          return $file;
        }   
      }
      
      closedir($dh);
      return null;
    }
    
    function _get_image_names($path)
    {
      if( ! is_dir($path))
        return array();
      
      $files = scandir($path);
      
      $arr = array();
      
      foreach($files AS $file)
      {
        if(preg_match('/\.jpe?g|\.png|\.gif$/i', $file))
          $arr[] = $file;
      }
      
      return $arr;
    }
    
    function admin_help($contextual_help, $screen_id, $screen)
    {
      global $my_plugin_hook;
	    if ($screen_id == 'toplevel_page_wp_vtpkonfigurator')
      {
        require_once $this->plugin_path.'/help.php';        
	    }      
	    return $contextual_help;
    }
                             
  }
}


if (class_exists('wp_vtpkonfigurator'))
{
  $wp_vtpkonfigurator_var = new wp_vtpkonfigurator();
}

/**
 * Source: http://aktuell.de.selfhtml.org/artikel/php/verzeichnisse/
 * The one implemented in the class above doesnt work for some reason in all cases
 */
function rec_rmdir ($path) {
	// schau' nach, ob das ueberhaupt ein Verzeichnis ist
	if (!is_dir ($path)) {
		return -1;
	}
	// oeffne das Verzeichnis
	$dir = @opendir ($path);

	// Fehler?
	if (!$dir) {
		return -2;
	}

	// gehe durch das Verzeichnis
	while (($entry = @readdir($dir)) !== false) {
		// wenn der Eintrag das aktuelle Verzeichnis oder das Elternverzeichnis
		// ist, ignoriere es
		if ($entry == '.' || $entry == '..') continue;
		// wenn der Eintrag ein Verzeichnis ist, dann
		if (is_dir ($path.'/'.$entry)) {
			// rufe mich selbst auf
			$res = rec_rmdir ($path.'/'.$entry);
			// wenn ein Fehler aufgetreten ist
			if ($res == -1) {
				// dies duerfte gar nicht passieren
				@closedir ($dir); // Verzeichnis schliessen
				return -2; // normalen Fehler melden
			} else if ($res == -2) {
				// Fehler?
				@closedir ($dir); // Verzeichnis schliessen
				return -2; // Fehler weitergeben
			} else if ($res == -3) {
				// nicht unterstuetzer Dateityp?
				@closedir ($dir); // Verzeichnis schliessen
				return -3; // Fehler weitergeben
			} else if ($res != 0) {
				// das duerfe auch nicht passieren...
				@closedir ($dir); // Verzeichnis schliessen
				return -2; // Fehler zurueck
			}
		} else if (is_file ($path.'/'.$entry) || is_link ($path.'/'.$entry)) {
			// ansonsten loesche diese Datei / diesen Link
			$res = @unlink ($path.'/'.$entry);
			// Fehler?
			if (!$res) {
				@closedir ($dir); // Verzeichnis schliessen
				return -2; // melde ihn
			}
		} else {
			// ein nicht unterstuetzer Dateityp
			@closedir ($dir); // Verzeichnis schliessen
			return -3; // tut mir schrecklich leid...
		}
	}

	// schliesse nun das Verzeichnis
	@closedir ($dir);

	// versuche nun, das Verzeichnis zu loeschen
	$res = @rmdir ($path);

	// gab's einen Fehler?
	if (!$res) {
		return -2; // melde ihn
	}

	// alles ok
	return 0;
}

?>