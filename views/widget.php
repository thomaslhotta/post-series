<nav>
    <ul class="pager">
        <?php if ( $prev_post ):?>
           <li class="previous">
               <a href="<?php echo esc_attr( $prev_post['permalink'] )?>">
                   &larr; <?php echo esc_html( $prev_post['title'] )?>
               </a>
          </li>
        <?php endif?>
        
        <?php if ( $next_post ):?>
           <li class="next">
               <a href="<?php echo esc_attr( $next_post['permalink'] )?>">
                   <?php echo esc_html( $next_post['title'] )?> &rarr;
               </a>
           </li>
        
        <?php endif?>
    </ul>
    
    <h3><?php _e( 'Contents', 'post-series' )?>:</h3>
    
    <ol>
        <?php foreach ( $posts as $post ):?>
        <li>
            <?php 
                if (true === $post['active']) {
                    echo $post['title'];
                } else {
                    echo '<a href="' . esc_attr( $post['permalink'] ) . '">' . esc_html( $post['title'] ) . '</a>';
                }
            ?>
        </li>
        <?php endforeach;?>
    </ol>
</nav>