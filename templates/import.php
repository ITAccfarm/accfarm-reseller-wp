<div class="wrap" id="af-import-wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <div style="font-size: 16px">
        <div style="margin-top: 30px">
            <h4>Publish products on import</h4>

            <input type="checkbox" id="publish-products">
            <label for="publish-products">Publish</label>
        </div>

        <div style="margin-top: 30px">
            <h4>Set accfarm prices to products</h4>

            <input type="checkbox" id="set-accfarm-prices" checked>
            <label for="set-accfarm-prices">Set Prices</label>

            <div class="prices-dependent">
                <h4>Add margin to prices</h4>

                <input placeholder="Add sum to accfarm prices"
                       type="number"
                       id="price-margin"
                       style="margin-right: 10px; width: 250px;">

                <select id="price-margin-select">
                    <option value="sum">Sum $</option>
                    <option value="percent">Percent %</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 30px">
            <button id="import-selected">Import Selected</button>
        </div>

        <button id="import-all" style="float: right">Import All</button>
    </div>

    <div style="font-size: 16px; margin-top: 30px; margin-bottom: 20px">
        <a href="#" id="go-back" style="display: none;">Previous Page</a>
    </div>

    <table class="widefat striped fixed">
        <thead>
        <tr>
            <th style="width: 5%;"><input type="checkbox" class="check-all"></th>
            <th class="af-type-name">Category</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody id="af-table-body">

        </tbody>

        <tfoot>
        <tr>
            <th style="width: 5%;"><input type="checkbox" class="check-all"></th>
            <th class="af-type-name">Category</th>
            <th>Action</th>
        </tr>
        </tfoot>
    </table>
</div>