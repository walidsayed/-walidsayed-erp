<div class="wrap erp-crm-campaign" id="wp-erp">

    <h2><?php _e( 'Campaigns', 'erp' ); ?>
        <a href="#" id="erp-customer-new" class="erp-contact-new add-new-h2" data-type="contact" title="<?php _e( 'Add New Contact', 'erp' ); ?>"><?php _e( 'Add New Contact', 'erp' ); ?></a>
    </h2>

    <div class="list-table-wrap">
        <div class="list-table-inner">

            <form method="get">
                <input type="hidden" name="page" value="erp-sales-campaigns">
                <?php
                $customer_table = new \WeDevs\ERP\CRM\Campaign_List_Table( 'contact' );
                $customer_table->prepare_items();
                $customer_table->search_box( __( 'Search Contact', 'erp' ), 'erp-customer-search' );
                $customer_table->views();

                $customer_table->display();
                ?>
            </form>

        </div><!-- .list-table-inner -->
    </div><!-- .list-table-wrap -->
</div>