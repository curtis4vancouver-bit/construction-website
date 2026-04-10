<?php
/**
 * Title: search
 * Slug: retrospect/search
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
<div class="wp-block-column" style="flex-basis:75%"><!-- wp:group {"tagName":"main","style":{"position":{"type":""},"dimensions":{"minHeight":"100vh"}},"layout":{"type":"default"}} -->
<main class="wp-block-group" style="min-height:100vh"><!-- wp:query {"queryId":3,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"layout":{"inherit":false}} -->
<div class="wp-block-query"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"border":{"left":{"color":"var:preset|color|theme-2","width":"1px"},"top":[],"right":{"color":"var:preset|color|theme-2","width":"1px"},"bottom":{"color":"var:preset|color|theme-2","width":"1px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-right-color:var(--wp--preset--color--theme-2);border-right-width:1px;border-bottom-color:var(--wp--preset--color--theme-2);border-bottom-width:1px;border-left-color:var(--wp--preset--color--theme-2);border-left-width:1px;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:query-title {"type":"search","level":3} /-->

<!-- wp:search {"showLabel":false,"placeholder":"Search...","buttonText":"Search","buttonPosition":"no-button","style":{"border":{"radius":"50px"}},"backgroundColor":"theme-5","textColor":"background","fontSize":"small"} /-->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"align":"left"} -->
<p class="has-text-align-left"><?php esc_html_e('No search results.', 'retrospect');?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"position":{"type":""},"spacing":{"margin":{"top":"0"}},"border":{"right":{"color":"var:preset|color|theme-2","width":"1px"},"top":[],"bottom":[],"left":{"color":"var:preset|color|theme-2","width":"1px"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="border-right-color:var(--wp--preset--color--theme-2);border-right-width:1px;border-left-color:var(--wp--preset--color--theme-2);border-left-width:1px;margin-top:0"><!-- wp:post-template {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default","columnCount":2}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9"} /-->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|60"}},"border":{"bottom":{"color":"var:preset|color|theme-2","width":"1px"}}},"layout":{"type":"constrained","justifyContent":"center"}} -->
<div class="wp-block-group" style="border-bottom-color:var(--wp--preset--color--theme-2);border-bottom-width:1px;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:post-title {"isLink":true,"style":{"typography":{"lineHeight":"1.2"}}} /-->

<!-- wp:post-excerpt {"fontSize":"normal"} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"6px"},"elements":{"link":{"color":{"text":"var:preset|color|theme-3"}}}},"textColor":"theme-3","fontSize":"x-small","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group has-theme-3-color has-text-color has-link-color has-x-small-font-size"><!-- wp:post-terms {"term":"category","prefix":"Posted in ","suffix":" on","fontSize":"x-small"} /-->

<!-- wp:post-date {"isLink":true,"style":{"elements":{"link":{"color":{"text":"var:preset|color|theme-3"},":hover":{"color":{"text":"var:preset|color|theme-4"}}}}},"textColor":"theme-3","fontSize":"x-small"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|30","left":"var:preset|spacing|30","top":"var:preset|spacing|30"},"margin":{"top":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:group --></div>
<!-- /wp:query --></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></main>
<!-- /wp:group -->