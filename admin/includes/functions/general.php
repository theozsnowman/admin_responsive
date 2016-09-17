<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

////
// Get the installed version number
  function tep_get_version() {
    static $v;

    if (!isset($v)) {
      $v = trim(implode('', file(DIR_FS_CATALOG . 'includes/version.php')));
    }

    return $v;
  }
  
// conversion bootstrap icons to fontawesome
 function bootstrap_icon_fontawesome( $icon ) {	 
	$array_bootstrap    = array(  "circle-arrow-right" ,
								  "move",
                                  "transfer",
                                  "question-sign",
								  "ok",
								  "info-sign",
								  "check",
								  "clock",
								  "ok-sign",								  
								  "th",
								  "screenshot",
								  "remove-sign",
								  "folder-open",
								  "list-alt",
								  "th-list",
								  "disk",
								  "transfer",
								  "remove-circle",
								  "ok-circle",
								  "ban-circle",
								  "sort-by-alphabet",
								  "sort-by-alphabet-alt",
								  "sort-by-order",
								  "sort-by-order-alt",
								  "triangle-bottom",
								  "triangle-top",
								  "eye-open",
								  "equalizer",
								  "import",
								  "open",
								  "log-in") ;
	$array_font    = array(  "arrow-circle-o-right",
							 "sign-out",
							 "random",
							 "question",
							 "check-square-o",
							 "info-circle",
							 "check-square-o",
							 "clock-o",
							 "check-circle",
							 "th-large",
							 "paperclip",
							 "times-circle",
							 "unlock",
							 "list-ol",
							 "money", 
							 "server",
							 "forward",
							 "times-circle-o",
							 "plus-circle",
							 "trash",
							 "sort-alpha-asc",
							 "sort-alpha-desc",
							 "sort-amount-asc",
							 "sort-amount-desc",
							 "caret-down",
							 "caret-up",
							 "eye",
							 "bar-chart",
							 "external-link",
							 "upload",
							 "sign-in") ;
	$key   =  array_search($icon, $array_bootstrap ) ;	
	if ( $key !== false ) {    		
		   $name = $array_font[$key] ;
    } else {
		   $name = $icon ;
	}   	  
   return $name;
}

 function glyphicon_icon_to_fontawesome( $icon = "" ) {	 
    if ( $icon != "" ) { 
/*	  if ( MODULE_JAVA_CSS_FONTAWESOME_CSS_STATUS == 'True' ) {  // font awesome
	     $icon_size = '';
	     switch ( MODULE_JAVA_CSS_FONTAWESOME_SIZE_ICONS ) {
			case "X Large" :
				$icon_size = ' fa-lg';
				break;
			case "2X Large" :
				$icon_size = ' fa-2x';
				break;
			case "3X Large" :
				$icon_size = ' fa-3x';
				break;			
			case "4X Large" :
				$icon_size = ' fa-4x';
				break;
			case "5X Large" :
				$icon_size = ' fa-5x';
				break;					
		 }
*/		  
	     $icon_string = "fa fa-" . bootstrap_icon_fontawesome( $icon ) . $icon_size ;
//	  } else {
//	     $icon_string = "glyphicon glyphicon-" . $icon ;		  
//	  }
	}

   return $icon_string;
}
  
// BOF: XSell
function rdel($path, $deldir = true) { 
        // $path is the path on the php file
        // $deldir (optional, defaults to true) allow if you want to delete the directory (true) or empty only (false)
  
        // it first checks the name of the directory contents "/" at the end, if we add it
        if ($path[strlen($path)-1] != "/") 
                $path .= "/"; 
  
        if (is_dir($path)) { 
                $d = opendir($path); 
  
                while ($f = readdir($d)) { 
                        if ($f != "." && $f != "..") { 
                                $rf = $path . $f; // path of the php file 
  
                                if (is_dir($rf)) // if it is the directory of the function recursively call
                                        rdel($rf); 
                                else // if you delete the file
                                        unlink($rf); 
                        } 
                } 
                closedir($d); 
  
                if ($deldir) // if $deldir is true you delete the directory
                        rmdir($path); 
        } 
        else { 
                unlink($path); 
        } 
} 
// EOF: XSell
////
// BOF Enable & Disable Categories

// Sets the status of a category and all nested categories and products whithin.
  function tep_set_categories_status($category_id, $status) {
    if ($status == '1') {
      tep_db_query("update " . TABLE_CATEGORIES . " set categories_status = '1', last_modified = now() where categories_id = '" . $category_id . "'");
      $tree = tep_get_category_tree($category_id);
      for ($i=1; $i<sizeof($tree); $i++) {
        tep_db_query("update " . TABLE_CATEGORIES . " set categories_status = '1', last_modified = now() where categories_id = '" . $tree[$i]['id'] . "'");
      }
    } elseif ($status == '0') {
      tep_db_query("update " . TABLE_CATEGORIES . " set categories_status = '0', last_modified = now() where categories_id = '" . $category_id . "'");
      $tree = tep_get_category_tree($category_id);
      for ($i=1; $i<sizeof($tree); $i++) {
        tep_db_query("update " . TABLE_CATEGORIES . " set categories_status = '0', last_modified = now() where categories_id = '" . $tree[$i]['id'] . "'");
      }
    }
  }
// EOF Enable & Disable Categories

// Redirect to another page or site
  function tep_redirect($url) {
    global $logger;

    if ( (strstr($url, "\n") != false) || (strstr($url, "PHP_EOL") != false) || (strstr($url, "\r") != false) ) {
      tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false));
    }

    // 2.3.3
    if ( strpos($url, '&amp;') !== false ) {
      $url = str_replace('&amp;', '&', $url);
    }
	// eof 2.3.3

    header('Location: ' . $url);

    if (STORE_PAGE_PARSE_TIME == 'true') {
      if (!is_object($logger)) $logger = new logger;
      $logger->timer_stop();
    }
// 8703 mysqli
    tep_session_close();
// end 8703	
    exit;
  }

////
// Parse the data used in the html tags to ensure the tags will not break
  function tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }

  function tep_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
//      return htmlspecialchars($string);
        return htmlspecialchars($string, ENT_QUOTES, CHARSET); 
    } else {
      if ($translate == false) {
        return tep_parse_input_field_data($string, array('"' => '&quot;'));
      } else {
        return tep_parse_input_field_data($string, $translate);
      }
    }
  }

  function tep_output_string_protected($string) {
    return tep_output_string($string, false, true);
  }

  function tep_sanitize_string($string) {
    $patterns = array ('/ +/','/[<>]/');
    $replace = array (' ', '_');
    return preg_replace($patterns, $replace, trim($string));
  }

  function tep_customers_name($customers_id) {
    $customers = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customers_id . "'");
    $customers_values = tep_db_fetch_array($customers);

    return $customers_values['customers_firstname'] . ' ' . $customers_values['customers_lastname'];
  }
/* eric optimize
  function tep_get_path($current_category_id = '') {
    global $cPath_array;

    if ($current_category_id == '') {
      $cPath_new = implode('_', $cPath_array);
    } else {
      if (sizeof($cPath_array) == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$cPath_array[(sizeof($cPath_array)-1)] . "'");
        $last_category = tep_db_fetch_array($last_category_query);

        $current_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
        $current_category = tep_db_fetch_array($current_category_query);

        if ($last_category['parent_id'] == $current_category['parent_id']) {
          for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }

        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    }

    return 'cPath=' . $cPath_new;
  }
*/
  function tep_get_path($current_category_id = '') {
    global $cPath_array, $countproducts;

    if ($current_category_id == '') {
      $cPath_new = implode('_', $cPath_array);
    } else {
      if (sizeof($cPath_array) == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        if (is_object($countproducts)) {
          $last_category['parent_id'] = '';
          $parentID = $countproducts->getParentCategory((int)$cPath_array[(sizeof($cPath_array)-1)]);
          if ($parentID !== false) {
            $last_category['parent_id'] = $parentID;
          } // end if ($parentID !== false)
        } else {
        $last_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$cPath_array[(sizeof($cPath_array)-1)] . "'");
        $last_category = tep_db_fetch_array($last_category_query);
        }
        
        if (is_object($countproducts)) {
          $current_category['parent_id'] = '';
          $parentID = $countproducts->getParentCategory((int)$current_category_id);
          if ($parentID !== false) {
            $current_category['parent_id'] = $parentID;
          } // end if ($parentID !== false)
        } else {
        $current_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
        $current_category = tep_db_fetch_array($current_category_query);
        }

        if ($last_category['parent_id'] == $current_category['parent_id']) {
          for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }

        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    }

    return 'cPath=' . $cPath_new;
  }
// end eric optimize

  function tep_get_all_get_params($exclude_array = '') {
    global $HTTP_GET_VARS;

    if ($exclude_array == '') $exclude_array = array();

    $get_url = '';

//    reset($HTTP_GET_VARS);
//    while (list($key, $value) = each($HTTP_GET_VARS)) {
    foreach ( $HTTP_GET_VARS as $key => $value ) {	
      if (($key != tep_session_name()) && ($key != 'error') && (!in_array($key, $exclude_array))) $get_url .= $key . '=' . $value . '&';
    }

    return $get_url;
  }

  function tep_date_long($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = (int)substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    return strftime(DATE_FORMAT_LONG, mktime($hour, $minute, $second, $month, $day, $year));
  }

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
  function tep_date_short($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
      return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
    } else {
      return preg_replace('/2037$/', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037)));
    }

  }

  function tep_datetime_short($raw_datetime) {
    if ( ($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == '') ) return false;

    $year = (int)substr($raw_datetime, 0, 4);
    $month = (int)substr($raw_datetime, 5, 2);
    $day = (int)substr($raw_datetime, 8, 2);
    $hour = (int)substr($raw_datetime, 11, 2);
    $minute = (int)substr($raw_datetime, 14, 2);
    $second = (int)substr($raw_datetime, 17, 2);

    return strftime(DATE_TIME_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
  }

// bof optimize get tree funtion
/*
  function tep_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    if ($include_itself) {
      $category_query = tep_db_query("select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . (int)$languages_id . "' and cd.categories_id = '" . (int)$parent_id . "'");
      $category = tep_db_fetch_array($category_query);
      $category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
    }

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($exclude != $categories['categories_id']) $category_tree_array[] = array('id' => $categories['categories_id'], 'text' => $spacing . $categories['categories_name']);
      $category_tree_array = tep_get_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }

    return $category_tree_array;
  }
*/
  
    function tep_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    $category_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id, pcategories.parent_id as grand_parent_id from " . TABLE_CATEGORIES . " c  left join " . TABLE_CATEGORIES . " AS pcategories on pcategories.categories_id = c.parent_id," . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by grand_parent_id, c.parent_id, c.sort_order, cd.categories_name");

    while ($categories = tep_db_fetch_array($category_query)) {
	$_category_tree_array[] = array('grand_parent_id' => $categories['grand_parent_id'], 'parent_id' => $categories['parent_id'], 'id' => $categories['categories_id'], 'text' => ($categories['parent_id'] != '0' ? "&nbsp;&nbsp;&nbsp;".$categories['categories_name']: $categories['categories_name']));  
    } 

    if ($parent_id == '0') {
    foreach ($_category_tree_array as $index_of => $sub_array) {
	    if (!tep_not_null($sub_array['grand_parent_id'])) {
		    $parent_categories[] = array('id' => $sub_array['id'], 'text' =>  $sub_array['text']);		   
       }
    } // end foreach ($_category_tree_array as $index_of => $sub_array)
    } else { // parent_id is set, get a subset (used for deleting a category, with subcats and products)
	 foreach ($_category_tree_array as $index_of => $sub_array) {
	    if ($sub_array['id'] == $parent_id) {
		    $parent_categories[] = array('id' => $sub_array['id'], 'text' =>  $sub_array['text']);		   
       }
    } // end foreach ($_category_tree_array as $index_of => $sub_array)
    }

    for ($x = 0; $x < count($parent_categories); $x++) {
	    array_push($category_tree_array, $parent_categories[$x]);
	    foreach($_category_tree_array as $index_of_cta => $sub_array_cta) {
		    if ($sub_array_cta['parent_id'] == $parent_categories[$x]['id']) {
			    array_push($category_tree_array, array_slice($_category_tree_array[$index_of_cta], 2));
			    // BOF insert subsubcategories and everything below that
			    $categories_counter = $sub_array_cta['id'];
			    // delete the sorted category from the array, less to loop through
			    unset($_category_tree_array[$index_of_cta]);
			    $add_more_subcategories = tep_add_subcategory_to_tree($category_tree_array, $_category_tree_array, $parent_categories, $categories_counter, '2');
			    $category_tree_array = $add_more_subcategories['cat_tree'];
			    $_category_tree_array = $add_more_subcategories['_cat_tree'];
			    // EOF insert subsubcategories and everything below that
		    }
	    }
    }
    return $category_tree_array;
  } 

  function tep_add_subcategory_to_tree($category_tree_array, $_category_tree_array, $parent_categories, $categories_counter, $level) {
	  foreach($_category_tree_array as $index_of_cta => $sub_array_cta) {
		    if ($sub_array_cta['parent_id'] ==  $categories_counter && !in_array($categories_counter, $parent_categories) ) {
			    $subcategory_to_add = array_slice($_category_tree_array[$index_of_cta],2);
			    $spacing = '';
			    for ($i = 1; $i < $level ; $i++) { $spacing .= "&nbsp;&nbsp;&nbsp;"; }
			    $subcategory_to_add['text'] = $spacing.$subcategory_to_add['text'];
			    array_push($category_tree_array, $subcategory_to_add);
			    $cat_counter = $sub_array_cta['id'];
			    // delete the sorted category from the array, less to loop through
			    unset($_category_tree_array[$index_of_cta]);
			    // tep_add_subcategory_to_tree keeps calling itself to add the subcategories
			    $add_more_subcategories = tep_add_subcategory_to_tree($category_tree_array, $_category_tree_array, $parent_categories, $cat_counter, $level+1);
			    $category_tree_array = $add_more_subcategories['cat_tree'];
			    $_category_tree_array = $add_more_subcategories['_cat_tree'];
		    }
	    }
	    $resultant = array('cat_tree' => $category_tree_array, '_cat_tree' => $_category_tree_array);
	    return $resultant;
  } // end function tep_add_subcategory_to_tree

// Eof optimize get tree funtion  

  function tep_draw_products_pull_down($name, $parameters = '', $exclude = '') {
    global $currencies, $languages_id;

    if ($exclude == '') {
      $exclude = array();
    }

    $select_string = '<select name="' . $name . '"';

    if ($parameters) {
      $select_string .= ' ' . $parameters;
    }

    $select_string .= '>';

    $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by products_name");
    while ($products = tep_db_fetch_array($products_query)) {
      if (!in_array($products['products_id'], $exclude)) {
        $select_string .= '<option value="' . $products['products_id'] . '">' . $products['products_name'] . ' (' . $currencies->format($products['products_price']) . ')</option>';
      }
    }

    $select_string .= '</select>';

    return $select_string;
  }

  function tep_format_system_info_array($array) {

    $output = '';
    foreach ($array as $section => $child) {
      $output .= '[' . $section . ']' . "\n";
      foreach ($child as $variable => $value) {
        if (is_array($value)) {
          $output .= $variable . ' = ' . implode(',', $value) ."\n";
        } else {
          $output .= $variable . ' = ' . $value . "\n";
        }
      }

    $output .= "\n";
    }
    return $output;

  }



  function tep_options_name($options_id) {
    global $languages_id;

    $options = tep_db_query("select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$options_id . "' and language_id = '" . (int)$languages_id . "'");
    $options_values = tep_db_fetch_array($options);

    return $options_values['products_options_name'];
  }

  function tep_values_name($values_id) {
    global $languages_id;

    $values = tep_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$values_id . "' and language_id = '" . (int)$languages_id . "'");
    $values_values = tep_db_fetch_array($values);

    return $values_values['products_options_values_name'];
  }

  function tep_info_image($image, $alt, $width = '', $height = '') {
// bof multi stores  $default_store_images_directory  
    GLOBAL $default_store_images_directory ;
//    if (tep_not_null($image) && (file_exists(DIR_FS_CATALOG_IMAGES . $image)) ) {
    if (tep_not_null($image) && (file_exists($default_store_images_directory   . $image)) ) {
// eof multi stores	
      $image = tep_image(DIR_WS_CATALOG_IMAGES . $image, $alt, $width, $height);
    } else {
      $image = TEXT_IMAGE_NONEXISTENT . $image;
    }

    return $image;
  }

  function tep_break_string($string, $len, $break_char = '-') {
    $l = 0;
    $output = '';
    for ($i=0, $n=strlen($string); $i<$n; $i++) {
      $char = substr($string, $i, 1);
      if ($char != ' ') {
        $l++;
      } else {
        $l = 0;
      }
      if ($l > $len) {
        $l = 1;
        $output .= $break_char;
      }
      $output .= $char;
    }

    return $output;
  }

  function tep_get_country_name($country_id) {
    $country_query = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");

    if (!tep_db_num_rows($country_query)) {
      return $country_id;
    } else {
      $country = tep_db_fetch_array($country_query);
      return $country['countries_name'];
    }
  }

