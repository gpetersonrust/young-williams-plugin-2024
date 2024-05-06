<div class="wrap">
    <h2>Animal API Control</h2>
    
    <form method="post" action="">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Animal API Refresh Time</th>
                <td>
                    <input type="text" name="animal_api_refresh_time" value="<?php echo esc_attr(get_option('animal_api_refresh_time')); ?>" />
                    <p class="description">Enter the refresh time in minutes.</p>
                </td>
            </tr>
           
            <!-- next_api_call_time -->
            <tr valign="top">
                <th scope="row">Animal API LAST Call Time</th>
                <td>
                   <!-- calendar with value -->
                   <input type="datetime-local" name="animal_api_next_call_time" value="<?php echo esc_attr(get_option('animal_api_next_call_time')); ?>" />

                    <p class="description">Enter the next call time.</p>
                </td>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
