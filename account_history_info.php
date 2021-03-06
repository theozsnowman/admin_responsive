<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  // bof images products
//  define(ORDERS_IMAGE_HEIGHT, 50);
  define(ORDERS_IMAGE_WIDTH, 50);
// eof images products  

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  if (!isset($HTTP_GET_VARS['order_id']) || (isset($HTTP_GET_VARS['order_id']) && !is_numeric($HTTP_GET_VARS['order_id']))) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }
  
  $customer_info_query = tep_db_query("select o.customers_id from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " s where o.orders_id = '". (int)$HTTP_GET_VARS['order_id'] . "' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and s.public_flag = '1'");
  $customer_info = tep_db_fetch_array($customer_info_query);
  if ($customer_info['customers_id'] != $customer_id) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_HISTORY_INFO);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  $breadcrumb->add(sprintf(NAVBAR_TITLE_3, $HTTP_GET_VARS['order_id']), tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $HTTP_GET_VARS['order_id'], 'SSL'));

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order($HTTP_GET_VARS['order_id']);

  require(DIR_WS_INCLUDES . 'template_top.php');
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<div class="contentContainer">
  <h2><?php echo sprintf(HEADING_ORDER_NUMBER, $HTTP_GET_VARS['order_id']) . ' <span class="contentText">(' . $order->info['orders_status'] . ')</span>'; ?></h2>

  <div class="contentText">
 

    <div class="panel panel-default">
      <div class="panel-heading"><strong><?php echo sprintf(HEADING_ORDER_NUMBER, $HTTP_GET_VARS['order_id']) . ' <span class="badge pull-right">' . $order->info['orders_status'] . '</span>'; ?></strong></div>
      <div class="panel-body">

        <table border="0" width="100%" cellspacing="0" cellpadding="2" class="table-hover order_confirmation">
<?php
  if (sizeof($order->info['tax_groups']) > 1) {
?>
          <tr>
            <td colspan="2"><strong><?php echo HEADING_PRODUCTS; ?></strong></td>
            <td align="right"><strong><?php echo HEADING_TAX; ?></strong></td>
            <td align="right"><strong><?php echo HEADING_TOTAL; ?></strong></td>
          </tr>
<?php
  } else {
?>
          <tr>
            <td colspan="2"><strong><?php echo HEADING_PRODUCTS; ?></strong></td>
            <td align="right"><strong><?php echo HEADING_TOTAL; ?></strong></td>
          </tr>
<?php
  }

// bof images products
//  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
//    echo '          <tr>' . "\n" .
//         '            <td align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '&nbsp;x&nbsp;</td>' . "\n" .
//         '            <td valign="top">' . $order->products[$i]['name'];
  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {

    $orders_products_pic_query = tep_db_query("select products_image, products_model from " . TABLE_PRODUCTS . " where products_id = '" . (int)$order->products[$i]['id'] . "'");
    $orders_products_pic = tep_db_fetch_array($orders_products_pic_query);

	echo '<tr>' ;
	echo '<td>' ;
    echo '<button class="btn" data-toggle="modal" data-target="#myModal">' .
               tep_image(DIR_WS_IMAGES . $orders_products_pic['products_image'], addslashes($orders_products_pic['products_model']), ORDERS_IMAGE_WIDTH, ORDERS_IMAGE_WIDTH, 'itemprop="image"') .
         '</button>

         <!-- Modal -->
         <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
           <div class="modal-dialog">
              <div class="modal-content">
                 <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">' . $orders_products_pic['products_model'] . '</h4>
                 </div>
                 <div class="modal-body">' . 
	               tep_image(DIR_WS_IMAGES . $orders_products_pic['products_image'], addslashes($orders_products_pic['products_model']), PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT, 'itemprop="image"') .        
                '</div>
                 <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
                 </div>
              </div>
            </div>
           </div>' ;

    echo ' <td valign="top" align="right" width="30">' . $order->products[$i]['qty'] . '&nbsp;x&nbsp;</td>' . "\n" .
         ' <td valign="top">' . $order->products[$i]['name'] . PHP_EOL;
// eof products images

    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br /><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>';
      }
    }

    echo '</td>' . PHP_EOL;

    if (sizeof($order->info['tax_groups']) > 1) {
      echo '            <td valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";
    }

    echo '            <td align="right" valign="top">' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table>
        <hr>
        <table width="100%" class="pull-right">
