<div class="wrap">
    <h2><?php _e( 'Customers', 'erp' ); ?> <a href="<?php echo admin_url( 'admin.php?page=erp-accounting-customers&action=new' ); ?>" class="add-new-h2"><?php _e( 'Add New', 'erp' ); ?></a></h2>

    <form method="post" class="erp-ac-list-table-form">
        <input type="hidden" name="page" value="ttest_list_table">

        <?php
        $list_table = new WeDevs\ERP\Accounting\Customer_List_Table();
        $list_table->prepare_items();
        $list_table->search_box( 'search', 'search_id' );
        $list_table->display();
        ?>
    </form>
</div>
