<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License

  Discount Code 3.6 BS
  http://high-quality-php-coding.com/
  
  Version 3.6 BS: another calculation fix (April 2016) / ...and once more THANK YOU raiwa!
  Version 3.5 BS: bracket fix by raiwa (April 2016) / and once more THANK YOU raiwa!
  Version 3.4 BS: calculation fix by raiwa (April 2016) / again THANK YOU raiwa!
  Version 3.3 BS: Tax calculation and larger discount than subtotal bug fix by raiwa (April 2016) / THANK YOU raiwa!
  Version 3.2 BS: Updated for osC 2.3.4 BS by Tsimi (April 2016)
*/

  class ot_discount {
    var $title, $output;

    function ot_discount() {
      $this->code = 'ot_discount';
	  $this->version = '3.6 BS';
      $this->title = MODULE_ORDER_TOTAL_DISCOUNT_TITLE;
      $this->description = MODULE_ORDER_TOTAL_DISCOUNT_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_DISCOUNT_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies, $currency, $customer_id, $discount, $sess_discount_code;
      $discount = 0;

      if (!empty($sess_discount_code)) {
        $check_query = tep_db_query("select count(*) as total, dc.number_of_use from discount_codes dc, customers_to_discount_codes c2dc where dc.discount_codes_id = c2dc.discount_codes_id and dc.discount_codes = '" . tep_db_input($sess_discount_code) . "' and c2dc.customers_id = '" . (int)$customer_id . "' group by c2dc.customers_id limit 1");
        if (tep_db_num_rows($check_query) == 0) {
          $check['number_of_use'] = 0;
        } else {
          $check = tep_db_fetch_array($check_query);
        }
        if (($check['number_of_use'] == 0 ? 1 : ($check['total'] < $check['number_of_use'] ? 1 : 0)) == 1) {
          $check_query = tep_db_query("select dc.products_id, dc.categories_id, dc.manufacturers_id, dc.excluded_products_id, dc.customers_id, dc.orders_total, dc.order_info, dc.exclude_specials, dc.discount_values, dc.number_of_products from discount_codes dc where dc.discount_codes = '" . tep_db_input($sess_discount_code) . "' and if(dc.expires_date = '0000-00-00', date_format(date_add(now(), interval 1 day), '%Y-%m-%d'), dc.expires_date) >= date_format(now(), '%Y-%m-%d') and dc.minimum_order_amount <= " . $order->info['subtotal'] . " and dc.status = '1' limit 1");
          if (tep_db_num_rows($check_query)) {
            $check = tep_db_fetch_array($check_query);
            $order_info = $check['order_info'];

            if (!empty($check['customers_id'])) {
              $customers = explode(',', $check['customers_id']);
            } else {
              $customers = array($customer_id);
            }

            if (in_array($customer_id, $customers)) {
              if (!empty($check['products_id']) || !empty($check['categories_id']) || !empty($check['manufacturers_id'])) {

                $products = array();
                if (!empty($check['products_id'])) {
                  $products = explode(',', $check['products_id']);
                } elseif (!empty($check['categories_id'])) {
                  $product_query = tep_db_query("select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id in (" . $check['categories_id'] . ")" . (empty($check['excluded_products_id']) ? '' : " and products_id not in (" . $check['excluded_products_id'] . ")"));
                  while ($product = tep_db_fetch_array($product_query)) {
                    $products[] = $product['products_id'];
                  }
                } elseif (!empty($check['manufacturers_id'])) {
                  $product_query = tep_db_query("select products_id from " . TABLE_PRODUCTS . " where manufacturers_id in (" . $check['manufacturers_id'] . ")" . (empty($check['excluded_products_id']) ? '' : " and products_id not in (" . $check['excluded_products_id'] . ")"));
                  while ($product = tep_db_fetch_array($product_query)) {
                    $products[] = $product['products_id'];
                  }
                }

                if ((int)$check['exclude_specials'] == 1) {
                  $specials = array();
                  $product_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s where p.products_id = s.products_id and s.status = '1' and ifnull(s.expires_date, now()) >= now()");
                  while ($product = tep_db_fetch_array($product_query)) {
                    $specials[] = $product['products_id'];
                  }
                  if (sizeof($specials) > 0) {
                    $products = array_diff($products, $specials);
                  }
                }

                if (empty($check['number_of_products'])) {
                  $k = PHP_INT_MAX;
                } else {
                  $k = $check['number_of_products'];
                }

                for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
                  if (in_array(tep_get_prid($order->products[$i]['id']), $products)) {
                    if ($k >= $order->products[$i]['qty']) {
                      $products_discount = $this->format_raw(strpos($check['discount_values'], '%') === false ? $check['discount_values'] * $order->products[$i]['qty'] : tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * str_replace('%', '', $check['discount_values']) / 100 * $order->products[$i]['qty']);
                      $k -= $order->products[$i]['qty'];
                    } else {
                      $products_discount = $this->format_raw(strpos($check['discount_values'], '%') === false ? $check['discount_values'] * $k : tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * str_replace('%', '', $check['discount_values']) / 100 * $k);
                      $k = 0;
                    }

                    if (!empty($order->products[$i]['tax'])) {
                    	if (DISPLAY_PRICE_WITH_TAX != 'true') {
                    		$tax_correction = $this->format_raw(($products_discount * ($order->products[$i]['tax'] / 100)));
                    		$order->info['total'] -= $tax_correction;
                    	} else {
                    		$tax_correction = $this->format_raw($products_discount - $products_discount / (1.0 + $order->products[$i]['tax'] / 100));
                    	}
					}
                    $subtotal_correction += $order->products[$i]['price']; //use for tax calculation only products which have taxes
                    $order->info['tax'] -= $tax_correction;
                    $order->info['tax_groups'][$order->products[$i]['tax_description']] -= $tax_correction;
                    $discount += $products_discount;
                    
                  }
                }

                $order->info['total'] -= $discount;

              } elseif (!empty($check['orders_total'])) {
                if ($check['orders_total'] == 2) {
                  $discount = (strpos($check['discount_values'], '%') === false ? $check['discount_values'] : $order->info['subtotal'] * str_replace('%', '', $check['discount_values']) / 100);
                  if ($discount > $order->info['subtotal']) {
                  	$discount = $order->info['subtotal'];
                  }
                	$order_tax = 0;
                	for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
                    if (!empty($order->products[$i]['tax'])) {
                    	//here it gets complicate, we have to find the proportional part of the global discount for each product
                      $global_tax_correction = $this->format_raw($order->products[$i]['qty']*(($order->products[$i]['price']/$order->info['subtotal'])*$discount)+(($order->products[$i]['qty']*$order->products[$i]['price']/$order->info['subtotal'])*$discount) * ($order->products[$i]['tax'] / 100));
                      $order->info['total'] -= $global_tax_correction;
                    }
                  }
                  
                      if (is_array($order->info['tax_groups']) && count($order->info['tax_groups']) > 0) {
                        foreach ($order->info['tax_groups'] as $key => $value) {
                          if (!empty($value)) {
                            $order->info['tax_groups'][$key] = $this->format_raw(($order->info['subtotal'] - $discount) * ($value / $order->info['subtotal']));
                            $order_tax += $order->info['tax_groups'][$key];
                          }
                        }
                      }
                      if (!empty($order_tax)) {
                        $order->info['tax'] = $order_tax;
                      } else {
                      	$order->info['total'] -= $discount;
                      }
                    }
                  }
                }
              }
            }
          }

      if (!empty($discount)) {
        $this->output[] = array('title' =>'<span class="alert-success">' . TEXT_DISCOUNT . '</span>' . (!empty($order_info) ? ' (' . $sess_discount_code . ')' : '') . ':',
                                'text' => '<span class="alert-success">-' . $currencies->format($discount, true, $order->info['currency'], $order->info['currency_value']) . '</span>',
                                'value' => -$discount);
      }
    }

    function check() {
	  global $multi_stores_config;		
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . $multi_stores_config . " where configuration_key = 'MODULE_ORDER_TOTAL_DISCOUNT_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_DISCOUNT_VERSION', 'MODULE_ORDER_TOTAL_DISCOUNT_STATUS', 'MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER');
    }

    function install() {
	  global $multi_stores_config;			
	  tep_db_query("insert into " . $multi_stores_config  . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ( 'Module Version', 'MODULE_ORDER_TOTAL_DISCOUNT_VERSION', '" . $this->version . "', 'The version of this module that you are running', '6', '0', 'tep_version_readonly(', now())");
      tep_db_query("insert into " . $multi_stores_config . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Discount', 'MODULE_ORDER_TOTAL_DISCOUNT_STATUS', 'true', 'Do you want to display the discount value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . $multi_stores_config . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER', '2', 'Sort order of display.', '6', '2', now())");

   }

    function remove() {
	  global $multi_stores_config;			
      tep_db_query("delete from " . $multi_stores_config . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function format_raw($number, $currency_code = '', $currency_value = '') {
      global $currencies, $currency;

      if (empty($currency_code) || !$currencies->is_set($currency_code)) {
        $currency_code = $currency;
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $currencies->currencies[$currency_code]['value'];
      }

      return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '.', '');
    }
  }
  
  ////
// Function for version read out
  if(!function_exists('tep_version_readonly')) {
        function tep_version_readonly($value){
          $version_text = $value;
          return $version_text;
        }
  } 
?>