// multi stores
  function tep_get_stores_name($stores_id) {
    $stores_query = tep_db_query("select stores_name from " . TABLE_STORES . " where stores_id = '" . (int)$stores_id . "'");

    if (!tep_db_num_rows($stores_query)) {
      return $stores_id;
    } else {
      $store_name = tep_db_fetch_array($stores_query);
      return $store_name['stores_name'];
    }
  }
  
  function tep_get_zone_name($country_id, $zone_id, $default_zone) {
    $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' and zone_id = '" . (int)$zone_id . "'");
    if (tep_db_num_rows($zone_query)) {
      $zone = tep_db_fetch_array($zone_query);
      return $zone['zone_name'];
    } else {
      return $default_zone;
    }
  }

  function tep_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if ( (is_string($value) || is_int($value)) && ($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }

  function tep_browser_detect($component) {
    global $HTTP_USER_AGENT;

    return stristr($HTTP_USER_AGENT, $component);
  }

  function tep_tax_classes_pull_down($parameters, $selected = '') {
    $select_string = '<select ' . $parameters . '>';
    $classes_query = tep_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($classes = tep_db_fetch_array($classes_query)) {
      $select_string .= '<option value="' . $classes['tax_class_id'] . '"';
      if ($selected == $classes['tax_class_id']) $select_string .= ' SELECTED';
      $select_string .= '>' . $classes['tax_class_title'] . '</option>';
    }
    $select_string .= '</select>';

    return $select_string;
  }

  function tep_geo_zones_pull_down($parameters, $selected = '') {
    $select_string = '<select ' . $parameters . '>';
    $zones_query = tep_db_query("select geo_zone_id, geo_zone_name from " . TABLE_GEO_ZONES . " order by geo_zone_name");
    while ($zones = tep_db_fetch_array($zones_query)) {
      $select_string .= '<option value="' . $zones['geo_zone_id'] . '"';
      if ($selected == $zones['geo_zone_id']) $select_string .= ' SELECTED';
      $select_string .= '>' . $zones['geo_zone_name'] . '</option>';
    }
    $select_string .= '</select>';

    return $select_string;
  }

  function tep_get_geo_zone_name($geo_zone_id) {
    $zones_query = tep_db_query("select geo_zone_name from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . (int)$geo_zone_id . "'");

    if (!tep_db_num_rows($zones_query)) {
      $geo_zone_name = $geo_zone_id;
    } else {
      $zones = tep_db_fetch_array($zones_query);
      $geo_zone_name = $zones['geo_zone_name'];
    }

    return $geo_zone_name;
  }

  function tep_address_format($address_format_id, $address, $html, $boln, $eoln) {
    $address_format_query = tep_db_query("select address_format as format from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . (int)$address_format_id . "'");
    $address_format = tep_db_fetch_array($address_format_query);

    $company = tep_output_string_protected($address['company']);
    if (isset($address['firstname']) && tep_not_null($address['firstname'])) {
      $firstname = tep_output_string_protected($address['firstname']);
      $lastname = tep_output_string_protected($address['lastname']);
    } elseif (isset($address['name']) && tep_not_null($address['name'])) {
      $firstname = tep_output_string_protected($address['name']);
      $lastname = '';
    } else {
      $firstname = '';
      $lastname = '';
    }
    $street = tep_output_string_protected($address['street_address']);
    $suburb = tep_output_string_protected($address['suburb']);
    $city = tep_output_string_protected($address['city']);
    $state = tep_output_string_protected($address['state']);
    if (isset($address['country_id']) && tep_not_null($address['country_id'])) {
      $country = tep_get_country_name($address['country_id']);

      if (isset($address['zone_id']) && tep_not_null($address['zone_id'])) {
        $state = tep_get_zone_code($address['country_id'], $address['zone_id'], $state);
      }
    } elseif (isset($address['country']) && tep_not_null($address['country'])) {
      $country = tep_output_string_protected($address['country']);
    } else {
      $country = '';
    }
    $postcode = tep_output_string_protected($address['postcode']);
    $zip = $postcode;

    if ($html) {
// HTML Mode
      $HR = '<hr />';
      $hr = '<hr />';
      if ( ($boln == '') && ($eoln == "\n") ) { // Values not specified, use rational defaults
        $CR = '<br />';
        $cr = '<br />';
        $eoln = $cr;
      } else { // Use values supplied
        $CR = $eoln . $boln;
        $cr = $CR;
      }
    } else {
// Text Mode
      $CR = $eoln;
      $cr = $CR;
      $HR = '----------------------------------------';
      $hr = '----------------------------------------';
    }

    $statecomma = '';
    $streets = $street;
    if ($suburb != '') $streets = $street . $cr . $suburb;
    if ($country == '') $country = tep_output_string_protected($address['country']);
    if ($state != '') $statecomma = $state . ', ';

    $fmt = $address_format['format'];
    eval("\$address = \"$fmt\";");

    if ( (ACCOUNT_COMPANY == 'true') && (tep_not_null($company)) ) {
      $address = $company . $cr . $address;
    }

    return $address;
  }

  ////////////////////////////////////////////////////////////////////////////////////////////////
  //
  // Function    : tep_get_zone_code
  //
  // Arguments   : country           country code string
  //               zone              state/province zone_id
  //               def_state         default string if zone==0
  //
  // Return      : state_prov_code   state/province code
  //
  // Description : Function to retrieve the state/province code (as in FL for Florida etc)
  //
  ////////////////////////////////////////////////////////////////////////////////////////////////
  function tep_get_zone_code($country, $zone, $def_state) {

    $state_prov_query = tep_db_query("select zone_code from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and zone_id = '" . (int)$zone . "'");

    if (!tep_db_num_rows($state_prov_query)) {
      $state_prov_code = $def_state;
    }
    else {
      $state_prov_values = tep_db_fetch_array($state_prov_query);
      $state_prov_code = $state_prov_values['zone_code'];
    }
    
    return $state_prov_code;
  }

  function tep_get_uprid($prid, $params) {
    $uprid = $prid;
    if ( (is_array($params)) && (!strstr($prid, '{')) ) {
//      while (list($option, $value) = each($params)) {
      foreach ( $params as $option => $value ) {  // vertigo
        $uprid = $uprid . '{' . $option . '}' . $value;
      }
    }

    return $uprid;
  }

  function tep_get_prid($uprid) {
    $pieces = explode('{', $uprid);

    return $pieces[0];
  }

  function tep_get_languages() {
    $languages_query = tep_db_query("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " order by sort_order");
    while ($languages = tep_db_fetch_array($languages_query)) {
      $languages_array[] = array('id' => $languages['languages_id'],
                                 'name' => $languages['name'],
                                 'code' => $languages['code'],
                                 'image' => $languages['image'],
                                 'directory' => $languages['directory']);
    }

    return $languages_array;
  }
  
  function tep_get_multi_stores() {
      $stores_query = tep_db_query("select stores_id, stores_name, stores_image, stores_config_table, stores_status from " . TABLE_STORES . " order by stores_id");
      while ($stores = tep_db_fetch_array($stores_query)) {
        $available_stores_array[] = array('id' =>            $stores['stores_id'],
                                          'name' =>          $stores['stores_name'],
                                          'image' =>         $stores['stores_image'],
                                          'configuration' => $stores['stores_config_table'],
										  'status' =>        $stores['stores_status'] );
      }  
    return $available_stores_array;	 
  }

  function tep_get_category_name($category_id, $language_id) {
    $category_query = tep_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_name'];
  }

  function tep_get_orders_status_name($orders_status_id, $language_id = '') {
    global $languages_id;

    if (!$language_id) $language_id = $languages_id;
    $orders_status_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . (int)$orders_status_id . "' and language_id = '" . (int)$language_id . "'");
    $orders_status = tep_db_fetch_array($orders_status_query);

    return $orders_status['orders_status_name'];
  }

  function tep_get_orders_status() {
    global $languages_id;

    $orders_status_array = array();
    $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$languages_id . "' order by orders_status_id");
    while ($orders_status = tep_db_fetch_array($orders_status_query)) {
      $orders_status_array[] = array('id' => $orders_status['orders_status_id'],
                                     'text' => $orders_status['orders_status_name']);
    }

    return $orders_status_array;
  }

  function tep_get_products_name($product_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $product_query = tep_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_name'];
  }

  function tep_get_products_description($product_id, $language_id) {
    $product_query = tep_db_query("select products_description from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_description'];
  }

  function tep_get_products_url($product_id, $language_id) {
    $product_query = tep_db_query("select products_url from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_url'];
  }

////
// Return the manufacturers URL in the needed language
// TABLES: manufacturers_info
  function tep_get_manufacturer_url($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("select manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
    $manufacturer = tep_db_fetch_array($manufacturer_query);

    return $manufacturer['manufacturers_url'];
  }
  
  function tep_get_locationmap_url($locationmap_id, $language_id) {
    $locationmap_query = tep_db_query("select locationmap_url from " . TABLE_LOCATIONMAP_INFO . " where locationmap_id = '" . (int)$locationmap_id . "' and languages_id = '" . (int)$language_id . "'");
    $locationmap = tep_db_fetch_array($locationmap_query);

    return $locationmap['locationmap_url'];
  }
  

////
// Wrapper for class_exists() function
// This function is not available in all PHP versions so we test it before using it.
  function tep_class_exists($class_name) {
    if (function_exists('class_exists')) {
      return class_exists($class_name);
    } else {
      return true;
    }
  }

////
// Count how many products exist in a category
// TABLES: products, products_to_categories, categories
/* eric optimize
  function tep_products_in_category_count($categories_id, $include_deactivated = false) {
    $products_count = 0;

    if ($include_deactivated) {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$categories_id . "'");
    } else {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . (int)$categories_id . "'");
    }

    $products = tep_db_fetch_array($products_query);

    $products_count += $products['total'];

    $childs_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$categories_id . "'");
    if (tep_db_num_rows($childs_query)) {
      while ($childs = tep_db_fetch_array($childs_query)) {
        $products_count += tep_products_in_category_count($childs['categories_id'], $include_deactivated);
      }
    }

    return $products_count;
  }
*/
 function tep_products_in_category_count($category_id, $include_inactive = false) {
    global $countproducts;
    $products_count = 0;
    if (is_object($countproducts)) {
      $products_count += $countproducts->CountProductsInCategory($category_id);
    } else {
    if ($include_inactive == true) {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$category_id . "'");
    } else {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . (int)$category_id . "'");
    }
    $products = tep_db_fetch_array($products_query);
    $products_count += $products['total'];
    } // end if/else (is_object($countproducts)

    if (is_object($countproducts)) {
      $child_categories = $countproducts->hasChildCategories($category_id);
      if ($child_categories !== false) {
         foreach ($child_categories as $key => $child_categories_id) {
           $products_count += tep_products_in_category_count($child_categories_id, $include_inactive);
         }
      }
    } else {
    $child_categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");
    if (tep_db_num_rows($child_categories_query)) {
      while ($child_categories = tep_db_fetch_array($child_categories_query)) {
        $products_count += tep_products_in_category_count($child_categories['categories_id'], $include_inactive);
      }
    }
   } // end if/else (is_object($countproducts))
   
    return $products_count;
  }

// end eric optimize
  
////
// Count how many subcategories exist in a category
// TABLES: categories
/* eric optimize
  function tep_childs_in_category_count($categories_id) {
    $categories_count = 0;

    $categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$categories_id . "'");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_count++;
      $categories_count += tep_childs_in_category_count($categories['categories_id']);
    }

    return $categories_count;
  }
*/
  function tep_childs_in_category_count($categories_id) {
    $categories_count = 0;
    global $countproducts;
    if (is_object($countproducts)) {
      $child_categories = $countproducts->hasChildCategories($categories_id);
      if ($child_categories !== false) {
        foreach ($child_categories as $key => $categories_id) {
          $categories_count++;
          $categories_count += tep_childs_in_category_count($categories_id);
        }
         return $categories_count;
      } else {
        return $categories_count;
      }
    } else {
    $categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$categories_id . "'");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_count++;
      $categories_count += tep_childs_in_category_count($categories['categories_id']);
    }
     return $categories_count;   
    }
  }

// end eric optimize
////
// Returns an array with countries
// TABLES: countries
  function tep_get_countries($default = '') {
    $countries_array = array();
    if ($default) {
      $countries_array[] = array('id' => '',
                                 'text' => $default);
    }
    $countries_query = tep_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . " order by countries_name");
    while ($countries = tep_db_fetch_array($countries_query)) {
      $countries_array[] = array('id' => $countries['countries_id'],
                                 'text' => $countries['countries_name']);
    }

    return $countries_array;
  }

////
// return an array with country zones
  function tep_get_country_zones($country_id) {
    $zones_array = array();
    $zones_query = tep_db_query("select zone_id, zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' order by zone_name");
    while ($zones = tep_db_fetch_array($zones_query)) {
      $zones_array[] = array('id' => $zones['zone_id'],
                             'text' => $zones['zone_name']);
    }

    return $zones_array;
  }

  function tep_prepare_country_zones_pull_down($country_id = '') {
// preset the width of the drop-down for Netscape
    $pre = '';
    if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
      for ($i=0; $i<45; $i++) $pre .= '&nbsp;';
    }

    $zones = tep_get_country_zones($country_id);

    if (sizeof($zones) > 0) {
      $zones_select = array(array('id' => '', 'text' => PLEASE_SELECT));
      $zones = array_merge((array)$zones_select, (array)$zones);
    } else {
      $zones = array(array('id' => '', 'text' => TYPE_BELOW));
// create dummy options for Netscape to preset the height of the drop-down
      if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
        for ($i=0; $i<9; $i++) {
          $zones[] = array('id' => '', 'text' => $pre);
        }
      }
    }

    return $zones;
  }

////
// Get list of address_format_id's
  function tep_get_address_formats() {
    $address_format_query = tep_db_query("select address_format_id from " . TABLE_ADDRESS_FORMAT . " order by address_format_id");
    $address_format_array = array();
    while ($address_format_values = tep_db_fetch_array($address_format_query)) {
      $address_format_array[] = array('id' => $address_format_values['address_format_id'],
                                      'text' => $address_format_values['address_format_id']);
    }
    return $address_format_array;
  }

