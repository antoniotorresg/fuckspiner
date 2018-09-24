<?php
require_once('../../../wp-load.php');
require_once('fuckspiner.php');
require_once('feed.class.php');

// Sacamos las variables de la BD
$email 		= get_option( 'fuckspiner_email' );
$apikey 	= get_option( 'fuckspiner_apikey' );
$feed 		= get_option( 'fuckspiner_feed' );
$ingles 	= get_option( 'fuckspiner_ingles' );
$apiyandex 	= get_option( 'fuckspiner_apiyandex' );
$categorias = get_option( 'fuckspiner_categorias' );


function curl_spineame($email, $apikey, $descripcion){ 

    $url = 'https://spinea.me/api/query.php';
	$fields = array(

		'email'	=> urlencode($email),
	    'key'	=> urlencode($apikey),
	    'input'	=> urlencode($descripcion)
	);
	$fields_string = '';
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close ($ch);

	$result = json_decode($result);
	return $result;

}

function yandex_translate($apiyandex, $text)
{
    $url = file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?key=' . $apiyandex . '&lang=en-es&format=html&text=' . urlencode($text));
    $json = json_decode($url);
    return $json->text[0];
}

function check_exists_post( $titulo ) {
    $args_posts = array(
        'title'     => $titulo,
        'posts_per_page' => 1,
    );
    $loop_posts = new WP_Query( $args_posts );
    if ( ! $loop_posts->have_posts() ) {
        return false;
    } else {
        $loop_posts->the_post();
        return $loop_posts->post->ID;
    }
}

function extract_img_url($text) { 
	preg_match_all('/http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?/', $text, $matches);

	return $matches[0][0]; 
}

function Generate_Featured_Image( $image_url, $post_id  ){
	$upload_dir = wp_upload_dir();
	$image_data = file_get_contents($image_url);
	$filename = basename($image_url);
	if (wp_mkdir_p($upload_dir['path'])) {
		$file = $upload_dir['path'] . '/' . $filename;
	} else {                                   
		$file = $upload_dir['basedir'] . '/' . $filename;
		
	}
	file_put_contents($file, $image_data);

	$wp_filetype = wp_check_filetype($filename, null );
	$attachment = array(
	    'post_mime_type' 	=> $wp_filetype['type'],
	    'post_title' 		=> sanitize_file_name($filename),
	    'post_content' 		=> '',
	    'post_status' 		=> 'inherit',
	    'alt'   			=> trim(strip_tags( get_post_meta($attach_id, '_wp_attachment_image_alt', true) ))


	  );
	$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	$res1 = wp_update_attachment_metadata( $attach_id, $attach_data );
	$res2 = set_post_thumbnail( $post_id, $attach_id );

}


//* Creamos el objeto del feed
$rss = new RssReader ($feed);
foreach ($rss->get_items () as $item){ 

	if ($ingles == 1) {
		$descripcion = yandex_translate($apiyandex, $item->get_description());
		$titulo = yandex_translate($apiyandex, $item->get_title());
	} else {
		$descripcion = $item->get_description();
		$titulo = $item->get_title();
	}

	//* Chequeamos si existe el post
	$check = check_exists_post( $titulo );
	if (check_exists_post( $titulo ) == false) {
	
		if ($descripcion != '') {

			$result = curl_spineame($email, $apikey, $descripcion);
			if ($result->success == 'true') {

				// Creamos el array de post
				$my_post = array(
				  'post_title'    => wp_strip_all_tags( $titulo ),
				  'post_content'  => $result->output,
				  'post_status'   => 'publish',
				  'post_author'   => 1,
				  'post_category' => array( $categorias )
				);
				 
				// Insertamos el post en la base de datos
				$post_id = wp_insert_post( $my_post ); 

				// Asignamos la imagen destacada al post
				$url_imagen = extract_img_url($descripcion);
				Generate_Featured_Image( $url_imagen,   $post_id );
				//update_post_meta($attach_id, '_wp_attachment_image_alt', $title3);


			}
		}

		// Paramos durante 10 segundos para que no sea tan agresivo
		sleep(10);

	}

} 