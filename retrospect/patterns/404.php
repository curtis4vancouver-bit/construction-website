<?php
/**
 * Title: 404
 * Slug: retrospect/404
 * Inserter: no
 */
?>
<!-- wp:group {"tagName":"main","metadata":{"name":"Main"},"style":{"spacing":{"blockGap":"0","margin":{"top":"0"}}},"layout":{"type":"constrained","justifyContent":"center"}} -->
<main class="wp-block-group" style="margin-top:0"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"20%"} -->
<div class="wp-block-column" style="flex-basis:20%"><!-- wp:group {"style":{"position":{"type":"sticky","top":"0px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:template-part {"slug":"header"} /-->

<!-- wp:template-part {"slug":"sidebar"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"75%"} -->
<div class="wp-block-column" style="flex-basis:75%"><!-- wp:group {"style":{"dimensions":{"minHeight":"100vh"}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="min-height:100vh"><!-- wp:group {"tagName":"main","style":{"position":{"type":""},"dimensions":{"minHeight":""},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"left":{"color":"var:preset|color|theme-2","width":"1px"},"right":{"color":"var:preset|color|theme-2","width":"1px"},"bottom":{"color":"var:preset|color|theme-2","width":"1px"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="border-right-color:var(--wp--preset--color--theme-2);border-right-width:1px;border-bottom-color:var(--wp--preset--color--theme-2);border-bottom-width:1px;border-left-color:var(--wp--preset--color--theme-2);border-left-width:1px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)"><!-- wp:heading {"textAlign":"left","fontSize":"medium"} -->
<h2 class="wp-block-heading has-text-align-left has-medium-font-size" id="page-not-found"><?php esc_html_e('Page Not Found', 'retrospect');?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","style":{"elements":{"link":{"color":{"text":"var:preset|color|theme-3"}}}},"textColor":"theme-3","fontSize":"x-small"} -->
<p class="has-text-align-left has-theme-3-color has-text-color has-link-color has-x-small-font-size"><?php esc_html_e('The page you are looking for does not exist. Maybe try a search?', 'retrospect');?></p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search...","width":100,"widthUnit":"%","buttonText":"Search","buttonPosition":"no-button","style":{"border":{"radius":"50px"}}} /--></main>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></main>
<!-- /wp:group -->