////
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_pull_down_country_list($country_id) {
    return tep_draw_pull_down_menu('configuration_value', tep_get_countries(), $country_id);
  }

  function tep_cfg_pull_down_zone_list($zone_id) {
    return tep_draw_pull_down_menu('configuration_value', tep_get_country_zones(STORE_COUNTRY), $zone_id);
  }

  function tep_cfg_pull_down_tax_classes($tax_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $tax_class_query = tep_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($tax_class = tep_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = array('id' => $tax_class['tax_class_id'],
                                 'text' => $tax_class['tax_class_title']);
    }

    return tep_draw_pull_down_menu($name, $tax_class_array, $tax_class_id);
  }

////
// Function to read in text area in admin
 function tep_cfg_textarea($text) {
    return tep_draw_textarea_field('configuration_value', false, 35, 5, $text);
  }

  function tep_cfg_get_zone_name($zone_id) {
    $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_id = '" . (int)$zone_id . "'");

    if (!tep_db_num_rows($zone_query)) {
      return $zone_id;
    } else {
      $zone = tep_db_fetch_array($zone_query);
      return $zone['zone_name'];
    }
  }

////
// Sets the status of a banner
  function tep_set_banner_status($banners_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_BANNERS . " set status = '1', expires_impressions = NULL, expires_date = NULL, date_status_change = NULL where banners_id = '" . $banners_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_BANNERS . " set status = '0', date_status_change = now() where banners_id = '" . $banners_id . "'");
    } else {
      return -1;
    }
  }

////
// Sets the status of a product
  function tep_set_product_status($products_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '1', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '0', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
    } else {
      return -1;
    }
  }
// START Added for the purchase feature option
// Sets the product Purchase Yes/No
  function tep_set_product_purchase($products_id, $status1) {
    if ($status1 == '1') {
      return tep_db_query("update " . TABLE_PRODUCTS . " set products_purchase = '1', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
    } elseif ($status1 == '0') {
      return tep_db_query("update " . TABLE_PRODUCTS . " set products_purchase = '0', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
    } else {
      return -1;
    }
  }
// END Added for the purchase feature option
////
// Sets the status of a review
  function tep_set_review_status($reviews_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_REVIEWS . " set reviews_status = '1', last_modified = now() where reviews_id = '" . (int)$reviews_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_REVIEWS . " set reviews_status = '0', last_modified = now() where reviews_id = '" . (int)$reviews_id . "'");
    } else {
      return -1;
    }
  }

////
// Sets the status of a product on special
  function tep_set_specials_status($specials_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_SPECIALS . " set status = '1', expires_date = NULL, date_status_change = NULL where specials_id = '" . (int)$specials_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_SPECIALS . " set status = '0', date_status_change = now() where specials_id = '" . (int)$specials_id . "'");
    } else {
      return -1;
    }
  }

////
// Sets timeout for the current script.
// Cant be used in safe mode.
  function tep_set_time_limit($limit) {
    if (!get_cfg_var('safe_mode')) {
      set_time_limit($limit);
    }
  }

/*
////
// Alias function for Store configuration values in the Administration Tool
//redone to allow some bootstrapping features
  function tep_cfg_select_option($select_array, $key_value, $key = '') {
    
    for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {
      $name = ((tep_not_null($key)) ? 'configuration[' . $key . ']' : 'configuration_value');

      $radio .= '<input type="radio" id="' . $name . $i .'" name="' . $name . '" value="' . $select_array[$i] . '"';

      if ($key_value == $select_array[$i]) $string .= ' checked="checked"';

      $radio .= ' /><label for="' . $name . $i . '"  onClick="">' . $select_array[$i] . '</label>';
    }
	$string = '';
	$string .= '<div class="switch-toggle switch-android col-md-'. $n++ .'">';
	$string .= $radio;
	$string .= '<a></a>';
    $string .= '</div>';
    return $string;
  }
 */
////
// Alias function for Store configuration values in the Administration Tool
// add classes and labels for bootstrap, plus an id that incriments by a numeric value
  function tep_cfg_select_option($select_array, $key_value, $key = '') {
    $string = '';

    for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {
      $name = ((tep_not_null($key)) ? 'configuration[' . $key . ']' : 'configuration_value');
	           
//	  $string .= '   <div class="form-group">' . PHP_EOL ;
      $string .= '       <div class="radio radio-success">'. PHP_EOL  ;
      //$string .= '          <label>'. PHP_EOL  ;
	  $string .= '              <input type="radio" id="' . $name . $i .'" name="' . $name . '" value="' . $select_array[$i] . '"';

      if ($key_value == $select_array[$i]) $string .= ' checked="checked"';

      $string .= '                                                          /> '  ;
      $string .= '             <label for="' . $name . $i .'" >'. PHP_EOL  ;
      $string .=                   $select_array[$i] . PHP_EOL  ;	  
	  $string .= '             </label>'. PHP_EOL ;
      $string .= '       </div>'. PHP_EOL  ;	
//      $string .= '   </div>'. PHP_EOL  ;		  
    }

    return $string;
  }
////
// Alias function for module configuration keys
  function tep_mod_select_option($select_array, $key_name, $key_value) {
    reset($select_array);
    while (list($key, $value) = each($select_array)) {
      if (is_int($key)) $key = $value;
      $string .= '<br /><input type="radio" name="configuration[' . $key_name . ']" value="' . $key . '"';
      if ($key_value == $key) $string .= ' checked="checked"';
      $string .= ' /> ' . $value;
    }

    return $string;
  }

////
// Retreive server information
  function tep_get_system_information() {
// 8703 mysqli  
//    global $HTTP_SERVER_VARS;
    global $db_link, $HTTP_SERVER_VARS;
// end 8703	

    $db_query = tep_db_query("select now() as datetime");
    $db = tep_db_fetch_array($db_query);

    @list($system, $host, $kernel) = preg_split('/[\s,]+/', @exec('uname -a'), 5);

    $data = array();

    $data['oscommerce']  = array('version' => tep_get_version());

    $data['system'] = array('date' => date('Y-m-d H:i:s O T'),
                            'os' => PHP_OS,
                            'kernel' => $kernel,
                            'uptime' => @exec('uptime'),
                            'http_server' => $HTTP_SERVER_VARS['SERVER_SOFTWARE']);


    $data['mysql']  =     $data['mysql']  = array('version' => tep_db_get_server_info(), // 2.3.3.2
//	array('version' => (function_exists('mysql_get_server_info') ? mysql_get_server_info() : ''),
                  'date' => $db['datetime']);

    $data['php']    = array('version' => PHP_VERSION,
                            'zend' => zend_version(),
                            'sapi' => PHP_SAPI,
                            'int_size'	=> defined('PHP_INT_SIZE') ? PHP_INT_SIZE : '',
                            'safe_mode'	=> (int) @ini_get('safe_mode'),
                            'open_basedir' => (int) @ini_get('open_basedir'),
                            'memory_limit' => @ini_get('memory_limit'),
                            'error_reporting' => error_reporting(),
                            'display_errors' => (int)@ini_get('display_errors'),
                            'allow_url_fopen' => (int) @ini_get('allow_url_fopen'),
                            'allow_url_include' => (int) @ini_get('allow_url_include'),
                            'file_uploads' => (int) @ini_get('file_uploads'),
                            'upload_max_filesize' => @ini_get('upload_max_filesize'),
                            'post_max_size' => @ini_get('post_max_size'),
                            'disable_functions' => @ini_get('disable_functions'),
                            'disable_classes' => @ini_get('disable_classes'),
                            'enable_dl'	=> (int) @ini_get('enable_dl'),
                            'magic_quotes_gpc' => (int) @ini_get('magic_quotes_gpc'),
                            'register_globals' => (int) @ini_get('register_globals'),
                            'filter.default'   => @ini_get('filter.default'),
                            'zend.ze1_compatibility_mode' => (int) @ini_get('zend.ze1_compatibility_mode'),
                            'unicode.semantics' => (int) @ini_get('unicode.semantics'),
                            'zend_thread_safty'	=> (int) function_exists('zend_thread_id'),
                            'extensions' => get_loaded_extensions());

    return $data;
  }

  function tep_generate_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
    global $languages_id;

    if (!is_array($categories_array)) $categories_array = array();

    if ($from == 'product') {
      $categories_query = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$id . "'");
      while ($categories = tep_db_fetch_array($categories_query)) {
        if ($categories['categories_id'] == '0') {
          $categories_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
        } else {
          $category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$categories['categories_id'] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
          $category = tep_db_fetch_array($category_query);
          $categories_array[$index][] = array('id' => $categories['categories_id'], 'text' => $category['categories_name']);
          if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = tep_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
          $categories_array[$index] = array_reverse($categories_array[$index]);
        }
        $index++;
      }
    } elseif ($from == 'category') {
      $category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
      $category = tep_db_fetch_array($category_query);
      $categories_array[$index][] = array('id' => $id, 'text' => $category['categories_name']);
      if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = tep_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
    }

    return $categories_array;
  }

  function tep_output_generated_category_path($id, $from = 'category') {
    $calculated_category_path_string = '';
    $calculated_category_path = tep_generate_category_path($id, $from);
    for ($i=0, $n=sizeof($calculated_category_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_category_path[$i]); $j<$k; $j++) {
        $calculated_category_path_string .= $calculated_category_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -16) . '<br />';
    }
    $calculated_category_path_string = substr($calculated_category_path_string, 0, -6);

    if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

    return $calculated_category_path_string;
  }

  function tep_get_generated_category_path_ids($id, $from = 'category') {
    $calculated_category_path_string = '';
    $calculated_category_path = tep_generate_category_path($id, $from);
    for ($i=0, $n=sizeof($calculated_category_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_category_path[$i]); $j<$k; $j++) {
        $calculated_category_path_string .= $calculated_category_path[$i][$j]['id'] . '_';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -1) . '<br />';
    }
    $calculated_category_path_string = substr($calculated_category_path_string, 0, -6);

    if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

    return $calculated_category_path_string;
  }

  function tep_remove_category($category_id) {
    GLOBAL $default_store_images_directory ; // MULTI STORES  
    $category_image_query = tep_db_query("select categories_image from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$category_id . "'");
    $category_image = tep_db_fetch_array($category_image_query);

    $duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where categories_image = '" . tep_db_input($category_image['categories_image']) . "'");
    $duplicate_image = tep_db_fetch_array($duplicate_image_query);

    if ($duplicate_image['total'] < 2) {
// bof multi stores		
//      if (file_exists(DIR_FS_CATALOG_IMAGES . $category_image['categories_image'])) {
//        @unlink(DIR_FS_CATALOG_IMAGES . $category_image['categories_image']);
      if (file_exists($default_store_images_directory . $category_image['categories_image'])) {
        @unlink($default_store_images_directory . $category_image['categories_image']);		
		tep_multi_stores_images( $category_image['categories_image'], '', $default_store_images_directory, '', '' )    ; 
// eof multi stores		
      }
    }

    tep_db_query("delete from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$category_id . "'");
    tep_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . (int)$category_id . "'");

    if (USE_CACHE == 'true') {
      tep_reset_cache_block('categories');
      tep_reset_cache_block('also_purchased');	  
      tep_reset_cache_block('xsell_products'); // XSell
    }
//++++ QT Pro: Begin Changed code
	if ($products_id!=null) qtpro_doctor_amputate_all_from_product($product_id);
//++++ QT Pro: End Changed code		
  }

  function tep_remove_product($product_id) {
    GLOBAL $default_store_images_directory ; // MULTI STORES
    $product_image_query = tep_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    $product_image = tep_db_fetch_array($product_image_query);

    $duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where products_image = '" . tep_db_input($product_image['products_image']) . "'");
    $duplicate_image = tep_db_fetch_array($duplicate_image_query);

    if ($duplicate_image['total'] < 2) {
// bof multi stores		
//      if (file_exists(DIR_FS_CATALOG_IMAGES . $product_image['products_image'])) {
//        @unlink(DIR_FS_CATALOG_IMAGES . $product_image['products_image']);		
      if (file_exists($default_store_images_directory . $product_image['products_image'])) {
        @unlink($default_store_images_directory . $product_image['products_image']);
        tep_multi_stores_images( $product_image['products_image'], '', $default_store_images_directory, '', '' )    ;  		
// bof multi stores				
       }
    }

    $product_images_query = tep_db_query("select image from " . TABLE_PRODUCTS_IMAGES . " where products_id = '" . (int)$product_id . "'");
    if (tep_db_num_rows($product_images_query)) {
      while ($product_images = tep_db_fetch_array($product_images_query)) {
        $duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_IMAGES . " where image = '" . tep_db_input($product_images['image']) . "'");
        $duplicate_image = tep_db_fetch_array($duplicate_image_query);

        if ($duplicate_image['total'] < 2) {
// bof multi stores		
//          if (file_exists(DIR_FS_CATALOG_IMAGES . $product_images['image'])) {
//            @unlink(DIR_FS_CATALOG_IMAGES . $product_images['image']); 
          if (file_exists($default_store_images_directory . $product_images['image'])) {
            @unlink($default_store_images_directory . $product_images['image']); 
			tep_multi_stores_images( $product_images['image'], '', $default_store_images_directory, '', '' )    ;  
// eof multi stores			
          }
        }
      }

      tep_db_query("delete from " . TABLE_PRODUCTS_IMAGES . " where products_id = '" . (int)$product_id . "'");
    }

    tep_db_query("delete from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where products_id = '" . (int)$product_id . "' or products_id like '" . (int)$product_id . "{%'");
    tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where products_id = '" . (int)$product_id . "' or products_id like '" . (int)$product_id . "{%'");
// bof wishlist
// Wishlist addition to delete products from the wishlist when product deleted
    tep_db_query("delete from " . TABLE_WISHLIST . " where products_id = '" . (int)$product_id . "' or products_id like '" . (int)$product_id . "{%'");
    tep_db_query("delete from " . TABLE_WISHLIST_ATTRIBUTES . " where products_id = '" . (int)$product_id . "' or products_id like '" . (int)$product_id . "{%'");
// eof wislist	
//BOF qpbpp
    tep_db_query("delete from " . TABLE_PRODUCTS_PRICE_BREAK . " where products_id = '" . (int)$product_id . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_TO_DISCOUNT_CATEGORIES . " where products_id = '" . (int)$product_id . "'");
//EOF qpbpp	

    $product_reviews_query = tep_db_query("select reviews_id from " . TABLE_REVIEWS . " where products_id = '" . (int)$product_id . "'");
    while ($product_reviews = tep_db_fetch_array($product_reviews_query)) {
      tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . (int)$product_reviews['reviews_id'] . "'");
    }
    tep_db_query("delete from " . TABLE_REVIEWS . " where products_id = '" . (int)$product_id . "'");

    if (USE_CACHE == 'true') {
      tep_reset_cache_block('categories');
      tep_reset_cache_block('also_purchased');
	  tep_reset_cache_block('xsell_products'); // XSell
    }
//++++ QT Pro: Begin Changed code
	if ($products_id!=null) qtpro_doctor_amputate_all_from_product($product_id);
