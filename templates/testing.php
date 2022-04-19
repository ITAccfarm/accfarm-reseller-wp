<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <form action="options.php" method="post">
        <?php
        settings_errors();
        settings_fields('accfarm_reseller_testing_testing');
        do_settings_sections( 'accfarm_reseller_testing' );
        submit_button(); ?>
    </form>
</div>