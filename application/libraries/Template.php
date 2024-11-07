<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/*
 * Class for getting template info
 * 
 * Get information about template and browsers
 * used for fetch themes, css, js
 * used for get browsers, css path, js path etc
 * @author trilok
 */

require_once BASEPATH . '/core/Model.php';

class Template extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  public function getBrowser() {
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $b = $_SERVER['HTTP_USER_AGENT'];

      $bw = NULL;
      if (preg_match('/Firefox\//', $b)) {
        $bw = 'firefox';
      }
      if (preg_match('/Chrome\//', $b)) {
        $bw = 'chrome';
      }
      if (preg_match('/MSIE 7\./', $b)) {
        $bw = 'ie7';
      }
      if (preg_match('/MSIE 8\./', $b)) {
        $bw = 'ie8';
      }
      if (preg_match('/MSIE 9\./', $b)) {
        $bw = 'ie9';
      }
      if (preg_match('/Safari\//', $b) && preg_match('/Version\//', $b)) {
        $bw = 'safari';
      }
      if (preg_match('/iPhone/', $b) && preg_match('/Mobile\//', $b)) {
        $bw = 'iphone';
      }
      if (preg_match('/Android/', $b) && preg_match('/Mobile\/', $b)) {
        $bw = 'android';
      }
      return $bw;
    }
  }

  public function getThemes() {
    $themes = array();
    $skinsPath = str_replace('system', 'skin', BASEPATH);
    $dir = scandir($skinsPath);
    unset($dir[0]);
    unset($dir[1]);
    sort($dir);
    return $dir;
  }

  public function getStyles($theme = NULL) {
    $theme = ($theme === NULL || empty($theme)) ? 'default' : $theme;

    $styles = get_dir_file_info('skin/' . $theme . '/css/', true);
	$sessionCSS = 'red_screen.css';
	
    if (count($styles) > 0) {
      $css = array();
      foreach ($styles as $style) {

        if (end(explode('.', $style['name'])) === 'css' && substr($style['name'], 0, 2) !== '__') {
          $css[]['css'] = $style['name'];
        }
      }
    }
	
     $browser = $this->getBrowser();
    if (!is_null($browser)) {
      if (file_exists('skin/' . $theme . '/css/__' . $browser . '.css')) {
        $css[count($css)]['css'] = '__' . $browser . '.css';
      }
    }
	
	
    return $css;
  }

  public function getScripts($theme = NULL) {
    $theme = ($theme === NULL || empty($theme)) ? 'default' : $theme;

    $scripts = get_dir_file_info('skin/' . $theme . '/scripts/', true);
    $js = array();
    if (count($scripts) > 0) {
      foreach ($scripts as $script) {
        if (end(explode('.', $script['name'])) === 'js') {
          $js[]['script'] = $script['name'];
        }
      }
    }

    if (is_array($js)) {
      sort($js);
    }
    return $js;
  }

  public function getThemeUrl($theme = NULL) {
    $theme = ($theme === NULL || empty($theme)) ? 'default' : $theme;
    $url = base_url() . 'skin/' . $theme . '/';
    return $url;
  }

  public function getThemeStyleUrl($theme = NULL) {
    $theme = ($theme === NULL || empty($theme)) ? 'default' : $theme;
    $url = base_url() . 'skin/' . $theme . '/css/';
    return $url;
  }
  
  public function getThemeScriptUrl($theme = NULL) {
    $theme = ($theme === NULL || empty($theme)) ? 'default' : $theme;
    $url = base_url() . 'skin/' . $theme . '/scripts/';
    return $url;
  }

}

/* End of file Template.php */
/* Location: ./application/liabraries/Template.php */