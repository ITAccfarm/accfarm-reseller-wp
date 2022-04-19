<style>
    pre {
        white-space: pre-wrap;
        white-space: -moz-pre-wrap;
        white-space: -pre-wrap;
        white-space: -o-pre-wrap;
        word-wrap: break-word;
    }
</style>

<div class="wrap" id="af-import-wrap">
    <h2><?php use Src\Services\LogExtractor;

        echo esc_html( get_admin_page_title() ); ?></h2>

    <table class="widefat striped fixed">
        <thead>
        <tr>
            <th style="width: 66%;">Log</th>
            <th>Type</th>
            <th>Date</th>
        </tr>
        </thead>

        <tbody id="af-table-logs-body">
            <?php foreach (LogExtractor::instance()->all() as $log): ?>
                <tr>
                    <td style="width: 66%;"><pre><?php echo $log['data']; ?></pre></td>
                    <td><?php echo $log['type']; ?></td>
                    <td><?php echo $log['date']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
        <tr>
            <th style="width: 66%;">Log</th>
            <th>Type</th>
            <th>Date</th>
        </tr>
        </tfoot>
    </table>
</div>