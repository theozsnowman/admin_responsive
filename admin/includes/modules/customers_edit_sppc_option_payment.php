<?php
	  $contents_tab_sppc_01 .= '<div class="panel panel-primary">'; 
	  $contents_tab_sppc_01 .= '<div class="panel-body">'; 	  
	  $contents_tab_sppc_01 .= '<br />'; 
 	  $contents_tab_sppc_01 .= '   <div class="form-group">' . PHP_EOL ;
 	  $contents_tab_sppc_01 .= '      <div class="radio radio-success radio-inline">' . PHP_EOL ;	  
      $contents_tab_sppc_01 .=  			   tep_bs_radio_field('customers_payment_settings', '1', ENTRY_CUSTOMERS_PAYMENT_SET,     'input_Cust_payment_Sett',   (tep_not_null($cInfo->customers_payment_allowed)? true : false ), 'radio radio-success radio-inline', '', '', 'right') ;
      $contents_tab_sppc_01 .=  			   tep_bs_radio_field('customers_payment_settings', '0', ENTRY_CUSTOMERS_PAYMENT_DEFAULT, 'input_Cust_payment_Sett_2', (tep_not_null($cInfo->customers_payment_allowed)? false : true ), 'radio radio-success radio-inline', '', '', 'right') ;
      $contents_tab_sppc_01 .= '      </div>'. PHP_EOL  ;	
      $contents_tab_sppc_01 .= '   </div>'. PHP_EOL  ;	  
	  $contents_tab_sppc_01 .= '<br />';	
	  
 	  $contents_tab_sppc_01 .= '   <div class="form-group">' . PHP_EOL ;
	  
      $payments_allowed = explode (";",$cInfo->customers_payment_allowed);
      $module_active = explode (";",MODULE_PAYMENT_INSTALLED);
      $installed_modules = array();
      for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) {
         $file = $directory_array[$i];
         if (in_array ($directory_array[$i], $module_active)) {
            include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $file);
            include($module_directory . $file);

            $class = substr($file, 0, strrpos($file, '.'));
            if (tep_class_exists($class)) {
               $module = new $class;
               if ($module->check() > 0) {
                  $installed_modules[] = $file;
               }
            } // end if (tep_class_exists($class))

 
 	        $contents_tab_sppc_01 .= '<div class="form-group">' . PHP_EOL ;			
 	        $contents_tab_sppc_01 .= '  <div class="checkbox checkbox-success">' . PHP_EOL ;				
			$contents_tab_sppc_01 .=       tep_bs_checkbox_field('payment_allowed[' . $i . ']', $module->code.'.php', $module->title, $module->code.'.pay', ((in_array ($module->code.".php", $payments_allowed)) ?  true : false), 'checkbox checkbox-success', '', '', 'right') ;
			$contents_tab_sppc_01 .= '  </div>' . PHP_EOL ;			
			$contents_tab_sppc_01 .= '</div>' . PHP_EOL ;				
			$contents_tab_sppc_01 .= '<br />';			

//  echo tep_draw_checkbox_field('payment_allowed[' . $i . ']', $module->code.".php" , (in_array ($module->code.".php", $payments_allowed)) ?  1 : 0); &#160;&#160;<?php echo $module->title; 

         } // end if (in_array ($directory_array[$i], $module_active))
      } // end for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++)	  
	  
  
      $contents_tab_sppc_01 .= '   </div>'. PHP_EOL  ;	
	  $contents_tab_sppc_01 .= '<br />';	  
	  
      $contents_tab_sppc_01 .= '<div class="well mark">' . ENTRY_CUSTOMERS_PAYMENT_SET_EXPLAIN . '</div>'. PHP_EOL  ;	
	  $contents_tab_sppc_01 .= '<br />';	  
	  $contents_tab_sppc_01 .= '</div>'; 
	  $contents_tab_sppc_01 .= '</div>';	  
?>