//++++ QT Pro: End Changed code	
  }

  function tep_remove_order($order_id, $restock = false) {
    if ($restock == 'on') {
// begin qtpro461	
//      $order_query = tep_db_query("select products_id, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
//      while ($order = tep_db_fetch_array($order_query)) {
//        tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = products_quantity + " . $order['products_quantity'] . ", products_ordered = products_ordered - " . $order['products_quantity'] . " where products_id = '" . (int)$order['products_id'] . "'");
//      }
 //++++ QT Pro: Begin Changed code
      $order_query = tep_db_query("select products_id, products_quantity, products_stock_attributes from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
      while ($order = tep_db_fetch_array($order_query)) {
        $product_stock_adjust = 0;
        if (tep_not_null($order['products_stock_attributes'])) {
          if ($order['products_stock_attributes'] != '$$DOWNLOAD$$') {
            $attributes_stock_query = tep_db_query("SELECT products_stock_quantity 
                                                    FROM " . TABLE_PRODUCTS_STOCK . " 
                                                    WHERE products_stock_attributes = '" . $order['products_stock_attributes'] . "' 
                                                    AND products_id = '" . (int)$order['products_id'] . "'");
            if (tep_db_num_rows($attributes_stock_query) > 0) {
                $attributes_stock_values = tep_db_fetch_array($attributes_stock_query);
                tep_db_query("UPDATE " . TABLE_PRODUCTS_STOCK . " 
                              SET products_stock_quantity = products_stock_quantity + '" . (int)$order['products_quantity'] . "' 
                              WHERE products_stock_attributes = '" . $order['products_stock_attributes'] . "' 
                              AND products_id = '" . (int)$order['products_id'] . "'");
                $product_stock_adjust = min($order['products_quantity'],  $order['products_quantity']+$attributes_stock_values['products_stock_quantity']);
            } else {
                tep_db_query("INSERT into " . TABLE_PRODUCTS_STOCK . " 
                              (products_id, products_stock_attributes, products_stock_quantity)
                              VALUES ('" . (int)$order['products_id'] . "', '" . $order['products_stock_attributes'] . "', '" . (int)$order['products_quantity'] . "')");
                $product_stock_adjust = $order['products_quantity'];
            }
          }
        } else {
            $product_stock_adjust = $order['products_quantity'];
        } 
        tep_db_query("UPDATE " . TABLE_PRODUCTS . " 
                      SET products_quantity = products_quantity + " . $product_stock_adjust . ", products_ordered = products_ordered - " . (int)$order['products_quantity'] . " 
                      WHERE products_id = '" . (int)$order['products_id'] . "'");

      }
//++++ QT Pro: End Changed Code
    
    }  // end restock on

    tep_db_query("delete from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . (int)$order_id . "'");
    tep_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$order_id . "'");
  } // end function remove order

// bof xsell  
//  function tep_reset_cache_block($cache_block) {
//    global $cache_blocks;
//
//    for ($i=0, $n=sizeof($cache_blocks); $i<$n; $i++) {
//      if ($cache_blocks[$i]['code'] == $cache_block) {
//        if ($cache_blocks[$i]['multiple']) {
//          if ($dir = @opendir(DIR_FS_CACHE)) {
//            while ($cache_file = readdir($dir)) {
//              $cached_file = $cache_blocks[$i]['file'];
//              $languages = tep_get_languages();
//              for ($j=0, $k=sizeof($languages); $j<$k; $j++) {
//                $cached_file_unlink = preg_replace('/-language/', '-' . $languages[$j]['directory'], $cached_file);
//                if (preg_match('/^' . $cached_file_unlink . '/', $cache_file)) {
//                  @unlink(DIR_FS_CACHE . $cache_file);
//                }
 //             }
//            }
//            closedir($dir);
//          }
//        } else {
//          $cached_file = $cache_blocks[$i]['file'];
//          $languages = tep_get_languages();
//          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
//            $cached_file = preg_replace('/-language/', '-' . $languages[$i]['directory'], $cached_file);
//            @unlink(DIR_FS_CACHE . $cached_file);
//          }
//        }
//        break;
//      }
//    }
//  }
// bof hide products for sppc
//function tep_reset_cache_block($cache_block) {
//  global $cache_blocks;
//  
//  $pid = '*';
//  if ($cache_block == 'xsell_products') {
//  	$pid = '';
//    if (isset($_GET['add_related_product_ID']) ) {
//    	$pid =  $_GET['add_related_product_ID'];
//    }
//    if ( !$pid ) $pid = '*';
//  }  

//  for ($i=0, $n=sizeof($cache_blocks); $i<$n; $i++) {
//    if ($cache_blocks[$i]['code'] == $cache_block) {
//      $glob_pattern = preg_replace('#-language.+$#', '-*', $cache_blocks[$i]['file']);
//      foreach ( glob(DIR_FS_CACHE . $glob_pattern . '.cache' . $pid) as $cache_file ) {
//         @unlink($cache_file);
//      }
//      break;
//    }
//  }
//}
   function tep_reset_cache_block($cache_block) {
    global $cache_blocks;

    for ($i=0, $n=sizeof($cache_blocks); $i<$n; $i++) {
      if ($cache_blocks[$i]['code'] == $cache_block) {
        if ($cache_blocks[$i]['multiple']) {
          if ($dir = @opendir(DIR_FS_CACHE)) {
            while ($cache_file = readdir($dir)) {
              $cached_file = $cache_blocks[$i]['file'];
              $languages = tep_get_languages();
              for ($j=0, $k=sizeof($languages); $j<$k; $j++) {
                $cached_file_unlink = preg_replace('/-language/', '-' . $languages[$j]['directory'], $cached_file);
                // if the file name starts with one of those we are looking for and is a cache file (by
                // checking if it contains the string ".cache" we delete the cache file
                if (preg_match('/^' . $cached_file_unlink .'/', $cache_file)) {
                  @unlink(DIR_FS_CACHE . $cache_file);
                }
              }
            }
            closedir($dir);
          }
        } else {
          // not used using hide products or regular osC, but if so, it assumes the $cache_blocks[$i]['file'] does
          // contain the .cache on the end for example whatever_box-language.cache
          $cached_file = $cache_blocks[$i]['file'];
          $languages = tep_get_languages();
          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
            $cached_file = preg_replace('/-language/', '-' . $languages[$i]['directory'], $cached_file);
            @unlink(DIR_FS_CACHE . $cached_file);
          }
        }
        break;
      }
    }
  }


   function tep_reset_cache_block2($cache_block) {
    global $cache_blocks;
    
    for ($i=0, $n=sizeof($cache_blocks); $i<$n; $i++) {
      if ($cache_blocks[$i]['code'] == $cache_block) {
        if ($cache_blocks[$i]['multiple']) {
          if ($dir = @opendir(DIR_FS_CACHE)) {
            while ($cache_file = readdir($dir)) {
              $cached_file = $cache_blocks[$i]['file'];
              $languages = tep_get_languages();
              for ($j=0, $k=sizeof($languages); $j<$k; $j++) {
//                $cached_file_unlink = ereg_replace('-language', '-' . $languages[$j]['directory'], $cached_file); // 5.3
                $cached_file_unlink = preg_replace('/ -language/', '-' . $languages[$j]['directory'], $cached_file);				
                // if the file name starts with one of those we are looking for and is a cache file (by
                // checking if it contains the string ".cache" we delete the cache file
//                if (ereg('^' . $cached_file_unlink, $cache_file) && strstr($cache_file, '.cache')) { // 5.3
                if ( preg_match('/\.([^\.])/' . $cached_file_unlink, $cache_file) && strstr($cache_file, '.cache')) {				
                  @unlink(DIR_FS_CACHE . $cache_file);
                }
              }
            }
            closedir($dir);
          }
        } else {
          // not used using hide products or regular osC, but if so, it assumes the $cache_blocks[$i]['file'] does 
          // contain the .cache on the end for example whatever_box-language.cache
          $cached_file = $cache_blocks[$i]['file'];
          $languages = tep_get_languages();
          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
//            $cached_file = ereg_replace('-language', '-' . $languages[$i]['directory'], $cached_file); // 5.3
            $cached_file = preg_replace('/-language/', '-' . $languages[$i]['directory'], $cached_file);			
            unlink(DIR_FS_CACHE . $cached_file);
          }
        }
        break;
      }
    }
  }
// eof hide products for sppc
// EOF: XSell

  function tep_get_file_permissions($mode) {
// determine type
    if ( ($mode & 0xC000) == 0xC000) { // unix domain socket
      $type = 's';
    } elseif ( ($mode & 0x4000) == 0x4000) { // directory
      $type = 'd';
    } elseif ( ($mode & 0xA000) == 0xA000) { // symbolic link
      $type = 'l';
    } elseif ( ($mode & 0x8000) == 0x8000) { // regular file
      $type = '-';
    } elseif ( ($mode & 0x6000) == 0x6000) { //bBlock special file
      $type = 'b';
    } elseif ( ($mode & 0x2000) == 0x2000) { // character special file
      $type = 'c';
    } elseif ( ($mode & 0x1000) == 0x1000) { // named pipe
      $type = 'p';
    } else { // unknown
      $type = '?';
    }

// determine permissions
    $owner['read']    = ($mode & 00400) ? 'r' : '-';
    $owner['write']   = ($mode & 00200) ? 'w' : '-';
    $owner['execute'] = ($mode & 00100) ? 'x' : '-';
    $group['read']    = ($mode & 00040) ? 'r' : '-';
    $group['write']   = ($mode & 00020) ? 'w' : '-';
    $group['execute'] = ($mode & 00010) ? 'x' : '-';
    $world['read']    = ($mode & 00004) ? 'r' : '-';
    $world['write']   = ($mode & 00002) ? 'w' : '-';
    $world['execute'] = ($mode & 00001) ? 'x' : '-';

// adjust for SUID, SGID and sticky bit
    if ($mode & 0x800 ) $owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x400 ) $group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x200 ) $world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

    return $type .
           $owner['read'] . $owner['write'] . $owner['execute'] .
           $group['read'] . $group['write'] . $group['execute'] .
           $world['read'] . $world['write'] . $world['execute'];
  }

  function tep_remove($source) {
    global $messageStack, $tep_remove_error;

    if (isset($tep_remove_error)) $tep_remove_error = false;

    if (is_dir($source)) {
      $dir = dir($source);
      while ($file = $dir->read()) {
        if ( ($file != '.') && ($file != '..') ) {
          if (tep_is_writable($source . '/' . $file)) {
            tep_remove($source . '/' . $file);
          } else {
            $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source . '/' . $file), 'error');
            $tep_remove_error = true;
          }
        }
      }
      $dir->close();

      if (tep_is_writable($source)) {
        rmdir($source);
      } else {
        $messageStack->add(sprintf(ERROR_DIRECTORY_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    } else {
      if (tep_is_writable($source)) {
        unlink($source);
      } else {
        $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    }
  }

////
// Output the tax percentage with optional padded decimals
  function tep_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
    if (strpos($value, '.')) {
      $loop = true;
      while ($loop) {
        if (substr($value, -1) == '0') {
          $value = substr($value, 0, -1);
        } else {
          $loop = false;
          if (substr($value, -1) == '.') {
            $value = substr($value, 0, -1);
          }
        }
      }
    }

    if ($padding > 0) {
      if ($decimal_pos = strpos($value, '.')) {
        $decimals = strlen(substr($value, ($decimal_pos+1)));
        for ($i=$decimals; $i<$padding; $i++) {
          $value .= '0';
        }
      } else {
        $value .= '.';
        for ($i=0; $i<$padding; $i++) {
          $value .= '0';
        }
      }
    }

    return $value;
  }

// bof email send html
//  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {
function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address, $htm=false) {	
// eof email send html	
    if (SEND_EMAILS != 'true') return false;

    // Instantiate a new mail object
    $message = new email(array('X-Mailer: osCommerce'));

    // Build the text version
    $text = strip_tags($email_text);
    if (EMAIL_USE_HTML == 'true') {
// bof email send html	    
//      $message->add_html($email_text, $text);
      $message->add_html($email_text, $text, '',$htm);
// eof email send html      
    } else {
      $message->add_text($text);
    }

    // Send message
    $message->build_message();
    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
  }


  function tep_get_tax_class_title($tax_class_id) {
    if ($tax_class_id == '0') {
      return TEXT_NONE;
    } else {
      $classes_query = tep_db_query("select tax_class_title from " . TABLE_TAX_CLASS . " where tax_class_id = '" . (int)$tax_class_id . "'");
      $classes = tep_db_fetch_array($classes_query);

      return $classes['tax_class_title'];
    }
  }

  function tep_banner_image_extension() {
    if (function_exists('imagetypes')) {
      if (imagetypes() & IMG_PNG) {
        return 'png';
      } elseif (imagetypes() & IMG_JPG) {
        return 'jpg';
      } elseif (imagetypes() & IMG_GIF) {
        return 'gif';
      }
    } elseif (function_exists('imagecreatefrompng') && function_exists('imagepng')) {
      return 'png';
    } elseif (function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) {
      return 'jpg';
    } elseif (function_exists('imagecreatefromgif') && function_exists('imagegif')) {
      return 'gif';
    }

    return false;
  }

////
// Wrapper function for round() for php3 compatibility
  function tep_round($value, $precision) {
    return round($value, $precision);
  }

////
// Add tax to a products price
  function tep_add_tax($price, $tax, $override = false) {
    if ( ( (DISPLAY_PRICE_WITH_TAX == 'true') || ($override == true) ) && ($tax > 0) ) {
      return $price + tep_calculate_tax($price, $tax);
    } else {
      return $price;
    }
  }

// Calculates Tax rounding the result
  function tep_calculate_tax($price, $tax) {
    return $price * $tax / 100;
  }

////
// Returns the tax rate for a zone / class
// TABLES: tax_rates, zones_to_geo_zones
  function tep_get_tax_rate($class_id, $country_id = -1, $zone_id = -1) {
    //global $customer_zone_id, $customer_country_id;
    global $customer_zone_id, $customer_country_id, $sppc_customer_group_show_tax;  //HW: ADDED TO FIX PROBLEM WITH ZERO TAX BEING RETURNED WHEN USER LOGGED IN.
    if ( ($country_id == -1) && ($zone_id == -1) ) {
      if (!tep_session_is_registered('customer_id')) {
        $country_id = STORE_COUNTRY;
        $zone_id = STORE_ZONE;
      } else {
        $country_id = $customer_country_id;
        $zone_id = $customer_zone_id;
      }
    }

    $tax_query = tep_db_query("select SUM(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za ON tr.tax_zone_id = za.geo_zone_id left join " . TABLE_GEO_ZONES . " tz ON tz.geo_zone_id = tr.tax_zone_id WHERE (za.zone_country_id IS NULL OR za.zone_country_id = '0' OR za.zone_country_id = '" . (int)$country_id . "') AND (za.zone_id IS NULL OR za.zone_id = '0' OR za.zone_id = '" . (int)$zone_id . "') AND tr.tax_class_id = '" . (int)$class_id . "' GROUP BY tr.tax_priority");
    if (tep_db_num_rows($tax_query)) {
      $tax_multiplier = 0;
      while ($tax = tep_db_fetch_array($tax_query)) {
        $tax_multiplier += $tax['tax_rate'];
      }
      return $tax_multiplier;
    } else {
      return 0;
    }
  }

////
// Returns the tax rate for a tax class
// TABLES: tax_rates
  function tep_get_tax_rate_value($class_id) {
// bof 2.3.3.1
//    $tax_query = tep_db_query("select SUM(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " where tax_class_id = '" . (int)$class_id . "' group by tax_priority");
//    if (tep_db_num_rows($tax_query)) {
//      $tax_multiplier = 0;
//      while ($tax = tep_db_fetch_array($tax_query)) {
//        $tax_multiplier += $tax['tax_rate'];
//      }
//      return $tax_multiplier;
//    } else {
//      return 0;
//    }
   return tep_get_tax_rate($class_id, -1, -1);
// eof 2.3.3.1   
  }

  function tep_call_function($function, $parameter, $object = '') {
    if ($object == '') {
      return call_user_func($function, $parameter);
    } else {
      return call_user_func(array($object, $function), $parameter);
    }
  }

  function tep_get_zone_class_title($zone_class_id) {
    if ($zone_class_id == '0') {
      return TEXT_NONE;
    } else {
      $classes_query = tep_db_query("select geo_zone_name from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . (int)$zone_class_id . "'");
      $classes = tep_db_fetch_array($classes_query);

      return $classes['geo_zone_name'];
    }
  }

  function tep_cfg_pull_down_zone_classes($zone_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $zone_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $zone_class_query = tep_db_query("select geo_zone_id, geo_zone_name from " . TABLE_GEO_ZONES . " order by geo_zone_name");
    while ($zone_class = tep_db_fetch_array($zone_class_query)) {
      $zone_class_array[] = array('id' => $zone_class['geo_zone_id'],
                                  'text' => $zone_class['geo_zone_name']);
    }

    return tep_draw_pull_down_menu($name, $zone_class_array, $zone_class_id);
  }

  function tep_cfg_pull_down_order_statuses($order_status_id, $key = '') {
    global $languages_id;

    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $statuses_array = array(array('id' => '0', 'text' => TEXT_DEFAULT));
    $statuses_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$languages_id . "' order by orders_status_name");
    while ($statuses = tep_db_fetch_array($statuses_query)) {
      $statuses_array[] = array('id' => $statuses['orders_status_id'],
                                'text' => $statuses['orders_status_name']);
    }

    return tep_draw_pull_down_menu($name, $statuses_array, $order_status_id);
  }

  function tep_get_order_status_name($order_status_id, $language_id = '') {
    global $languages_id;

    if ($order_status_id < 1) return TEXT_DEFAULT;

    if (!is_numeric($language_id)) $language_id = $languages_id;

    $status_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . (int)$order_status_id . "' and language_id = '" . (int)$language_id . "'");
    $status = tep_db_fetch_array($status_query);

    return $status['orders_status_name'];
  }

