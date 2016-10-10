<?php
/**
 * Represents the view for Role Based Methods Shipping Method Settings.
 *
 *
 * @package   WC_Role_Methods
 * @author    Bryan Purcell <support@wpbackoffice.com>
 * @license   GPL-2.0+
 * @link      http://woothemes.com/woocommerce
 * @copyright 2015 WPBackOffice
 */

?>

<div class='wrap'>
	<h3><?php _e('Roles', 'woocommerce-role-based-methods'); ?></h3>
	<p style='width:800px;'><?php _e('Please select the shipping methods that you would like activated for each respective role currently configured.', 'woocommerce-role-based-methods'); ?>
</p>
	<form method='post' action='' id='psroles_settings'>
				<div class='shippingrolepanel'>
					<table class='wc_shipping widefat' cellspacing='0'>
						<thead>
							<tr>
								<th>&nbsp;</th>
								<?php foreach($shipping_methods as $col): ?>
									<th><p><?php echo $this->get_shipping_method_title($col); ?></p></th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach($wp_roles->get_names() as $roll): ?>
								<?php $this->print_shipping_row(strtolower($roll), $shipping_methods, $methodarray); ?>
							<?php endforeach; ?>
							<?php $this->print_shipping_row('Guest', $shipping_methods, $methodarray);?>
						</tbody>
					</table>
				</div>

			<?php if($groups) { ?>
				<div style="clear:both;"></div>
				<h3><?php _e('Groups', 'woocommerce-role-based-methods'); ?></h3>

				<div class="enable-groups-toggle">
					<label for="ship-groups-enable"><?php _e('Enable Group Based Method Control', 'woocommerce-role-based-methods'); ?>
						<input type="checkbox" <?php if(isset($options['ship-groups-enable']) && $options['ship-groups-enable'] == "Yes"): ?> checked <?php endif; ?> name="woocommerce_role_methods_options[ship-groups-enable]" id="ship-groups-enable" class="groups-toggle" value="Yes"><br>
					</label>
				</div>

				<div class="group-settings <?php if(isset($options['ship-groups-enable']) && $options['ship-groups-enable'] == "Yes"): ?> groups--visible <?php endif; ?> ">

						<label for="operator"><?php _e('Operator', 'woocommerce-role-based-methods'); ?></label>
						<select name="woocommerce_role_methods_options[shipping_operator]">
							<option <?php echo (isset($options['shipping_operator']) && $options['shipping_operator'] == 'and') ? "selected" : ""; ?> value="and"><?php _e('AND', 'woocommerce-role-based-methods'); ?></option>
							<option <?php echo ((isset($options['shipping_operator']) && $options['shipping_operator'] == 'or') || !isset($options['shipping_operator'])) ? "selected" : ""; ?> value="or" ><?php _e('OR', 'woocommerce-role-based-methods'); ?></option>
						</select>

						<p class="description"><?php _e("The operator will control how the Group/Role Settings will affect the available shipping methods. When 'AND' is selected, both group-based rules and role-based rules must match to use the shipping method. When 'OR' is selected, the gateway is available if either rule sections match for the purchasing customer.", "woocommerce-role-based-methods"); ?></p>

				<div class='group-role-panel'>
					<table class='wc_shipping widefat' cellspacing='0'>
						<thead>
							<tr>
							<th>&nbsp;</th>
								<?php foreach($shipping_methods as $col): ?>
									<th><p><?php echo $this->get_shipping_method_title($col); ?></p></th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach($groups as $group): ?>
								<?php $this->print_shipping_group_row($group, $shipping_methods, $group_methodarray); ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				</div>
			<?php } ?>


		<div style='clear:both;'></div>
		<p class='submit'>
			<input type='submit' name='Submit' class='button-primary' value='<?php _e('Save Changes', 'woocommerce-role-based-methods'); ?>' />
			<input type='hidden' name='settings-updated' value='true'/>
		</p>
	</form>
</div>