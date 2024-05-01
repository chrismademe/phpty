<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
      http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <?php

        foreach ( $collections->pages as $page ) {
            if ( str_ends_with( $page['permalink'], '.html' ) ) {
                echo '<url>';
                echo '<loc>' . $site['url'] . '/' . $page['url'] . '</loc>';
                echo '<lastmod>' . date( 'Y-m-d', filemtime( $page['inputFilePath'] ) ) . '</lastmod>';
                echo '</url>';
            }
        }

    ?>
</urlset>