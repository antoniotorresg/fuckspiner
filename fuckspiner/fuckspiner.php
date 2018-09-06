<?php
/**
* Plugin Name: FuckSpiner
* Plugin URI: https://antonio-torres.es
* Description: Este plugin te permite spinear textos desde un feed y crear los post automaticamente
* Version: 1.0.0
* Author: Antonio Torres
* Author URI: https://antonio-torres.es
* License: GPL2
*/

function fuckspiner_create_menu() {

	//* Creamos el nuevo enlace en el menu
	add_menu_page('Configuración de fuckspiner', 'FuckSpiner', 'administrator', __FILE__, 'fuckspiner_settings_page' , plugins_url('/icon.png', __FILE__) );

	//* Llamamos la funcion para guardar los datos
	add_action( 'admin_init', 'register_fuckspiner_settings' );
}


function register_fuckspiner_settings() {
	//* Guardmos la configuracion
	register_setting( 'fuckspiner-settings', 'fuckspiner_email' );
	register_setting( 'fuckspiner-settings', 'fuckspiner_apikey' );
	register_setting( 'fuckspiner-settings', 'fuckspiner_feed' );
	register_setting( 'fuckspiner-settings', 'fuckspiner_ingles' );
	register_setting( 'fuckspiner-settings', 'fuckspiner_apiyandex' );
	register_setting( 'fuckspiner-settings', 'fuckspiner_categorias' );
}


function fuckspiner_settings_page() {
?>
<div class="wrap">
<h1>FuckSpiner</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'fuckspiner-settings' ); ?>
    <?php do_settings_sections( 'fuckspiner-settings' ); ?>
    <table class="form-table">
        <tr valign="top">
        	<th scope="row">Email</th>
        	<td><input type="text" name="fuckspiner_email" value="<?php echo esc_attr( get_option('fuckspiner_email') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        	<th scope="row">API Key</th>
        	<td><input type="text" name="fuckspiner_apikey" value="<?php echo esc_attr( get_option('fuckspiner_apikey') ); ?>" /></td>
        </tr>

        <tr valign="top">
        	<th scope="row">Feed Spinear</th>
        	<td><input type="text" name="fuckspiner_feed" value="<?php echo esc_attr( get_option('fuckspiner_feed') ); ?>" /></td>
        </tr>

        <tr valign="top">
        	<th scope="row">Ingles</th>
        	<td>
        		<select name="fuckspiner_ingles">
        			<option value="0" <?php if (esc_attr( get_option('fuckspiner_ingles') ) == 0) { echo 'selected'; } ?>>No</option>
        			<option value="1" <?php if (esc_attr( get_option('fuckspiner_ingles') ) == 1) { echo 'selected'; } ?>>Si</option>
        		</select>
        	</td>
        </tr>

         <tr valign="top">
        	<th scope="row">API Yandex Translate</th>
        	<td><input type="text" name="fuckspiner_apiyandex" value="<?php echo esc_attr( get_option('fuckspiner_apiyandex') ); ?>" /></td>
        </tr>

        <tr valign="top">
        	<th scope="row">IDs categoria WordPress (separado por comas)</th>
        	<td><input type="text" name="fuckspiner_categorias" value="<?php echo esc_attr( get_option('fuckspiner_categorias') ); ?>" /></td>
        </tr>
        
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php }


//* Lo añadimos al menu lateral
add_action('admin_menu', 'fuckspiner_create_menu');
