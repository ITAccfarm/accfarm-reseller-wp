<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <form action="options.php" method="post">
        <?php
        settings_errors();
        settings_fields('accfarm_reseller_general');
        do_settings_sections('accfarm_reseller');
        submit_button(); ?>
    </form>
</div>