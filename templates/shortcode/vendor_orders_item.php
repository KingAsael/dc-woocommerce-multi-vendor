<?php
/**
 * The template for displaying vendor orders item band called from vendor_orders.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_orders_item.php
 *
 * @author 		dualcube
 * @package 	WCMp/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;	

$pages = get_option('wcmp_pages_settings_name');
$vendor_detail_page = $pages['vendor_order_detail'];

if(!empty($orders)) { 
	foreach($orders as $order) {
		$order_obj = new WC_Order ( $order );
		$mark_ship =  $WCMp->vendor_dashboard->is_order_shipped($order, get_wcmp_vendor(get_current_user_id()));
		$user_id = get_current_user_id();
		?>
		<tr>
			<td align="center"  width="20" ><span class="input-group-addon beautiful">
				<input type="checkbox" class="select_<?php echo $order_status;?>" name="select_<?php echo $order_status;?>[<?php echo $order; ?>]" >
				</span></td>
			<td align="center" ><?php echo $order; ?> </td>
			<td align="center" ><?php echo date('d/m', strtotime($order_obj->order_date)); ?></td>
			<td class="no_display" align="center" >
				<?php 
					$vendor_share = $vendor->wcmp_get_vendor_part_from_order($order_obj, $vendor->term_id);
					if(!isset($vendor_share['total'])) $vendor_share['total'] = 0;
					echo  get_woocommerce_currency_symbol().$vendor_share['total']; 
				?>
			</td>
			<td class="no_display" align="center" ><?php echo $order_obj->get_status(); ?></td>
			<td align="center" valign="middle" >
				<?php
					$actions = array();
					$is_shipped = get_post_meta( $order, 'dc_pv_shipped', true );
					if($is_shipped) {
						$mark_ship_title = __('Shipped', $WCMp->text_domain);
					} else  {
						$mark_ship_title = __('Mark as shipped', $WCMp->text_domain);
					}
					$actions['view'] = array(
						'url'  => esc_url( add_query_arg( array( 'order_id' => $order ), get_permalink($vendor_detail_page))),
						'img' => $WCMp->plugin_url . 'assets/images/view.png',
						'title' => __('View' ,$WCMp->text_domain),
					);    
					
					$actions['wcmp_vendor_csv_download_per_order'] = array(
						'url'  => admin_url( 'admin-ajax.php?action=wcmp_vendor_csv_download_per_order&order_id=' . $order . '&nonce=' . wp_create_nonce( 'wcmp_vendor_csv_download_per_order' ) ),
						'img' => $WCMp->plugin_url . 'assets/images/download.png',
						'title' => __('Download' ,$WCMp->text_domain),
					);
					if ( get_option('woocommerce_calc_shipping') != 'no' ) {
						$actions['mark_ship'] = array(
							'url'  => '#',
							'title' => $mark_ship_title,
						);
					}
					
					$actions = apply_filters( 'wcmp_my_account_my_orders_actions', $actions, $order );
	
					if ($actions) {
						foreach ( $actions as $key => $action ) { ?>
							<?php if ($key == 'view') { ?> 
								<a title="<?php echo $action['title']; ?>" target="_blank" href="<?php echo $action['url']; ?>"><i><img src="<?php echo $action['img']; ?>" alt=""></i></a>&nbsp; 
							<?php } elseif ($key == 'mark_ship') { ?>
								<a id="popup-window" data-popup-target="#inline<?php echo $order; ?>" href="javascript:void(0);" data-id="<?php echo $order; ?>" data-user="<?php echo $user_id; ?>" class="fancybox mark_ship_<?php echo $order; ?>" <?php if($mark_ship) { ?> title="Shipped" style="pointer-events: none; cursor: default;" <?php } else { ?> title="mark as shipped" <?php } ?> ><i><img src="<?php if(!$mark_ship) echo $WCMp->plugin_url.'assets/images/roket_deep.png'; else echo $WCMp->plugin_url.'assets/images/roket-green.png'; ?>"  alt=""></i></a>                                                                                                                                
                                                                <input type="hidden" name="shipping_tracking_url" id="shipping_tracking_url_<?php echo $order; ?>" >
                                                                <input type="hidden" name="shipping_tracking_id" id="shipping_tracking_id_<?php echo $order; ?>" >
                                                                <div id="inline<?php echo $order; ?>" class="popup">
                                                                    <div class="popup-body"> 
                                                                        <span class="popup-exit"></span>    
                                                                        <div class="popup-content">
                                                                            <div class="shipping_msg_<?php echo $order; ?>" style="color: green;"></div>
                                                                            <div class="wcmp_headding2"><?php _e( 'Shipment Tracking Details', $WCMp->text_domain );?></div>
                                                                            <p><?php _e( 'Enter Tracking Url', $WCMp->text_domain );?> *</p>
                                                                            <input  class="long" onkeyup="geturlvalue(this, '<?php echo $order; ?>')" required type="text" name="shipping_tracking_url" placeholder="<?php _e( 'http://example.com/tracking/', $WCMp->text_domain );?>">
                                                                            <p><?php _e( 'Enter Tracking ID', $WCMp->text_domain );?> *</p>
                                                                            <input  class="long" onkeyup="getidvalue(this, '<?php echo $order; ?>')" required type="text" name="shipping_tracking_id" placeholder="<?php _e( 'XXXXXXXXXXXXX', $WCMp->text_domain );?>">
                                                                            <div class="action_div_space"> </div>
                                                                            <div class="action_div">
                                                                                <button class="wcmp_orange_btn submit_tracking" name="submit_tracking" data-id="<?php echo $order; ?>" id="submit_tracking"><?php _e( 'Submit', $WCMp->text_domain );?></button>
                                                                                <div class="clear"></div>
                                                                            </div>
                                                                            <div class="clear"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
							<?php } else { ?>
								<a title="<?php echo $action['title']; ?>" href="<?php echo $action['url']; ?>" data-id="<?php echo $order; ?>" class="<?php echo sanitize_html_class( $key ); ?>" href="#"><i><img src="<?php echo $action['img']; ?>" alt=""></i></a>&nbsp;
							<?php 
							}
						}
					}
					?>
			</td>
		</tr>
		<?php 
	}
}	
?>
<div class="popup-overlay"></div>


<style>
    .popup-overlay {
        background: rgba(0, 0, 0, 0.85) none repeat scroll 0 0;
        height: 100%;
        left: 100%;
        opacity: 0;
        position: fixed;
        top: 0;
        transition: opacity 0.2s ease-out 0s;
        width: 100%;
        z-index: 999;
    }
    .overlay .popup-overlay {
        left: 0;
        opacity: 1;
    }
    .popup {
        left: 50%;
        position: fixed;
        top: 18%;
        z-index: -9999;
    }
    .popup .popup-body {
        background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #ffffff 0%, #f7f7f7 100%) repeat scroll 0 0;
        border: 1px solid #e9e9e9;
        box-shadow: 1px 2px 3px 1px rgb(185, 185, 185);
        margin-left: -160px;
        min-height: 150px;
        opacity: 0;
        padding: 20px;
        position: relative;
        text-align: center;
        transition: opacity 0.2s ease-out 0s;
        width: 300px;
    }
    .popup.visible, .popup.transitioning {
        z-index: 9999;
    }
    .popup.visible .popup-body {
        opacity: 1;
    }
    .popup .popup-exit {
        cursor: pointer;
        display: block;
        height: 24px;
        position: absolute;
        right: 195px;
        top: 150px;
        width: 24px;
    }
</style>