////
// Return a random value
function tep_rand($min = null, $max = null) {
        static $seeded;

        if (!isset($seeded)) {
          $seeded = true;

          if ( (PHP_VERSION < '4.2.0') ) {
                mt_srand((double)microtime()*1000000);
          }
        }

        if (isset($min) && isset($max)) {
          if ($min >= $max) {
                return $min;
          } else {
                return mt_rand($min, $max);
          }
        } else {
          return mt_rand();
        }
  }

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
  function tep_convert_linefeeds($from, $to, $string) {
    if ((PHP_VERSION < "4.0.5") && is_array($from)) {
      return preg_replace('/(' . implode('|', $from) . ')/', $to, $string);
    } else {
      return str_replace($from, $to, $string);
    }
  }

  function tep_string_to_int($string) {
    return (int)$string;
  }

////
// Parse and secure the cPath parameter values
  function tep_parse_category_path($cPath) {
// make sure the category IDs are integers
    $cPath_array = array_map('tep_string_to_int', explode('_', $cPath));

// make sure no duplicate category IDs exist which could lock the server in a loop
    $tmp_array = array();
    $n = sizeof($cPath_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($cPath_array[$i], $tmp_array)) {
        $tmp_array[] = $cPath_array[$i];
      }
    }

    return $tmp_array;
  }

  function tep_validate_ip_address($ip_address) {
    if (function_exists('filter_var') && defined('FILTER_VALIDATE_IP')) {
      return filter_var($ip_address, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV4));
    }

    if (preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $ip_address)) {
      $parts = explode('.', $ip_address);

      foreach ($parts as $ip_parts) {
        if ( (int($ip_parts) > 255) || (int($ip_parts) < 0) ) {
          return false; // number is not within 0-255
        }
      }

      return true;
    }

    return false;
  }

  function tep_get_ip_address() {
    global $HTTP_SERVER_VARS;

    $ip_address = null;
    $ip_addresses = array();

    if (isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR']) && !empty($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])) {
      foreach ( array_reverse(explode(',', $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])) as $x_ip ) {
        $x_ip = trim($x_ip);

        if (tep_validate_ip_address($x_ip)) {
          $ip_addresses[] = $x_ip;
        }
      }
    }

    if (isset($HTTP_SERVER_VARS['HTTP_CLIENT_IP']) && !empty($HTTP_SERVER_VARS['HTTP_CLIENT_IP'])) {
      $ip_addresses[] = $HTTP_SERVER_VARS['HTTP_CLIENT_IP'];
    }

    if (isset($HTTP_SERVER_VARS['HTTP_X_CLUSTER_CLIENT_IP']) && !empty($HTTP_SERVER_VARS['HTTP_X_CLUSTER_CLIENT_IP'])) {
      $ip_addresses[] = $HTTP_SERVER_VARS['HTTP_X_CLUSTER_CLIENT_IP'];
    }

    if (isset($HTTP_SERVER_VARS['HTTP_PROXY_USER']) && !empty($HTTP_SERVER_VARS['HTTP_PROXY_USER'])) {
      $ip_addresses[] = $HTTP_SERVER_VARS['HTTP_PROXY_USER'];
    }

    $ip_addresses[] = $HTTP_SERVER_VARS['REMOTE_ADDR'];

    foreach ( $ip_addresses as $ip ) {
      if (!empty($ip) && tep_validate_ip_address($ip)) {
        $ip_address = $ip;
        break;
      }
    }

    return $ip_address;
  }

////
// Wrapper function for is_writable() for Windows compatibility
  function tep_is_writable($file) {
    if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
      if (file_exists($file)) {
        $file = realpath($file);
        if (is_dir($file)) {
          $result = @tempnam($file, 'osc');
          if (is_string($result) && file_exists($result)) {
            unlink($result);
            return (strpos($result, $file) === 0) ? true : false;
          }
        } else {
          $handle = @fopen($file, 'r+');
          if (is_resource($handle)) {
            fclose($handle);
            return true;
          }
        }
      } else{
        $dir = dirname($file);
        if (file_exists($dir) && is_dir($dir) && tep_is_writable($dir)) {
          return true;
        }
      }
      return false;
    } else {
      return is_writable($file);
    }
  }
  
/*** Begin Header Tags SEO ***/
require('includes/functions/header_tags_general.php');
/*** End Header Tags SEO ***/

// Gallery
// delete the same function in sec_dir_permissions.php

   function tep_opendir($path) {
    $path = rtrim($path, '/') . '/';
    $exclude_array = array('.', '..', '.DS_Store', 'Thumbs.db','.php', '_notes', '.html');
    $result = array();
    if ($handle = opendir($path)) {
      while (false !== ($filename = readdir($handle))) {
        if (!in_array($filename, $exclude_array)) {
          $file = array('name' => $path . $filename,
                        'is_dir' => is_dir($path . $filename),
                        'writable' => is_writable($path . $filename));
          $result[] = $file;
          if ($file['is_dir'] == true) {
            $result = array_merge((array)$result, (array)tep_opendir($path . $filename));
          }
        }
      }
      closedir($handle);
    }
    return $result;
  }               

// front page for text main ckeditor  
  function tep_get_config_value( $config_value ) {
    return $config_value ;
	}
function tep_get_text_class() {
    return 'class="ckeditor"' ;
	}
	
// eof front page for text main ckeditor
	
  /**
  * ULTIMATE Seo Urls 5 PRO by FWR Media
  * Reset the various cache systems
  * @param string $action
  */
  /**
  * ULTIMATE Seo Urls 5 PRO by FWR Media
  * Reset the various cache systems
  * @param string $action R205
  */
  function tep_reset_cache_data_usu5( $action = false ) {
    global $multi_stores_config ;
    if ( $action == 'reset' ) {
      $usu5_path = realpath( dirname( __FILE__ ) . '/../../../' ) . '/' . DIR_WS_MODULES . 'ultimate_seo_urls5/';
      switch( USU5_CACHE_SYSTEM ) {
        case 'file': 
          $path_to_cache = $usu5_path . 'cache_system/cache/';
          $it = new DirectoryIterator( $path_to_cache );
          while( $it->valid() ) {
            if ( !$it->isDot() && is_readable( $path_to_cache . $it->getFilename() ) && ( substr( $it->getFilename(), -6 ) == '.cache' ) ) {
              @unlink( $path_to_cache . $it->getFilename() );
            }
            $it->next();
          }
          break;
        case 'mysql':
          tep_db_query( 'TRUNCATE TABLE `usu_cache`' );
          break;
        case 'memcache':
          if ( class_exists('Memcache') ){
            include $usu5_path . 'interfaces/cache_interface.php';
            include $usu5_path . 'cache_system/memcache.php';
            Memcache_Cache_Module::iAdmin()->initiate()
                                           ->flushOut();
          }
          break;
        case 'sqlite':
          include $usu5_path . 'interfaces/cache_interface.php';
          include $usu5_path . 'cache_system/sqlite.php';
          Sqlite_Cache_Module::admini()->gc(); 
          break;
      }
//      tep_db_query( "UPDATE " . $multi_stores_config . " SET configuration_value='false' WHERE configuration_key='USU5_RESET_CACHE'" );
    }    
    tep_db_query( "UPDATE " . $multi_stores_config . " SET configuration_value='false' WHERE configuration_key='USU5_RESET_CACHE'" );	
  } // end function
    // bof Dynamic Template System
	
//// Return an array of the catalog directory. mechanism for reading this.  
  function tep_list_catalog_files () {
    $d = dir(DIR_FS_CATALOG);
    $result = array();   
    $exclude = array('redirect.php', 'popup_search_help.php', 'popup_image.php', 'opensearch.php', 'info_shopping_cart.php', 'download.php', 'checkout_process.php',	
 'mobile_account.php', 'mobile_account_edit.php', 'mobile_account_history.php', 'mobile_account_history_info.php',  'mobile_account_newsletters.php',
 'mobile_account_notifications.php', 'mobile_account_password.php', 'mobile_address_book.php', 'mobile_address_book_process.php', 'mobile_checkout_confirmation.php',
 'mobile_checkout_payment.php', 'mobile_checkout_payment_address.php', 'mobile_checkout_process.php', 'mobile_checkout_shipping.php', 'mobile_checkout_shipping_address.php',
 'mobile_checkout_success.php', 'mobile_create_account.php', 'mobile_create_account_success.php', 'mobile_login.php', 'mobile_logoff.php', 'mobile_password_forgotten.php',
 'mobile_popup_image.php', 'mobile_popup_images.php', 'mobile_popup_search_help.php', 'mobile_popup_shipping.php', 'mobile_ssl_check.php', 'mobile_about.php',
 'mobile_advanced_search_result.php', 'mobile_catalogue.php', 'mobile_conditions.php', 'mobile_contact_us.php', 'mobile_index.php', 'mobile_currencies.php',
 'mobile_languages.php', 'mobile_products.php', 'mobile_privacy.php', 'mobile_product_info.php', 'mobile_product_reviews.php', 'mobile_product_reviews_write.php',
 'mobile_product_thumb.php', 'mobile_search.php', 'mobile_shipping.php', 'mobile_shopping_cart.php',
 'pdfinvoice.php' ) ;
	  while (false !== ($file = $d->read())) {
        if($file != '.' && $file != '..' && !is_dir($file) && (substr($file, -3, 3) == 'php') && !in_array($file, $exclude)) {  
            $result[] = $file;
        }
    }    
    $d->close();    
    return $result;
  }
  
////
// Alias function for module [boxes] configuration value
  function old_tep_cfg_select_pages($key_value, $key = '') {
    $name = ((tep_not_null($key)) ? 'configuration[' . $key . '][]' : 'configuration_value');
    $select_array = tep_list_catalog_files();    
    $selected_array = explode(';', $key_value);
    if($key_value === 'null') { $checkall = "UNCHECKED"; $checkany = "CHECKED";}
    if($key_value === 'all') { $checkall = "CHECKED"; $checkany = "UNCHECKED";} 
      $string = '<fieldset>';   
	  	  $string .= '<div class="form-group">'. PHP_EOL ;
      $string .= '<input type="radio" class="AllPages"  name="' . $name . '" value="all" ' . $checkall . ' />' . BOXES_ALL_PAGES . '<br />';
      $string .= '<input type="radio" class="AnyPages"  name="' . $name . '" value="null" ' . $checkany . ' />' . BOXES_ANY_PAGES . '<br />';
      $string .= '<br /><strong>&nbsp;&nbsp;' . BOXES_ONE_BY_ONE . '</strong><br /><br />';    
      for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {        
      $string .= '&nbsp;&nbsp;<input type="checkbox" class="ThisPage" name="' . $name . '" value="' . $select_array[$i] . ';"';
//        $string .= '&nbsp;&nbsp;<input type="checkbox" id="file_' . $i . '" class="ThisPage" name="' . $name . '" value="' . $select_array[$i] . '"';
          if(isset($selected_array))
            {                      
            foreach($selected_array as $value){            
               if ($select_array[$i] == $value) $string .= ' CHECKED';
               }
            }
      $string .= '>' . $select_array[$i] . '<br />';
      }      
	  $string .= '</div>'. PHP_EOL ;	  
      $string .= '</fieldset>';
      $string .= "<script type=\"text/javascript\">   
                  jQuery(document).ready(function () {     
                      $(\".AllPages\").click(
                          function() {               
                              $(this).parents('fieldset:eq(0)').find('.ThisPage').attr('checked', false);
                              $(this).parents('fieldset:eq(0)').find('.AnyPages').attr('checked', false);               
                          }
                      );
                      $(\".AnyPages\").click(
                          function() {               
                              $(this).parents('fieldset:eq(0)').find('.ThisPage').attr('checked', false);
                              $(this).parents('fieldset:eq(0)').find('.AllPages').attr('checked', false);
                          }
                      );                                              
                      $('.ThisPage').click(
                          function() {
                              if ($(this).parents('fieldset:eq(0)').find('.AllPages').attr('checked') == true && this.checked == false)
                                  $(this).parents('fieldset:eq(0)').find('.AllPages').attr('checked', false); 
                              if (this.checked == true) {
                                  var flag = true;
                                  var shlag = false;
                                  $(this).parents('fieldset:eq(0)').find('.ThisPage').each(   
              	                    function() {
              	                        if (this.checked == false) {
              	                            flag = false;              	                            
                                          }
              	                    }
            	                    
                                  );
                                  $(this).parents('fieldset:eq(0)').find('.AllPages').attr('checked', flag);
                                  $(this).parents('fieldset:eq(0)').find('.AnyPages').attr('checked', shlag);
                              }
                          }
                      );
                  }
                  );</script>";
      return $string;
  }
