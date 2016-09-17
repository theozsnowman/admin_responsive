<?php
/*
  $Id: cm_nb_generic.php, v1.0.1 20160321 Kymation$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 James C Keebaugh

  Released under the GNU General Public License
*/

  class cm_nb_generic {
    public $version = '1.0.1';
    public $code = '';
    public $group = '';
    public $title = '';
    public $description = '';
    public $sort_order = 0;
    public $enabled = false;
    public $side = 'left';

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_NAVBAR_GENERIC_TITLE;
      $this->description = MODULE_CONTENT_NAVBAR_GENERIC_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_NAVBAR_GENERIC_ERROR_MAIN_MODULE . '</div>';

      if ( defined('MODULE_CONTENT_NAVBAR_GENERIC_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_NAVBAR_GENERIC_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_NAVBAR_GENERIC_STATUS == 'True');
        $this->side = ((MODULE_CONTENT_NAVBAR_GENERIC_CONTENT_PLACEMENT === 'left') ? 'left' : 'right');
      }
    }

    public function getOutput() {
      if ( MODULE_CONTENT_NAVBAR_GENERIC_LINK_TYPE === 'internal' && MODULE_CONTENT_NAVBAR_GENERIC_LINK !== '' ) {
        $generic_link = tep_href_link(MODULE_CONTENT_NAVBAR_GENERIC_LINK);
      } elseif ( MODULE_CONTENT_NAVBAR_GENERIC_LINK_TYPE === 'external' && MODULE_CONTENT_NAVBAR_GENERIC_LINK !== '' ) {
        $generic_link = MODULE_CONTENT_NAVBAR_GENERIC_LINK;
      }
      
      ob_start();
      include DIR_WS_MODULES . 'content/navbar/templates/' . basename(__FILE__);
      $template = ob_get_clean();

      return $template;
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_CONTENT_NAVBAR_GENERIC_STATUS');
    }

    public function install() {
	  global $multi_stores_config;		
      tep_db_query("insert into " . $multi_stores_config . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Module Version', 'MODULE_CONTENT_NAVBAR_GENERIC_VERSION', '" . $this->version . "', 'The version of this module that you are running.', '6', '0', 'tep_cfg_disabled(', now() ) ");
      tep_db_query("insert into " . $multi_stores_config . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Generic Module', 'MODULE_CONTENT_NAVBAR_GENERIC_STATUS', 'True', 'Should the generic link be shown in the navigation bar?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . $multi_stores_config . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_NAVBAR_GENERIC_SORT_ORDER', '9190', 'Sort order of display. Lowest is displayed first.', '6', '2', now())");
      tep_db_query("insert into " . $multi_stores_config . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_CONTENT_NAVBAR_GENERIC_CONTENT_PLACEMENT', 'left', 'Should the module be loaded on the left or right side of the navbar?', '6', '3', 'tep_cfg_select_option(array(\'left\', \'right\'), ', now())");
      tep_db_query("insert into " . $multi_stores_config . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Link Type', 'MODULE_CONTENT_NAVBAR_GENERIC_LINK_TYPE', 'internal', 'Is this an internal or external link?', '6', '4', 'tep_cfg_select_option(array(\'internal\', \'external\'), ', now())");
      tep_db_query("insert into " . $multi_stores_config . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Link', 'MODULE_CONTENT_NAVBAR_GENERIC_LINK', 'index.php', 'The osCommerce filename if internal, otherwise the full URL.', '6', '5', now())");
    }

    public function remove() {
	  global $multi_stores_config;		
      tep_db_query("delete from " . $multi_stores_config . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    public function keys() {
      $keys = array();
      $keys[] = 'MODULE_CONTENT_NAVBAR_GENERIC_VERSION';
      $keys[] = 'MODULE_CONTENT_NAVBAR_GENERIC_STATUS';
      $keys[] = 'MODULE_CONTENT_NAVBAR_GENERIC_SORT_ORDER';
      $keys[] = 'MODULE_CONTENT_NAVBAR_GENERIC_CONTENT_PLACEMENT';
      $keys[] = 'MODULE_CONTENT_NAVBAR_GENERIC_LINK_TYPE';
      $keys[] = 'MODULE_CONTENT_NAVBAR_GENERIC_LINK';
       
      return $keys;
    }
   function enable() {
	  global $multi_stores_config;			
            tep_db_query("update " . $multi_stores_config  . " set configuration_value = 'True' where configuration_key = 'MODULE_CONTENT_NAVBAR_GENERIC_STATUS'");
            $this->enabled = (MODULE_CONTENT_NAVBAR_GENERIC_STATUS == 'True');
            

    }
    function disable() {
	  global $multi_stores_config;			
            tep_db_query("update " . $multi_stores_config  . " set configuration_value = 'False' where configuration_key = 'MODULE_CONTENT_NAVBAR_GENERIC_STATUS'");
            $this->enabled = (MODULE_CONTENT_NAVBAR_GENERIC_STATUS == 'False');

    }		
  } // End class


  ////////////////////////////////////////////////////////////////////////////
  //                                                                        //
  //  This is the end of the module class.                                  //
  //  Everything past this point is an independent function, not a method.  //
  //                                                                        //
  ////////////////////////////////////////////////////////////////////////////


  ////
  // Function to show a disabled entry (Value is shown but cannot be changed)
  if( !function_exists( 'tep_cfg_disabled' ) ) {
    function tep_cfg_disabled( $value ) {
      return tep_draw_input_field( 'configuration_value', $value, ' disabled' );
    }
  }