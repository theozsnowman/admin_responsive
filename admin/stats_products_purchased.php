<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_INCLUDES . 'template_top.php');

  $resetPurchased = (isset($HTTP_GET_VARS['resetPurchased']) ? tep_db_prepare_input($HTTP_GET_VARS['resetPurchased']) : '');
  if (tep_not_null($resetPurchased)) {
    if ($resetPurchased == '*' ) {
       // Reset ALL counts
       tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = 0 where 1");
    } else {
       // Reset selected product count
       tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = 0 where products_id = '" . (int)$resetPurchased . "'");
    }
  }

  // remember sort order when switching pages
  if (isset($HTTP_GET_VARS['purchasedSortOrder']))
  {
    if ( ! tep_session_is_registered('purchasedSortOrder') ) tep_session_register('purchasedSortOrder');
    $purchasedSortOrder = $HTTP_GET_VARS['purchasedSortOrder'];
  }

  if (isset($HTTP_GET_VARS['page']))
  {
    if ( ! tep_session_is_registered('page') ) tep_session_register('page');
    $page = $HTTP_GET_VARS['page'];
  }

  if(!isset($page)) $page = 1;

  switch ($purchasedSortOrder)
 {
    case "name-asc":
      $sortOrderPurchased = "pd.products_name ASC, p.products_ordered DESC";
      break;
    case "name-desc":
      $sortOrderPurchased = "pd.products_name DESC, p.products_ordered DESC";
      break;
    case "purchased-asc":
      $sortOrderPurchased = "p.products_ordered ASC, pd.products_name ASC";
      break;
    case "purchased-desc":
      $sortOrderPurchased = "p.products_ordered DESC, pd.products_name ASC";
      break;
    default:
      $sortOrderPurchased = "p.products_ordered DESC, pd.products_name ASC";
  }
?>

   <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <div class="page-header">
            <h1 class="col-xs-12 col-md-6"><?php echo HEADING_TITLE; ?></h1> 
            <div class="clearfix"></div>
          </div><!-- page-header-->
          <div class="table-responsive">
            <table class="table table-condensed table-striped">
              <thead>
                <tr class="heading-row">
                   <th>                    <?php echo TABLE_HEADING_NUMBER; ?></th>
                   <th class="text-left">  <?php echo '<a href="' . tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, 'purchasedSortOrder=name-asc&page=' . $page, 'NONSSL') . '">' . tep_glyphicon('sort-by-alphabet' ) . '</a>'; ?>&nbsp;<?php echo TABLE_HEADING_PRODUCTS; ?>&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, 'purchasedSortOrder=name-desc&page=' . $page, 'NONSSL') . '">' . tep_glyphicon('sort-by-alphabet-alt' ) .  '</a>'; ?></td>
                   <th class="text-center"><?php echo '<a href="' . tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, 'purchasedSortOrder=purchased-desc&page=' . $page, 'NONSSL') . '">' . tep_glyphicon('sort-numeric-desc' ) . '</a>'; ?>&nbsp;<?php echo TABLE_HEADING_PURCHASED; ?>&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, 'purchasedSortOrder=purchased-asc&page=' . $page, 'NONSSL'). '">' . tep_glyphicon('sort-numeric-asc ' ) . '</a>'; ?></td>
				   
                   <th class="text-center"><?php echo TABLE_HEADING_ACTION; ?></th>	   
                </tr>
              </thead>
              <tbody>
<?php
				  $rows = 0;
				  if (isset($HTTP_GET_VARS['page']) && ($HTTP_GET_VARS['page'] > 1)) $rows = $HTTP_GET_VARS['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;
				  $products_query_raw = "select p.products_id, p.products_ordered, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id = p.products_id and pd.language_id = '" . $languages_id. "' and p.products_ordered > 0 group by pd.products_id order by " . $sortOrderPurchased;
				  $products_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);

 				  $products_query = tep_db_query($products_query_raw);
				  while ($products = tep_db_fetch_array($products_query)) {
					$rows++;

					if (strlen($rows) < 2) {
					  $rows = '0' . $rows;
				    }
?>
                    <tr onclick="document.location.href='<?php echo tep_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $products['products_id'] . '&origin=' . FILENAME_STATS_PRODUCTS_PURCHASED . '?page=' . $HTTP_GET_VARS['page'], 'NONSSL'); ?>'">
                       <td>                    <?php echo $rows; ?>.</td>
                       <td class="text-left">  <?php echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $products['products_id'] . '&origin=' . FILENAME_STATS_PRODUCTS_PURCHASED . '?page=' . $HTTP_GET_VARS['page'], 'NONSSL') . '">' . $products['products_name'] . '</a>'; ?></td>
                       <td class="text-center"><?php echo $products['products_ordered']; ?>&nbsp;</td>
									<!-- Start Reset -->
					   <td class="text-center">
<?php 
                          if ( $products['products_ordered'] > 0 ) {
							  echo tep_glyphicon_button(IMAGE_DELETE,          'remove',        tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, 'resetPurchased=' . $products['products_id'] . '&page=' . $page . '&purchasedSortOrder=' . $purchasedSortOrder), null, 'danger') ;
						  }
?>
                       </td> 

                   </tr>
<?php
                  }
?>
			  </tbody>
			</table>
		  </div>
   </table>		
   
   <table class="table" style="width: 100%"> <!-- osCommerce table foot -->
	     <hr>
         <div class="row">
               <div class="mark text-left  col-xs-12 col-md-6"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></div>			   
               <div class="mark text-right col-xs-12 col-md-6"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></div>		   
	     </div>
          <hr>
          <div class="row">
             <div class="col-xs-12 col-md-7">		  
                   <?php echo tep_draw_bs_button(TEXT_CLEAR_ALL, 'remove', tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, 'resetPurchased=*&page=' . $page . '&purchasedSortOrder='. $purchasedSortOrder, NONSSL)) ; ?>
 			 </div>
            </div>		 
    </table>

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
