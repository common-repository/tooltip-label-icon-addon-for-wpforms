<?php
/*
Plugin Name: 	   Tooltip & Label Icon Addon For WPForms
Plugin URI: 	   https://zypacinfotech.com/wpforms-label-icon-tooltip/
Description:       Tooltip & Label Icon Addon For WPForms
Version:           1.0
Author:            Zypac Infotech
Author URI:        https://zypacinfotech.com
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:       label_icon_tooltip
*/

if (!function_exists('label_icon_tooltip_enqueue_backend')) {
	add_action( 'admin_enqueue_scripts', 'label_icon_tooltip_enqueue_backend' );
	function label_icon_tooltip_enqueue_backend() {
		wp_enqueue_style( 'fontawesome', esc_url( plugins_url( 'assets/css/all.min.css', __FILE__ ) ) );
		wp_enqueue_script( 'label_icon_tooltip', esc_url( plugins_url( 'assets/js/backend.js', __FILE__ ) ) );
	}
}

if (!function_exists('label_icon_tooltip_enqueue_frontend')) {
	add_action( 'wp_enqueue_scripts', 'label_icon_tooltip_enqueue_frontend' );
	function label_icon_tooltip_enqueue_frontend() {
		wp_register_style( 'fontawesome', esc_url( plugins_url( 'assets/css/all.min.css', __FILE__ ) ) );
		wp_register_style( 'wpforms-ui', esc_url( plugins_url( 'assets/css/jquery-ui.css', __FILE__ ) ) );
		wp_enqueue_script( 'label_icon_tooltip', esc_url( plugins_url( 'assets/js/frontend.js', __FILE__ ) ) );
	}
}

if (!function_exists('label_icon_tooltip_frontend_css_enq')) {
	add_action('wpforms_frontend_css','label_icon_tooltip_frontend_css_enq',100,1);
	function label_icon_tooltip_frontend_css_enq($form){
		wp_enqueue_style( 'fontawesome' );
		wp_enqueue_style( 'wpforms-ui' );
		wp_enqueue_script('jquery-ui-tooltip');
		wp_enqueue_script('label_icon_tooltip');
	}
}

if (!function_exists('label_icon_tooltip_display_field_before')) {
	add_action( 'wpforms_display_field_before', 'label_icon_tooltip_display_field_before', 9, 2 );
	function label_icon_tooltip_display_field_before( $field, $form_data ){
		if ( !empty( ( $field['icon'] ) || $field['tooltip']==1 ) && $field['label_hide']!=1 ) {
			$label = $form_data['fields'][$field['id']]['label'];
			$desc = $form_data['fields'][$field['id']]['description'];
			$for = 'wpforms-'.esc_attr( $form_data['id'] ).'-field_'.esc_attr( $field['id'] ); ?>
			<label class="wpforms-field-label new-label" for="<?php echo esc_attr( $for ); ?>">
			<?php if( !empty( $field['icon'] ) ){ ?>
				<i class="<?php echo esc_attr( $field['icon'] ); ?>"></i>
			<?php } ?>	
			<?php echo esc_html($label); ?>
			<?php if( $field['required']==1 ){ ?>
				<span class="wpforms-required-label">*</span>
			<?php } ?>
			<?php if( $field['tooltip']==1 ){ ?>
				<a href="javascript:void(0);" class="wpforms-tooltip" title="<?php echo esc_attr( $desc ); ?>">
				<i class="fa fa-info-circle"></i></a>
			<?php } ?>
			</label>
			<?php
		}
	}
}

if (!function_exists('label_icon_tooltip_field_data')) {
	add_filter( 'wpforms_field_data', 'label_icon_tooltip_field_data', 10, 2 );
	function label_icon_tooltip_field_data( $field, $form_data ){
		if( !empty($field['icon']) || $field['tooltip']==1 ){
			$field['label'] = '';
		}
		if( $field['tooltip']==1 ){
			$field['description'] = '';
		}
		return $field;
	}
}

if (!function_exists('label_icon_tooltip_field_options')) {
	add_action( 'wpforms_field_options_bottom_basic-options', 'label_icon_tooltip_field_options' );
	function label_icon_tooltip_field_options( $field ){
		$path = plugin_dir_path(__DIR__).'/tooltip-label-icon-addon-for-wpforms/icons.json';
		$icons = file_get_contents($path);
		$icons = explode(',',$icons); 
		
		$select_icon = isset( $field['icon'] ) && !empty( $field['icon'] ) ? $field['icon'] : '';
		$tooltip_sel = $field['tooltip']==1 ? esc_attr( 'checked' ) : '';
		$fid = $field['id'];
		
		echo '<div class="wpforms-field-option-row" id="wpforms-field-option-row-'.esc_attr( $fid ).'-icon" data-field-id="'.esc_attr( $fid ).'">
			<label for="wpforms-field-option-'.esc_attr( $fid ).'-icon">'.__( 'Select Icon', 'label_icon_tooltip').'<span class="toggle-unfoldable-cont" data-type="other"><i class="'.esc_attr( $field['icon'] ).'"></i></span></label>
			<select name="fields['.esc_attr( $fid ).'][icon]" class="wpform-icon-select">
			<option value="">'.__( '-Select-', 'label_icon_tooltip' ).'</option>';
			foreach ($icons as $key => $icon) {
				$icon_label = str_replace(['"','fa fa-','fab fa-','far fa-','fas fa-'],'',$icon);
				$icon = str_replace('"','',$icon);
				$sel = ( $icon==$select_icon ) ? 'selected' : '';
				echo '<option value="'.esc_attr( $icon ).'" '.$sel.'>'.esc_attr( $icon_label ).'</option>';
			}
		echo '</select></div>';
		
		echo '<div class="wpforms-field-option-row" id="wpforms-field-option-row-'.esc_attr( $fid ).'-tooltip" data-field-id="'.esc_attr( $fid ).'">
		<span class="wpforms-toggle-control"><input type="checkbox" id="wpforms-field-option-'.esc_attr( $fid ).'-tooltip" name="fields['.esc_attr( $fid ).'][tooltip]" class="" value="1" '.$tooltip_sel.'><label class="wpforms-toggle-control-icon" for="wpforms-field-option-'.esc_attr( $fid ).'-tooltip"></label><label for="wpforms-field-option-'.esc_attr( $fid ).'-tooltip" class="wpforms-toggle-control-label">'.__( 'Tooltip', 'label_icon_tooltip' ).'</label></span></div>'; 
	}
}