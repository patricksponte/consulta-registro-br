<?php

/**
 * Plugin Name:       Consulta Registro.br
 * Plugin URI:        https://github.com/patricksponte/consulta-registro-br
 * Description:       Faça consultas no registro.br através do shortcode <code>[consulta-registro-br]</code>.
 * Version:           1.0.0
 * Author:            Patrick Ponte
 * Author URI:        https://patrickponte.com
 * License:           GPL-2.0 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       consulta-registro-br
 */

include "vendor/isavail/Avail.php";

function php_extension_admin_notice__error() {
    if (extension_loaded('sockets') && function_exists('socket_create')) {
        return;
    } else {
        $class = 'notice notice-error';
        $message = __( 'Consulta Registro.br: PHP Socket Extension was not found, this should ocasionate plugin malfunction.', 'sample-text-domain' );
    }
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}
add_action( 'admin_notices', 'php_extension_admin_notice__error' );

function pesquisar_dominio() {
    include dirname(__FILE__) . '/form-consulta-registro-br.php';
    die();
}
add_shortcode( 'consulta-registro-br', 'pesquisar_dominio' );

function registro_br() {
    $fqdn = $_POST["domain"];

    if ( empty( $fqdn ) ) {     //recebe dominio
        $return["domain"] = "Domínio em branco!";
    } else {
        $atrib = array(
            "lang"        => 0,            # EN (PT = 1)
            "server"      => SERVER_ADDR,
            "port"        => SERVER_PORT,
            "cookie_file" => COOKIE_FILE,
            "ip"          => "",
            "suggest"     => 0,            # No domain suggestions
            "v6"          => ""
        );

        $fqdn             = $fqdn;
        $domain_info      = pp_verificar_dominio( $fqdn, $atrib );
        $response['json'] = [ "domain" => $domain_info->fqdn, "status" => $domain_info->status ];
        echo json_encode( $response['json'] );
        wp_die();
    }
}
add_action( 'wp_ajax_registro_br', 'registro_br' );
add_action( 'wp_ajax_nopriv_registro_br', 'registro_br' );

function pp_verificar_dominio( $fqdn, $parameters ) {
    $cliente = new AvailClient();
    $cliente->setParam( $parameters );

    return $cliente->send_query( $fqdn );
}
