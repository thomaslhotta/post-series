<nav>
    <ul class="pager">
        <?php if ($prev_post):?>
           <li class="previous">
               <a href="<?php echo $prev_post['permalink']?>">
                   &larr; <?php echo $prev_post['title']?>
               </a>
          </li>
        <?php endif?>
        
        <?php if ($next_post):?>
           <li class="next">
               <a href="<?php echo $next_post['permalink']?>">
                   <?php echo $next_post['title']?> &rarr;
               </a>
           </li>
        
        <?php endif?>
    </ul>
    
    <h3><?php _e('Inhalt', 'post-series')?>:</h3>
    
    
    <ul>
        <?php foreach ($posts as $post):?>
        <li>
            <?php 
                if (true === $post['active']) {
                    echo $post['title'];
                } else {
                    echo '<a href="' . $post['permalink'] . '">' . $post['title'] . '</a>';
                }
            ?>
        </li>
        <?php endforeach;?>
    </ul>
</nav>