// eof Dynamic Template System 
// Alias function for module [boxes] configuration value
  function tep_cfg_select_pages($key_value, $key = '') {
    $name = ((tep_not_null($key)) ? 'configuration[' . $key . '][]' : 'configuration_value');
    $select_array = tep_list_catalog_files();    
    $selected_array = explode(';', $key_value);
    if($key_value === 'null') { $checkall = false; $checkany = true;}
    if($key_value === 'all') { $checkall = true; $checkany = false;} 
      $string  = '<fieldset>'; 
	  $string .= '<div class=form-group>'. PHP_EOL ;
      $string .=      tep_bs_radio_field($name, 'all',  BOXES_ALL_PAGES, 'input_Pages_All', $checkall, 'radio radio-success radio-inline', '', 'AllPages', 'right') . PHP_EOL ;
//      $string .=      tep_bs_radio_field($name, 'null', BOXES_ANY_PAGES, 'input_Pages_Any', $checkany, 'radio radio-success radio-inline', '', 'AnyPages', 'right') . PHP_EOL ;
//      $string .= '<input type="radio" class="AllPages"  name="' . $name . '" value="all" ' . $checkall . ' />' . BOXES_ALL_PAGES . '<br />';
//      $string .= '<input type="radio" class="AnyPages"  name="' . $name . '" value="null" ' . $checkany . ' />' . BOXES_ANY_PAGES . '<br />';
      $string .= '</div>'. PHP_EOL ; 
      $string .= '<br /><strong>&nbsp;&nbsp;' . BOXES_ONE_BY_ONE . '</strong><br /><br />';    

      $string .=  tep_bs_checkbox_field('CheckAll', $select_array[$i] . ';', CHECK_ALL, 'CheckAllLabel', false, 'checkbox checkbox-success', '', 'CheckAll', 'right') . PHP_EOL ;	  
//    $string .= '<input type="checkbox" id="CheckAll" class="CheckAll" name="CheckAll" /><label id="CheckAllLabel" for="CheckAll">' . CHECK_ALL . '</label></p>';	  
      for ($i=0, $n=sizeof($select_array); $i<$n; $i++) { 

	      $file_checked = false ;
          if(isset($selected_array)) {                      
            foreach($selected_array as $value){            
               if ($select_array[$i] == $value) $file_checked = true;
               }
          }	  
        $string .=  tep_bs_checkbox_field($name, $select_array[$i] . ';', $select_array[$i], 'file_' . $i, $file_checked, 'checkbox checkbox-success', '', 'ThisPage', 'right') . PHP_EOL ;
      }     
      $string .= '</fieldset>';
      $string .= "<script type=\"text/javascript\">
                    jQuery(document).ready(function () {
                     $('.AllPages').click(
                        function() {
                          $('.ThisPage').prop('checked', false);
                          $('.CheckAll').prop('checked', false);
                          $('#CheckAllLabel').text('" . CHECK_ALL . "');
                        }
                     );
                     $('.CheckAll').click(
                        function () {
                          $(this).parents('fieldset:eq(0)').find(':checkbox').prop('checked', this.checked);
                          $('.AllPages').prop('checked', (!this.checked));
                          if (this.checked) {
                             $('#CheckAllLabel').text('" . DESELECT_ALL . "');
                          } else {
                             $('#CheckAllLabel').text('" . CHECK_ALL . "');
                          }
                        }
                     );
                     $('.ThisPage').click(
                        function() {
                           var n = $( \"input.ThisPage:checked\" ).length;
                           if (n >0) {
                              $('.AllPages').prop('checked', false);
                           } else {
                              $('.AllPages').prop('checked', true);
                           }
                        }
                     );
                    });
                  </script>";
      return $string;
  }
  
// Alias function for module [boxes] configuration value
// template system  
  function oud2_tep_cfg_select_pages($key_value, $key = '') {
    $name = ((tep_not_null($key)) ? 'configuration[' . $key . '][]' : 'configuration_value');
    $select_array = tep_list_catalog_files();    
    $selected_array = explode(';', $key_value);

    if($key_value === 'all') { 
      $checkall = true ; 
    } else { 
      $checkall = false; 
    }

      $string = '<fieldset>';    
	  $string .= '<div class="form-group">'. PHP_EOL ;	 
      $string .=      tep_bs_radio_field($name, 'all',  BOXES_ALL_PAGES, 'input_Pages_All', $checkall, 'radio radio-success radio-inline', '', 'AllPages', 'right') . PHP_EOL ;
	  $string .= '</div>' .PHP_EOL ;
//      $string .=      tep_bs_radio_field($name, 'null', BOXES_ANY_PAGES, 'input_Pages_Any', $checkany, 'radio radio-success radio-inline', '', 'AnyPages', 'right') . PHP_EOL ;	  
//      $string .= '<input type="radio" class="AllPages"  name="' . $name . '" value="all" ' . $checkall . ' />' . ALL_PAGES . '<br />';
      $string .= '<p><strong>&nbsp;&nbsp;' . ONE_BY_ONE . '</strong><br />';
      $string .= '<input type="checkbox" id="CheckAll" class="CheckAll" name="CheckAll" /><label id="CheckAllLabel" for="CheckAll">' . CHECK_ALL . '</label></p>';

      for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {
        $string .= '&nbsp;&nbsp;<input type="checkbox" id="file_' . $i . '" class="ThisPage" name="' . $name . '" value="' . $select_array[$i] . ';"';
        if ( isset($selected_array) ) {
          foreach($selected_array as $value) {
            if ($select_array[$i] == $value) $string .= ' CHECKED';
          }
        }
        $string .= '><label class="ThisPage" for="file_' . $i . '">' . $select_array[$i] . '</label><br />';
      }
	  $string .= '</div>'. PHP_EOL ;	  
      $string .= '</fieldset>';
      $string .= "<script type=\"text/javascript\">
  jQuery(document).ready(function () {
    $('.AllPages').click(
      function() {
        $('.ThisPage').prop('checked', false);
        $('.CheckAll').prop('checked', false);
        $('#CheckAllLabel').text('" . CHECK_ALL . "');
      }
    );
    $('.CheckAll').click(
      function () {
        $(this).parents('fieldset:eq(0)').find(':checkbox').prop('checked', this.checked);
        $('.AllPages').prop('checked', (!this.checked));
        if (this.checked) {
          $('#CheckAllLabel').text('" . DESELECT_ALL . "');
        } else {
          $('#CheckAllLabel').text('" . CHECK_ALL . "');
        }
      }
    );
    $('.ThisPage').click(
      function() {
        var n = $( \"input.ThisPage:checked\" ).length;
        if (n >0) {
          $('.AllPages').prop('checked', false);
        } else {
          $('.AllPages').prop('checked', true);
        }
      }
    );
  });
</script>";
      return $string;
  }
// eof Dynamic Template System  
// eof Dynamic Template System 
//// bof Address Format drop down
// Get list of international address_format_id's
  function tep_get_address_formats_for_countries() {
    $address_format_query = tep_db_query("select address_format_id, address_format from " . TABLE_ADDRESS_FORMAT . " order by address_format_id");
    $address_format_array = array();
    $patterns = array('/\$firstname \$lastname/','/\$streets/','/\$city/','/\$postcode/','/\$state/','/\$country/','/\$/','/comma/','/cr/');
    $replacements = array(substr(ENTRY_FIRST_NAME, 0, -1) . '&nbsp;' . substr(ENTRY_LAST_NAME, 0, -1),substr(ENTRY_STREET_ADDRESS, 0, -1),substr(ENTRY_CITY, 0 ,-1),substr(ENTRY_POST_CODE,0,-1),substr(ENTRY_STATE,0,-1),substr(ENTRY_COUNTRY,0,-1),' ',';',' | ');    
    while ($address_format_values = tep_db_fetch_array($address_format_query)) {    
      $address_format_array[] = array('id' => $address_format_values['address_format_id'],
                                      'text' => substr(TEXT_INFO_ADDRESS_FORMAT,0,-1) . '&nbsp;' . $address_format_values['address_format_id']. ' : | ' . preg_replace($patterns, $replacements, $address_format_values['address_format']));
    }
    return $address_format_array;
  }
// eof Address Format drop down
//BOF qpbpp
//function qpbpp_insert_update_discount_cats($products_id, $current_discount_categories_id, $new_discount_categories_id) {
//  if (!tep_not_null($products_id)) {
//    return false; // if $products_id is not set stop here
//  }
//  if ($current_discount_categories_id == $new_discount_categories_id) {
//    return true; // if they are the same no update is necessary
//  }
//  if ($current_discount_categories_id == 0 && $new_discount_categories_id > 0) {
//    // insert needed
//    tep_db_query("insert into " . TABLE_PRODUCTS_TO_DISCOUNT_CATEGORIES . " (products_id, discount_categories_id) values ('" . (int)$products_id . "', '" . (int)$new_discount_categories_id . "')");
//    return true;
//  }
//  if ($current_discount_categories_id > 0 && $new_discount_categories_id == 0) {
//    // delete needed
//    tep_db_query("delete from " . TABLE_PRODUCTS_TO_DISCOUNT_CATEGORIES . " where products_id = '" . (int)$products_id . "'");
//    return true;
//  }
//  if ($current_discount_categories_id > 0 && ($current_discount_categories_id !== $new_discount_categories_id)) {
//   // update needed
//    tep_db_query("update " . TABLE_PRODUCTS_TO_DISCOUNT_CATEGORIES . " set discount_categories_id = '" . (int)$new_discount_categories_id . "' where products_id = '" . (int)$products_id . "' and discount_categories_id = '" . (int)$current_discount_categories_id . "'");
//    return true;
//  }
//  return false; // for good measure
//}
//EOF qpbpp
//BOF QPBPP for SPPC
function qpbpp_insert_update_discount_cats($products_id, $current_discount_categories_id, $new_discount_categories_id, $customers_group_id) {
  if (!tep_not_null($products_id)) {
    return false; // if $products_id is not set stop here
  }
  if ($current_discount_categories_id == $new_discount_categories_id) {
    return true; // if they are the same no update is necessary
  }
  if ($current_discount_categories_id == 0 && $new_discount_categories_id > 0) {
    // insert needed
    tep_db_query("insert into " . TABLE_PRODUCTS_TO_DISCOUNT_CATEGORIES . " (products_id, discount_categories_id, customers_group_id) values ('" . (int)$products_id . "', '" . (int)$new_discount_categories_id . "', '" . (int)$customers_group_id . "')");
    return true;
  }
  if ($current_discount_categories_id > 0 && $new_discount_categories_id == 0) {
    // delete needed
    tep_db_query("delete from " . TABLE_PRODUCTS_TO_DISCOUNT_CATEGORIES . " where products_id = '" . (int)$products_id . "' and customers_group_id = '" . (int)$customers_group_id . "'");
    return true;
  }
  if ($current_discount_categories_id > 0 && ($current_discount_categories_id !== $new_discount_categories_id)) {
    // update needed
    tep_db_query("update " . TABLE_PRODUCTS_TO_DISCOUNT_CATEGORIES . " set discount_categories_id = '" . (int)$new_discount_categories_id . "' where products_id = '" . (int)$products_id . "' and discount_categories_id = '" . (int)$current_discount_categories_id . "' and customers_group_id = '" . (int)$customers_group_id . "'");
    return true;
  }
  return false; // for good measure
}

  function sortByQty($a, $b) {
    if ($a['products_qty'] == $b['products_qty']) {
      return 0;
    }
     if ($a['products_qty'] < $b['products_qty']) {
      return -1;
    }
      return 1;
  }
//EOF QPBPP for SPPC
//////create a pull down for all payment installed payment methods for Order Editor configuration
   
  // Get list of all payment modules available
  function tep_cfg_pull_down_payment_methods() {
  global $language;
  $enabled_payment = array();
  $module_directory = DIR_FS_CATALOG_MODULES . 'payment/';
  $file_extension = '.php';

  if ($dir = @dir($module_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir( $module_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  // For each available payment module, check if enabled
  for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
    $file = $directory_array[$i];

    include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $file);
    include($module_directory . $file);

    $class = substr($file, 0, strrpos($file, '.'));
    if (tep_class_exists($class)) {
      $module = new $class;
      if ($module->check() > 0) {
        // If module enabled create array of titles
        $enabled_payment[] = array('id' => $module->title, 'text' => $module->title);
                
      }
   }
 }
                                
    $enabled_payment[] = array('id' => 'Other', 'text' => 'Other');     
                
                //draw the dropdown menu for payment methods and default to the order value
          return tep_draw_pull_down_menu('configuration_value', $enabled_payment, '', ''); 
                }


/////end payment method dropdown
// bof email send html
function tep_add_base_ref($string) {
    $i = 0;
    $output = '';
		$n=strlen($string);
		for ($i=0; $i<$n; $i++) {
    		$char = substr($string, $i, 1);
		$char5 = substr($string, $i, 5);
		  if ($char5 == 'src="' ) {$output .= 'src="' . HTTP_SERVER; $i = $i+4;}
		 else {
      		 $output .= $char; 
	  }	}
    return $output;
  }
  // Added chris23. New wrapper around email class to handle string attachments. Allows emailing of pdf customer invoices
  // Additional parameters:
  // $string = string data (ascii or binary)
  // $filename = target filename for attached data
  
  function tep_mail_string_attachment($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address, $string, $filename) {
    if (SEND_EMAILS != 'true') return false;

    // Instantiate a new mail object
    $message = new email(array('X-Mailer: osCommerce'));

    // Build the text version
    $text = strip_tags($email_text);
    if (EMAIL_USE_HTML == 'true') {
      $message->add_html($email_text, $text);
    } else {
      $message->add_text($text);
    }
    
    // Now add string attachment - new method of email.php
    $message->add_string_attachment($string, $filename, 'application/pdf');


    // Send message
    $message->build_message();
    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
  }
// eof email send html
// bof tooltip orders at a glance
 // Function    : tep_html_noquote
  // Arguments   : string	any string
  // Return      : strips apostrophes from strings
  //Used with "Orders at a glance" and "Comments at a Glance" to workaround problem with apostrophes, double quotes, and line breaks
  
  function tep_html_noquote($string) {
  $string=str_replace('&#39;', '', $string);
  $string=str_replace("'", "", $string);
  $string=str_replace('"', '', $string);
  $string=preg_replace("/\\r\\n|\\n|\\r/", "<BR>", $string); 
  return $string;
	
  }
// bof products multi copy etc
  function tep_array_merge($array1, $array2, $array3 = '') {
    if ($array3 == '') $array3 = array();
    if (function_exists('array_merge')) {
      $array_merged = array_merge((array)$array1, (array)$array2, (array)$array3);
    } else {
      while (list($key, $val) = each($array1)) $array_merged[$key] = $val;
      while (list($key, $val) = each($array2)) $array_merged[$key] = $val;
      if (sizeof($array3) > 0) while (list($key, $val) = each($array3)) $array_merged[$key] = $val;
    }

    return (array) $array_merged;
  }
// Change password -- copied from catalog/includes/functions/general.php
  function tep_create_random_value($length, $type = 'mixed') {
    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;

    $rand_value = '';
    while (strlen($rand_value) < $length) {
      if ($type == 'digits') {
        $char = tep_rand(0,9);
      } else {
        $char = chr(tep_rand(0,255));
      }
      if ($type == 'mixed') {
       //if (eregi('^[a-z0-9]$', $char)) $rand_value .= $char;
                if (preg_match('/^[a-z0-9]$/i', $char)) $rand_value .= $char;
          } elseif ($type == 'chars') {
                if (preg_match('/^[a-z]$/i', $char)) $rand_value .= $char;
          } elseif ($type == 'digits') {
                if (preg_match('/^[0-9]$/', $char)) $rand_value .= $char;
      }
    }

    return $rand_value;
  }
////
// Alias function for module [boxes] configuration value
  function tep_cfg_select_payment($key_value, $key = '') {
    $name = ((tep_not_null($key)) ? 'configuration[' . $key . '][]' : 'configuration_value');
    $select_array = tep_list_shipping_methods();    
    $selected_array = explode(';', $key_value);
 //   if($key_value === 'null') { $checkall = "UNCHECKED"; $checkany = "CHECKED";}
    if($key_value === 'all') { $checkall = "CHECKED"; } 
      $string = '<fieldset>';    
      $string .= '<input type="radio" class="AllPages"  name="' . $name . '" value="all" ' . $checkall . ' />' . SHIPPING_METHODS_ALL . '<br />';
//      $string .= '<input type="radio" class="AnyPages"  name="' . $name . '" value="null" ' . $checkany . ' />' . BOXES_ANY_PAGES . '<br />';
      $string .= '<br /><strong>&nbsp;&nbsp;' . SHIPPING_METHODS_ONE_BY_ONE . '</strong><br /><br />';    
      for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {        
      $string .= '&nbsp;&nbsp;<input type="checkbox" class="ThisPage" name="' . $name . '" value="' . $select_array[$i]['id'] . ';"';
          if(isset($selected_array))
            {                      
            foreach($selected_array as $value){            
               if ($select_array[$i]['id'] == $value) $string .= ' CHECKED';
               }
            }
      $string .= '>' . $select_array[$i]['text'] . '<br />';
      }      
      $string .= '</fieldset>';
      $string .= "<script type=\"text/javascript\">   
                  jQuery(document).ready(function () {     
                      $(\".AllPages\").click(
                          function() {               
                              $(this).parents('fieldset:eq(0)').find('.ThisPage').attr('checked', false);
                              $(this).parents('fieldset:eq(0)').find('.AnyPages').attr('checked', false);               
                          }
                      );                                
                      $('.ThisPage').click(
                          function() {
                              if ($(this).parents('fieldset:eq(0)').find('.AllPages').attr('checked') == true && this.checked == false)
                                  $(this).parents('fieldset:eq(0)').find('.AllPages').attr('checked', false); 
                              if (this.checked == true) {
                                  var flag = true;
                                  var shlag = false;
                                  $(this).parents('fieldset:eq(0)').find('.ThisPage').each(   
              	                    function() {
              	                        if (this.checked == false) {
              	                            flag = false;              	                            
                                          }
              	                    }
            	                    
                                  );
                                  $(this).parents('fieldset:eq(0)').find('.AllPages').attr('checked', flag);
                                  $(this).parents('fieldset:eq(0)').find('.AnyPages').attr('checked', shlag);
                              }
                          }
                      );
                  }
                  );</script>";
      return $string;
  }  
  
// get the active shipping modules
  function tep_list_shipping_methods() {
  global $language;
  $enabled_payment = array();
  $module_directory = DIR_FS_CATALOG_MODULES . 'shipping/';
  $file_extension = '.php';

  if ($dir = @dir($module_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir( $module_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  // For each available payment module, check if enabled
  for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
    $file = $directory_array[$i];

    include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/' . $file);
    include($module_directory . $file);

    $class = substr($file, 0, strrpos($file, '.'));
    if (tep_class_exists($class)) {
      $module = new $class;
      if ($module->check() > 0) {
        // If module enabled create array of titles
        $enabled_payment[] = array('id' => $module->code, 'text' => $module->title) ;
                
      }
   }
 }
 return $enabled_payment ;
}

// Alias function for module [boxes] configuration value
  function tep_cfg_select_order_total($key_value, $key = '') {
    $name = ((tep_not_null($key)) ? 'configuration[' . $key . '][]' : 'configuration_value');
    $select_array = tep_list_order_total_methods();    
    $selected_array = explode(';', $key_value);
 //   if($key_value === 'null') { $checkall = "UNCHECKED"; $checkany = "CHECKED";}
    if($key_value === 'all') { $checkall = "CHECKED"; } 
      $string = '<fieldset>';    
      $string .= '<input type="radio" class="AllPages"  name="' . $name . '" value="all" ' . $checkall . ' />' . SHIPPING_METHODS_ALL . '<br />';
//      $string .= '<input type="radio" class="AnyPages"  name="' . $name . '" value="null" ' . $checkany . ' />' . BOXES_ANY_PAGES . '<br />';
      $string .= '<br /><strong>&nbsp;&nbsp;' . SHIPPING_METHODS_ONE_BY_ONE . '</strong><br /><br />';    
      for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {        
      $string .= '&nbsp;&nbsp;<input type="checkbox" class="ThisPage" name="' . $name . '" value="' . $select_array[$i]['id'] . '"';
          if(isset($selected_array))
            {                      
            foreach($selected_array as $value){            
               if ($select_array[$i]['id'] == $value) $string .= ' CHECKED';
               }
            }
      $string .= '>' . $select_array[$i]['text'] . '<br />';
      }      
      $string .= '</fieldset>';
      $string .= "<script type=\"text/javascript\">   
                  jQuery(document).ready(function () {     
                      $(\".AllPages\").click(
                          function() {               
                              $(this).parents('fieldset:eq(0)').find('.ThisPage').attr('checked', false);
                              $(this).parents('fieldset:eq(0)').find('.AnyPages').attr('checked', false);               
                          }
                      );                                
                      $('.ThisPage').click(
                          function() {
                              if ($(this).parents('fieldset:eq(0)').find('.AllPages').attr('checked') == true && this.checked == false)
                                  $(this).parents('fieldset:eq(0)').find('.AllPages').attr('checked', false); 
                              if (this.checked == true) {
                                  var flag = true;
                                  var shlag = false;
                                  $(this).parents('fieldset:eq(0)').find('.ThisPage').each(   
              	                    function() {
              	                        if (this.checked == false) {
              	                            flag = false;              	                            
                                          }
              	                    }
            	                    
                                  );
                                  $(this).parents('fieldset:eq(0)').find('.AllPages').attr('checked', flag);
                                  $(this).parents('fieldset:eq(0)').find('.AnyPages').attr('checked', shlag);
                              }
                          }
                      );
                  }
                  );</script>";
      return $string;
  }  
  
// get the active shipping modules
  function tep_list_order_total_methods() {
  global $language;
  $enabled_payment = array();
  $module_directory = DIR_FS_CATALOG_MODULES . 'order_total/';
  $file_extension = '.php';

  if ($dir = @dir($module_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir( $module_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  // For each available payment module, check if enabled
  for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
    $file = $directory_array[$i];

    include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $file);
    include($module_directory . $file);

    $class = substr($file, 0, strrpos($file, '.'));
	$exclude = array( 'ot_total', 'ot_subtotal', 'ot_subtotal_ex', 'ot_tax', 'ot_loworderfee' ) ;
    if (tep_class_exists($class)) {
      $module = new $class;
      if ( ! in_array( $module->code , $exclude ) ) {	  
         if ($module->check() > 0) {
               // If module enabled create array of titles
               $enabled_payment[] = array('id' => $module->code, 'text' => $module->title) ;                
         } 
	  }
   }
 }
 return $enabled_payment ;
}
 // CONFIGURATION CACHES by Jason Chuh
 /*
  function writeConfiguration(&$var, $filename='configuration.cache') {
    $filename = DIR_FS_CATALOG.'cache/' . $filename;
    $success = false;

    if ($fp = @fopen($filename, 'w')) {
      flock($fp, 2); // LOCK_EX
      fputs($fp, serialize($var));
      flock($fp, 3); // LOCK_UN
      fclose($fp);
      $success = true;
    }

    return $success;
  }
  function updateConfiguration($filename='configuration.cache')
  {
    $result = array();
    $configuration_query = tep_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . $multi_stores_config);
    while ($configuration = tep_db_fetch_array($configuration_query)) {
        $result[] = array('key'=>$configuration['cfgKey'],'value'=> $configuration['cfgValue']);
    }
    tep_db_free_result($configuration_query);
    writeConfiguration($result,$filename);
  }
*/  
// create manual order order   
  function tep_get_address_format_id($country_id) {
    $address_format_query = tep_db_query("select address_format_id as format_id from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");
    if (tep_db_num_rows($address_format_query)) {
      $address_format = tep_db_fetch_array($address_format_query);
      return $address_format['format_id'];
    } else {
      return '1';
    }
  }
 function tep_reset_page_cache( $action = false ) {
    GLOBAL $multi_stores_config ;
    if ( $action == 'true' ) {
      $path_to_cache = DIR_FS_CACHE  ;
	  // realpath( dirname( __FILE__ ) . '/../../../' ) . '/temp/';
//      $path_to_cache = $usu5_path . 'cache_system/cache/';
          $it = new DirectoryIterator( $path_to_cache );
          while( $it->valid() ) {
            if ( !$it->isDot() && is_readable( $path_to_cache . $it->getFilename() )  ) {
//&& ( substr( $it->getFilename(), -6 ) == '.cache' )	
              if ( $it->getFilename() != '.htaccess' ) {  // do not delete htaccess file
                @unlink( $path_to_cache . $it->getFilename() );
			  }
            }
            $it->next();
          }

      tep_db_query( "UPDATE " . $multi_stores_config . " SET configuration_value='false' WHERE configuration_key='PAGE_CACHE_DELETE_FILES'" );
    } 
  } // end function  
 
// BOF kissit scaled images 
  function tep_remove_thumbnails_images( $action = true ) {
    GLOBAL $multi_stores_config ;
    if ( $action == 'true' ) {
      $path_to_cache = DIR_FS_CATALOG . 'includes/modules/kiss_image_thumbnailer/thumbs/' ;
	  // realpath( dirname( __FILE__ ) . '/../../../' ) . '/temp/';
//      $path_to_cache = $usu5_path . 'cache_system/cache/';
          $it = new DirectoryIterator( $path_to_cache );
          while( $it->valid() ) {
            if ( !$it->isDot() && is_readable( $path_to_cache . $it->getFilename() )  ) {
//&& ( substr( $it->getFilename(), -6 ) == '.cache' )	
              if ( $it->getFilename() != '.htaccess' ) {  // do not delete htaccess file
                @unlink( $path_to_cache . $it->getFilename() );
			  }
            }
            $it->next();
          }

      tep_db_query( "UPDATE " . $multi_stores_config . " SET configuration_value='false' WHERE configuration_key='SYS_REMOVE_CACHE_THUMBNAILS'" );
    } 
  } // end function  
// EOF kissit scaled images

/// Return the shipping ETA in the needed language
// TABLES: products_availability
  function tep_get_products_availability_name($products_availability_id, $language_id) {
    $products_availability_query = tep_db_query("select products_availability_name from " . TABLE_PRODUCTS_AVAILABILITY . " where products_availability_id = '" . (int)$products_availability_id . "' and language_id = '" . (int)$language_id . "'");
    $products_availability = tep_db_fetch_array($products_availability_query);

    return $products_availability['products_availability_name'];
  }

  function tep_cfg_pull_down_stock_status($availability_id, $key = '') {
    global $languages_id;  
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $availability_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $availability_query = tep_db_query("select products_availability_id, products_availability_name from " . TABLE_PRODUCTS_AVAILABILITY . " where language_id = '" . (int)$languages_id . "' order by products_availability_id"); //where language_id = '" . (int)$languages_id . "'
    while ($availability = tep_db_fetch_array($availability_query)) {
      $availability_array[] = array('id' => $availability['products_availability_id'],
                                 'text' => $availability['products_availability_name']);
    }

    return tep_draw_pull_down_menu($name, $availability_array, $availability_id);
  }
  function tep_get_stock_status_title($availability_id) {
    global $languages_id;    
    if ($availability_id == '0') {
      return TEXT_NONE;
    } else {
      $availability_query = tep_db_query("select products_availability_name from " . TABLE_PRODUCTS_AVAILABILITY . " where products_availability_id = '" . (int)$availability_id . "' and language_id = '" . (int)$languages_id . "'");
      $availability = tep_db_fetch_array($availability_query);

      return $availability['products_availability_name'];
    }
  }  
//++++ QT Pro: Begin Changed code
////
// Function to build menu of available class files given a file prefix
// Used for configuring plug-ins for product information attributes
  function tep_cfg_pull_down_class_files($prefix, $current_file) {
    $d=DIR_FS_CATALOG . DIR_WS_CLASSES;
    $function_directory = dir ($d);

    while (false !== ($function = $function_directory->read())) {
      if (preg_match('/^'.$prefix.'(.+)\.php$/',$function,$function_name)) {
          $file_list[]=array('id'=>$function_name[1], 'text'=>$function_name[1]);
      }
    }
    $function_directory->close();

    return tep_draw_pull_down_menu('configuration_value', $file_list, $current_file);
  }

require(DIR_WS_FUNCTIONS . 'qtpro_functions.php');  
//++++ QT Pro: End Changed Code  
////
/*** BOF: Additional Orders Info ***/
// Return orders shipping method
  function tep_get_orders_shipping_method($order_id) {
    $check_order_query= tep_db_query("select title from " . TABLE_ORDERS_TOTAL . " where orders_id='" . $order_id . "' and class='ot_shipping'");
    $check_order= tep_db_fetch_array($check_order_query);
    if (SHOW_INVOICE_SHIPPING=='2' and ($check_order['title']=='United Parcel Service' or $check_order['title']=='United States Postal Service')) {
      // return short version on UPS and USPS
      $short_shipping_end= strpos($check_order['title'], '');
      $short_shipping= substr($check_order['title'], 1, $short_shipping_end);
      return $short_shipping;
    } else {
      // return normal shipping
      return $check_order['title'];
    }
  }
  function GetNumber($text)
  {
    $numb = '';
    for ($i = 0; $i < strlen($text); ++$i)
    {
      if (is_numeric($text[$i]) || $text[$i] == '.')
       $numb .= $text[$i];
    }
    return $numb;
  }    
  
  function BillingShippingDontMatch($oID)
  {
    $shipping_query = tep_db_query("select * from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "' limit 1");
    $shipping = tep_db_fetch_array($shipping_query);
  
    if ($shipping['billing_street_address'] === $shipping['delivery_street_address'] &&
        $shipping['billing_city'] === $shipping['delivery_city'] &&
        $shipping['billing_postcode'] === $shipping['delivery_postcode'] &&
        $shipping['billing_state'] === $shipping['delivery_state'] )
          return false;
    return true;   
  }     
  
  function GetAdditionalOrderInfo($parts, $paymentMethod, $text = '')
  { 
    $message = '';
    for ($i = 0; $i < count($parts); ++$i)
    {
       if (trim(strtolower($parts[$i])) == strtolower(strip_tags($paymentMethod))) //payment method is being watched
       {
          if (tep_not_null($text)) 
            $message = '&nbsp;&nbsp;<span class="smallText">' . $text . '</span>';
          else  
            $message = SHOW_ORDERS_COLOR_PAYMENT;
          break;
       }  
    } 
    return $message;
  }  

  function GetNumberOfOrders($custID) {
    $orders_query = tep_db_query("select count(*) as total from " . TABLE_ORDERS . " where customers_id = " . (int)$custID);
    $orders = tep_db_fetch_array($orders_query);
    return $orders['total'];
  }  
  
  function GetOrderTotal($custID) {
    require(DIR_WS_CLASSES . 'currencies.php');
    $currencies = new currencies();
  
    $ttl = '';
    $orders_query= tep_db_query("select ot.value  from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on o.orders_id = ot.orders_id where o.customers_id = '" . (int)$custID . "' and ot.class = 'ot_total'");
    while ($orders= tep_db_fetch_array($orders_query)) {
      $ttl += $orders['value'];
    }
    
    $ttl = $currencies->format($ttl);
    return $ttl;
  }
  
  function GetProductsOnHand($product) {
    $prod_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id where pd.products_name = '" . tep_db_prepare_input($product['name']) . "' and p.products_model = '" . tep_db_prepare_input($product['model']) . "'");
    if (tep_db_num_rows($prod_query) > 0 ) {
      $prod = tep_db_fetch_array($prod_query);
      
      $stock_query = tep_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . (int)$prod['products_id'] . "'");
      $stock_values = tep_db_fetch_array($stock_query);

      return $stock_values['products_quantity'];
    }
    
    return '';
  }  
/*** EOF: Additional Orders Info ***/
// Sets the status of a popup window
  function tep_set_popup_status($popups_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_POPUPS . " set status = '1', date_status_change = now() where popups_id = '" . $popups_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_POPUPS . " set status = '0', date_status_change = now() where popups_id = '" . $popups_id . "'");
    } else {
      return -1;
    }
  }
  

  function tep_get_popups_text($popups_id, $language_id) {
    $pop_query = tep_db_query("select popups_html_text from " . TABLE_POPUPS_DESCRIPTION . " where popups_id = '" . (int)$popups_id . "' and language_id = '" . (int)$language_id . "'");
    $popup = tep_db_fetch_array($pop_query);

    return $popup['popups_html_text'];
  }
  
//rmh M-S_pricing end
////rmh M-S_multi-stores begin
// Sets the status of the store
  function tep_set_store_status($stores_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_STORES . " set stores_status = '1', last_modified = now() where stores_id = '" . (int)$stores_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_STORES . " set stores_status = '0', last_modified = now() where stores_id = '" . (int)$stores_id . "'");
    } else {
      return -1;
    }
  }


  function tep_get_stores() {
    global $admin_allowed_stores;

//    $stores_query = tep_db_query("select stores_id, stores_name, stores_config_table from " . TABLE_STORES . ($admin_allowed_stores[0] == '*' ? " " : " where stores_id in(" . implode(',' , $admin_allowed_stores) . ") ") . "order by stores_name");
    $stores_query = tep_db_query("select stores_id, stores_name, stores_config_table from " . TABLE_STORES . " order by stores_name");
    while ($stores = tep_db_fetch_array($stores_query)) {
      $stores_array[] = array('id' => $stores['stores_id'],
                              'text' => $stores['stores_name']);
    }

    return $stores_array;
  }

  function tep_add_category_to_store($category_id, $store_id) {
    tep_db_query("delete from " . TABLE_CATEGORIES_TO_STORES . " where categories_id = '" . (int)$category_id . "' AND stores_id = '" . (int)$store_id . "'");
    tep_db_query("insert into " . TABLE_CATEGORIES_TO_STORES . " (categories_id, stores_id) values ('" . (int)$category_id . "', '" . (int)$store_id . "')");
  }

  function tep_add_product_to_store($product_id, $store_id) {
    tep_db_query("delete from " . TABLE_PRODUCTS_TO_STORES . " where products_id = '" . (int)$product_id . "' AND stores_id = '" . (int)$store_id . "'");
    tep_db_query("insert into " . TABLE_PRODUCTS_TO_STORES . " (products_id, stores_id) values ('" . (int)$product_id . "', '" . (int)$store_id . "')");
  }

  function tep_table_exists($table_name) {
    $table = tep_db_query("show tables like '" . $table_name . "'");
    if ( !tep_not_null(tep_db_fetch_array($table)) ) {
      return(false);
    } else {
      return(true);
    }
  }

  function tep_get_allowed_categories() {
    global $admin_allowed_stores;

    $category_tree_array = array();
//    $categories_query = tep_db_query("select c.categories_id from " . TABLE_CATEGORIES . " c " . ($admin_allowed_stores[0] == '*' ? " " : " LEFT JOIN " . TABLE_CATEGORIES_TO_STORES . " c2s ON c.categories_id = c2s.categories_id where c2s.stores_id in(" . implode(',' , $admin_allowed_stores) . ") "));
    $categories_query = tep_db_query("select c.categories_id from " . TABLE_CATEGORIES . " c LEFT JOIN " . TABLE_CATEGORIES_TO_STORES . " c2s ON c.categories_id = c2s.categories_id");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $category_tree_array[] = $categories['categories_id'];
    }

    return $category_tree_array;
  }

  function tep_is_allowed_product($product_id) {
    global $admin_allowed_stores, $login_id;

    if ($admin_allowed_stores[0] == '*') { //a super-admin
      return true;
    }

    $product_query = tep_db_query("select distinct p.products_id from " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_TO_STORES . " p2s ON p.products_id = p2s.products_id where p.products_id = '" . (int)$product_id . "' and p2s.stores_id in(" . implode(',' , $admin_allowed_stores) . ") ");

     if (!tep_db_num_rows($product_query)) {
       return false;
     } else {
       return true;
     }
  }

  function tep_is_product_distributor($product_id) {
    global $admin_allowed_stores, $login_id;

    if ($login_id == '1') { //a super-admin
      return true;
    }

    $product_query = tep_db_query("select distinct p.products_id from " . TABLE_PRODUCTS . " p where p.products_id = '" . (int)$product_id . "' and p.distributors_id = '" . (int)tep_get_distributor_id($login_id) . "'");

     if (!tep_db_num_rows($product_query)) {
       return false;
     } else {
       return true;
     }
  }

  function tep_is_allowed_special($special_id = '') {
    global $admin_allowed_stores;

      if ($admin_allowed_stores[0] != '*') {
        $product_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_SPECIALS . " s ON p.products_id = s.products_id where s.specials_id = '" . (int)$special_id . "' and s.stores_id in(" . implode(',' , $admin_allowed_stores) . ")");
        $product = tep_db_fetch_array($product_query);
      }

    if (tep_not_null($product) || $admin_allowed_stores[0] == '*') {
      return true;
    } else {
      return false;
    }
  }

  function tep_get_store_name($store_id) {
    $store_query = tep_db_query("select stores_name from " . TABLE_STORES . " where stores_id = '" . (int)$store_id . "'");

    if (!tep_db_num_rows($store_query)) {
      return $store_id;
    } else {
      $store = tep_db_fetch_array($store_query);
      return $store['stores_name'];
    }
  }

  function tep_get_distributor_id($login_id) {
//    $distributor_query = tep_db_query("select administrators_distributors_id from " . TABLE_ADMINISTRATORS . " where administrators_id = '" . (int)$login_id . "'");

  //  if (!tep_db_num_rows($distributor_query)) {
      return '0';
    //} else {
      //$distributor = tep_db_fetch_array($distributor_query);
      //return $distributor['administrators_distributors_id'];
    //}
  }

  function tep_is_allowed_to_view_order($order_id) {
    global $admin_allowed_stores, $login_id;

      if ($admin_allowed_stores[0] == '*') { //a super-admin
        return true;
      }

     $order_query = tep_db_query("select distinct o.orders_id from " . TABLE_ORDERS . " o inner join " . TABLE_ORDERS_PRODUCTS . " op on (o.orders_id = op.orders_id) where o.orders_id = '" . (int)$order_id . "' and ( (o.orders_stores_id in(" . implode(',' , $admin_allowed_stores) . ")) OR (op.products_distributors_id = '" . tep_get_distributor_id($login_id) . "') ) group by o.orders_id order by o.orders_id DESC");
     if (!tep_db_num_rows($order_query)) {
       return false;
     } else {
       return true;
     }
  }

  function tep_language_enabled_for_store($languages_id, $stores_id) {
     $lang_query = tep_db_query("select l2s.languages_id from " . TABLE_LANGUAGES_TO_STORES . " l2s where l2s.languages_id = '" . (int)$languages_id . "' and l2s.stores_id = '" . (int)$stores_id . "'");
     if (!tep_db_num_rows($lang_query)) {
       return false;
     } else {
       return true;
     }
  }

  function tep_packingslip_products($order_products_id) {
    global $admin_allowed_stores, $login_id;

      if ($admin_allowed_stores[0] == '*') { //a super-admin
        return true;
      }
      $order_query = tep_db_query("select o.orders_id from " . TABLE_ORDERS . " o inner join " . TABLE_ORDERS_PRODUCTS . " op on (o.orders_id = op.orders_id) where op.orders_products_id = '" . (int)$order_products_id . "' and ( (op.products_distributors_id = '" . tep_get_distributor_id($login_id) . "') ) group by op.orders_products_id order by op.orders_products_id DESC");
      if (!tep_db_num_rows($order_query)) {
        return false;
      } else {
        return true;
      }
  }

  function tep_is_allowed_insert_product($category_id) {
    global $login_id;

      if ($login_id == '1') return true;

      $category_query = tep_db_query("select c.categories_id from " . TABLE_CATEGORIES . " c left join " . TABLE_ADMINISTRATORS . " a on (c.distributors_id = a.administrators_distributors_id) where c.categories_id = '" . (int)$category_id . "' and a.administrators_id = '" . (int)$login_id . "' group by c.categories_id");
      if (!tep_db_num_rows($category_query)) {
        return false;
      } else {
        return true;
      }
  }
  function osc_realpath($directory = '../' ) {
    return str_replace('\\', '/', realpath($directory))  ;
  }  
