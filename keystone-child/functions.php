<?php
/**
 * Keystone Child Theme functions and definitions
 */

/**
 * Enqueue parent theme styles
 */
function keystone_child_enqueue_styles() {
    wp_enqueue_style( 'keystone-child-style', get_stylesheet_uri(), array( 'astra-theme-css' ), wp_get_theme()->get('Version') );
}
add_action( 'wp_enqueue_scripts', 'keystone_child_enqueue_styles' );

/**
 * Inject JSON-LD Schema into wp_head
 */
function keystone_child_inject_schema() {
    // Attempt to get custom logo from theme mods, fallback to default URL
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
    if ( ! $logo_url ) {
        $logo_url = 'https://keystonerecomposition.com/wp-content/uploads/logo.png';
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Keystone Digital',
        'url' => 'https://keystonerecomposition.com',
        'logo' => $logo_url,
        'sameAs' => array(
            'https://www.youtube.com/@KeystoneRecomposition',
            'https://musicbrainz.org/label/30027d0e-6aeb-4704-8792-a031c936c62a',
            'https://audiomack.com/keystone-recomposition',
            'https://toolost.com'
        ),
        'identifier' => array(
            '@type' => 'PropertyValue',
            'propertyID' => 'Too Lost Catalog Reference ID',
            'value' => 'TOOLOST3000939655'
        ),
        'subOrganization' => array(
            array(
                '@type' => 'HealthAndBeautyBusiness',
                'name' => 'Keystone Recomposition',
                'url' => 'https://keystonerecomposition.com'
            ),
            array(
                '@type' => 'HomeAndConstructionBusiness',
                'name' => 'Keystone Possibilities',
                'url' => 'https://keystonepossibilities.ca',
                'identifier' => array(
                    '@type' => 'PropertyValue',
                    'propertyID' => 'BC Builder License',
                    'value' => '52603'
                ),
                'memberOf' => array(
                    '@type' => 'Organization',
                    'name' => 'WBI Home Warranty',
                    'url' => 'https://wbihomewarranty.com/'
                )
            )
        )
    );

    $json_schema = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

    echo "<!-- Keystone Digital JSON-LD Schema -->\n";
    echo "<script type=\"application/ld+json\">\n";
    echo $json_schema . "\n";
    echo "</script>\n";
    echo "<!-- End Keystone Digital JSON-LD Schema -->\n";
}
add_action( 'wp_head', 'keystone_child_inject_schema' );
