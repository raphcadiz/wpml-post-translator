<div class="wrap wrap-rcpt-settings">

    <h2>Post Translator Settings</h2>

    <?php
        $rcpt_settings = get_option('rcpt_settings');
        $google_transalator_key = isset($rcpt_settings['google_transalator_key']) ? $rcpt_settings['google_transalator_key'] : '';
        $cron_translate = isset($rcpt_settings['cron_translate']) ? $rcpt_settings['cron_translate'] : '';
        $number_posts_translate = isset($rcpt_settings['number_posts_translate']) ? $rcpt_settings['number_posts_translate'] : '10';
        $translate_time = isset($rcpt_settings['translate_time']) ? $rcpt_settings['translate_time'] : '';
        $languages_to_translate = isset($rcpt_settings['languages_to_translate']) ? $rcpt_settings['languages_to_translate'] : '';
        
    ?>

    <form method="post" action="options.php">
        <?php settings_fields( 'rcpt_settings' ); ?>
        <?php do_settings_sections( 'rcpt_settings' ); ?> 
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Google Translator API Key</th>
                    <td>
                        <input name="rcpt_settings[google_transalator_key]" type="text" value="<?= $google_transalator_key ?>" size="80" aria-required="true">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Enable CRON translation</th>
                    <td>
                        <input type="checkbox" name="rcpt_settings[cron_translate]" value="1" <?php checked( $cron_translate, 1 ); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row">Number of posts to translate</th>
                    <td>
                        <input name="rcpt_settings[number_posts_translate]" type="number" value="<?= $number_posts_translate ?>" size="80" aria-required="true">
                        <p class="description">Number of post to translate every cron run. Default is 10.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Time between translating</th>
                    <td>
                        <select name="rcpt_settings[translate_time]">
                            <option value="10" <?php selected( $translate_time, 10 ) ?>>10 minutes</option>
                            <option value="15" <?php selected( $translate_time, 15 ) ?>>15 minutes</option>
                            <option value="30" <?php selected( $translate_time, 30 ) ?>>30 minutes</option>
                        </select>
                        <p class="description">Time interval between cron translations.</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>
</div>