//rmh M-S_multi-stores end  
// BOF DEFAULT_SHIPPING_METHOD	
	function tep_get_available_shipping_method () {
	global $PHP_SELF, $language;
    $module_type = 'shipping';
    $module_directory = DIR_FS_CATALOG_MODULES . 'shipping/';
    $module_key = 'MODULE_SHIPPING_INSTALLED';
		$file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
  	$directory_array = array();
  	if ($dir = @dir($module_directory)) {
    	while ($file = $dir->read()) {
      	if (!is_dir($module_directory . $file)) {
       		if (substr($file, strrpos($file, '.')) == $file_extension) {
          	$directory_array[] = $file;
        	}
      	}
    	}
    	sort($directory_array);
    	$dir->close();
  	}
		
		$installed_modules = array(array('id' => 'false', 'text' => 'false')); 
		for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
			$file = $directory_array[$i];
	
			include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/' . $module_type . '/' . $file);
			include($module_directory . $file);
			
			$class = substr($file, 0, strrpos($file, '.'));
			if (tep_class_exists($class)) {
      	$module = new $class;
      	if ($module->check() > 0) {
          $installed_modules[] = array('id' => $module->code,
                                				'text' => $module->title);        	
      	}
			}
		
		}
	return $installed_modules;
	}
																
  function tep_cfg_pull_down_available_shipping_method() {
	  
    return tep_draw_pull_down_menu('configuration_value', tep_get_available_shipping_method (), DEFAULT_SHIPPING_METHOD);
  }
