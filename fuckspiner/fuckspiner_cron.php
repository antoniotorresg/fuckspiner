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
				wp_insert_post( $my_post );

			}
		}

		// Paramos durante 10 segundos para que no sea tan agresivo
		sleep(10);

	}

} 
