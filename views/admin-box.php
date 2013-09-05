<input type="hidden" name="series_noncename" id="series_noncename" value="<?php echo wp_create_nonce( 'series_noncename' ) ?>" />
 
<select name='tax_input[series]' id='post_series' class="widefat">
    <!-- Display series as options -->
    <option class='series-option' value='' <?php if ($none) echo "selected";?>>None</option>
    <?php
    foreach ($series as $single) {
        echo '<option class="series-option" value="' . $single->slug . '" ';
        echo $single->selected ? 'selected' : '';
        echo '>' . $single->name . "</option>\n"; 
    }
    ?>
</select>    