// EOF DEFAULT_SHIPPING_METHOD
// BOF multi stores
  function tep_multi_stores_images( $image_name, $active_stores_images, $main_catalog_images = '', $dir_source = '', $dir_target = '' ) {
// multi stores upload or delete the category image from the different stores
         if ( !tep_not_null( $image_name ) ) return false ;
         $stores_query_img_prod = tep_db_query("select stores_id, stores_name, stores_image, stores_url, stores_absolute, stores_config_table, stores_status from " . TABLE_STORES . "  order by stores_id");
		 $image_copy = new GetImage;
		 $image_copy->source = $main_catalog_images . $dir_source . $image_name ;
		 
		 while ($stores_image_product = tep_db_fetch_array($stores_query_img_prod)) {
			 if ( $stores_image_product[ 'stores_id'] != 1 ) { // only sub stores
// get config table plus value of the images directory
			   $config_table_img_prod = $stores_image_product[ 'stores_config_table' ] ; 
               $get_config_image_directory_query = tep_db_query("select configuration_value from " . $config_table_img_prod . " where configuration_key = 'DIR_FS_CATALOG_IMAGES'"); // get location images
		       $config_image_directory_query_prod= tep_db_fetch_array($get_config_image_directory_query); 				  

               if ( $active_stores_images  ) { // if any of the checkboxes are checked
                  foreach( $active_stores_images  as $val) {		              				  
			          if ( $val == $stores_image_product['stores_id' ] ) {  // save image to store location
                         $image_copy->save_to = $config_image_directory_query_prod[ 'configuration_value' ] . $dir_target   ; // with trailing slash at the end save the images to this store				  
                         $get = $image_copy->download('gd'); // using GD			  
			          }  
                  } // end foreach
			   } else { // remove images from dir store is not active
			      if (file_exists($config_image_directory_query_prod[ 'configuration_value' ] . $dir_target . $image_name )) {
                      @unlink( $config_image_directory_query_prod[ 'configuration_value' ] . $dir_target .  $image_name ) ;
				   }
               } // end if ( $active_stores_images  ) 			  
	         } // end if ( $stores_image_product[ 'stores_id'] !=
		 } // end while ($stores_image_product = tep_d
   }

// Sets the status of a category and all nested categories and products whithin.
  function tep_set_categories_to_stores($category_id, $cat_to_stores) {
     // tep_db_query("update " . TABLE_CATEGORIES . " set categories_to_stores = $cat_to_stores, last_modified = now() where categories_id = '" . $category_id . "'");
      $tree = tep_get_category_tree($category_id);
      for ($i=1; $i<sizeof($tree); $i++) {
        tep_db_query("update " . TABLE_CATEGORIES . " set categories_to_stores = '$cat_to_stores', last_modified = now() where categories_id = '" . $tree[$i]['id'] . "'");
      }
  }   
// EOF multi stores
// bof validate cpath
// cPath update cfg function

function tep_cfg_update_cpath( $action = 'false' ) {
   if ($action == 'update') {
     $cPath_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . "");
     while ($cP = tep_db_fetch_array($cPath_query)) {
       tep_update_cpath($cP['categories_id']);
     }
   }
 }
////

// cPath parameter save to table
function tep_update_cpath($categories_id) {
   $categories = array();
   $categories[] = $categories_id;
   tep_get_parent_categories($categories, $categories_id);
   $categories = array_reverse($categories);
   $new_cPath = implode('_', $categories);
   tep_db_query("update " . TABLE_CATEGORIES . " set cpath = '" . $new_cPath . "' where categories_id = " . (int)$categories_id . "");
 }

////

// Recursively go through the categories and retreive all parent categories IDs
// TABLES: categories

function tep_get_parent_categories(&$categories, $categories_id) {
  $parent_categories_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$categories_id . "'");
  while ($parent_categories = tep_db_fetch_array($parent_categories_query)) {
    if ($parent_categories['parent_id'] == 0) return true;
      $categories[sizeof($categories)] = $parent_categories['parent_id'];
      if ($parent_categories['parent_id'] != $categories_id) {
        tep_get_parent_categories($categories, $parent_categories['parent_id']);
      }
   }
}
// eof validate cpath

  function tep_get_location_map_text($locationmap_id, $language_id) {
    $location_query = tep_db_query("select location_text_map from " . TABLE_LOCATIONMAP_INFO . " where locationmap_id = '" . (int)$locationmap_id . "' and languages_id = '" . (int)$language_id . "'");
    $location = tep_db_fetch_array($location_query);

    return $location['location_text_map'];
  }
  function tep_get_location_map_text_marker($locationmap_id, $language_id) {
    $location_query = tep_db_query("select location_text_marker from " . TABLE_LOCATIONMAP_INFO . " where locationmap_id = '" . (int)$locationmap_id . "' and languages_id = '" . (int)$language_id . "'");
    $location = tep_db_fetch_array($location_query);

    return $location['location_text_marker'];
  }  
 // eof location map
// theme switcher  bootstrap css 
// Get a list of the files or directories in a directory
  if( !function_exists( 'tep_get_directory_list' ) ) {
    function tep_get_directory_list( $directory, $file=true, $exclude=array() ) {
      $d = dir( $directory );
      $list = array();
      while( $entry = $d->read() ) {
        if( $file == true ) { // We want a list of files, not directories
          $parts_array = explode( '.', $entry );
          $extension = $parts_array[1];
          // Don't add files or directories that we don't want
          if( $entry != '.' && $entry != '..' && $entry != '.htaccess' && $extension != 'php' ) {
            if( !is_dir( $directory . "/" . $entry ) ) {
              $list[] = $entry;
            }
          }
        } else { // We want the directories and not the files
          if( is_dir( $directory . "/" . $entry ) && $entry != '.' && $entry != '..' ) {  // && $entry != 'i18n'
            if( count( $exclude ) == 0 || !in_array ( $entry, $exclude ) ) {
              $list[] = array( 'id' => $entry,
                               'text' => $entry
                             );
            }
          }
        }
      }
      $d->close();
      return $list;
    }
  }

////
// Generate a pulldown menu of the available themes
  function tep_cfg_pull_down_themes( $theme_name, $key = '' ) {
  	$themes_array = array();
    $theme_directory = DIR_FS_CATALOG . 'ext/bootstrap/css';

    if( file_exists( $theme_directory ) && is_dir( $theme_directory ) ) {
      $name = ( ( $key ) ? 'configuration[' . $key . ']' : 'configuration_value' );

      $exclude = array( 'i18n' );
      $themes_array = tep_get_directory_list( $theme_directory, false, $exclude );
    }

    return tep_draw_pull_down_menu( $name, $themes_array, $theme_name );
  }
  
 function tep_return_themes( ) {
  	$themes_array = array();
    $theme_directory = DIR_FS_CATALOG . 'ext/bootstrap/css';

    if( file_exists( $theme_directory ) && is_dir( $theme_directory ) ) {
      $exclude = array( 'i18n' );
      $themes_array = tep_get_directory_list( $theme_directory, false, $exclude );
    }

    return $themes_array ;
  }  
// theme switcher  bootstrap css

// Decode string encoded with htmlspecialchars()
  function tep_decode_specialchars($string){
    $string=str_replace('&gt;', '>', $string);
    $string=str_replace('&lt;', '<', $string);
    $string=str_replace('&#039;', "'", $string);
    $string=str_replace('&quot;', "\"", $string);
    $string=str_replace('&amp;', '&', $string);
    $string=str_replace('â,¬', '€', $string);	
    $string=str_replace('?', '€', $string);
    $string=str_replace('â‚¬', '€', $string);	
    return $string;
  }
  
  function tep_get_language_name( $language_id = null ) {
    $languages_query = tep_db_query("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " where languages_id = '" . $language_id . "'");
    $languages = tep_db_fetch_array($languages_query) ;
    return $languages['name'] ;
  }
   
?>