<?php
  for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
    echo '          <tr>' . "\n" .
         '            <td align="right" width="100%">' . $order->totals[$i]['title'] . '&nbsp;</td>' . "\n" .
         '            <td align="right">' . $order->totals[$i]['text'] . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table>
      </div>



      <div class="panel-footer">
        <span class="pull-right hidden-xs"><?php echo HEADING_ORDER_TOTAL . ' ' . $order->info['total']; ?></span><?php echo HEADING_ORDER_DATE . ' ' . tep_date_long($order->info['date_purchased']); ?>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>

  <div class="row">
    <?php
    if ($order->delivery != false) {
      ?>
      <div class="col-sm-4">
        <div class="panel panel-info">
          <div class="panel-heading"><?php echo '<strong>' . HEADING_DELIVERY_ADDRESS . '</strong>'; ?></div>
          <div class="panel-body">
            <?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'); ?>
          </div>
        </div>
      </div>
      <?php
    }
    ?>
    <div class="col-sm-4">
      <div class="panel panel-warning">
        <div class="panel-heading"><?php echo '<strong>' . HEADING_BILLING_ADDRESS . '</strong>'; ?></div>
        <div class="panel-body">
          <?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'); ?>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <?php
      if ($order->info['shipping_method']) {
        ?>
        <div class="panel panel-info">
          <div class="panel-heading"><?php echo '<strong>' . HEADING_SHIPPING_METHOD . '</strong>'; ?></div>
          <div class="panel-body">
            <?php echo $order->info['shipping_method']; ?>
          </div>
        </div>
        <?php
      }
      ?>
      <div class="panel panel-warning">
        <div class="panel-heading"><?php echo '<strong>' . HEADING_PAYMENT_METHOD . '</strong>'; ?></div>
        <div class="panel-body">
          <?php echo $order->info['payment_method']; ?>
        </div>
      </div>
    </div>


  </div>
<!-- start pdf //-->
  <div class="panel panel-info">
      <div class="panel-heading"><?php echo '<strong>' . PDF_INVOICE . '</strong>'; ?></div>
      <div class="panel-body">
      <?php echo 
		tep_draw_button(PDF_DOWNLOAD_LINK,      'zoom-in',            tep_href_link( FILENAME_CUSTOMER_PDF,        'order_id=' . $HTTP_GET_VARS['order_id'] , 'SSL'), null, array('newwindow' => true)) ; 
	  ?>
    </div>
  </div>
<!-- end pdf //-->  

  <hr>

  <h2><?php echo HEADING_ORDER_HISTORY; ?></h2>

  <div class="clearfix"></div>
  
  <div class="contentText">
    <ul class="timeline">
      <?php
      $statuses_query = tep_db_query("select os.orders_status_name, osh.date_added, osh.comments from " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh where osh.orders_id = '" . (int)$HTTP_GET_VARS['order_id'] . "' and osh.orders_status_id = os.orders_status_id and os.language_id = '" . (int)$languages_id . "' and os.public_flag = '1' order by osh.date_added");
      while ($statuses = tep_db_fetch_array($statuses_query)) {
        echo '<li>';
        echo '  <div class="timeline-badge"><i class="' . glyphicon_icon_to_fontawesome( "check" ) . '"></i></div>'; //glyphicon glyphicon-check
        echo '  <div class="timeline-panel">';
        echo '    <div class="timeline-heading">';
        echo '      <p class="pull-right"><small class="text-muted"><i class="' . glyphicon_icon_to_fontawesome( "time" ) . '"></i> ' . tep_date_short($statuses['date_added']) . '</small></p><h2 class="timeline-title">' . $statuses['orders_status_name'] . '</h2>'; //glyphicon glyphicon-time
        echo '    </div>';
        echo '    <div class="timeline-body">';
        echo '      <p>' . (empty($statuses['comments']) ? '&nbsp;' : '<blockquote>' . tep_output_string_protected($statuses['comments']) . '</blockquote>') . '</p>'; // nl2br
        echo '    </div>';
        echo '  </div>';
        echo '</li>';
      }
      ?>
    </ul>
  </div>
<?php
  if (DOWNLOAD_ENABLED == 'true') include(DIR_WS_MODULES . 'downloads.php');
?>

  <div class="clearfix"></div>
  <div class="buttonSet">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'chevron-left', tep_href_link(FILENAME_ACCOUNT_HISTORY, tep_get_all_get_params(array('order_id')), 'SSL')); ?>
  </div>
</div>

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>