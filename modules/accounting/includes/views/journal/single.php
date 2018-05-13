<?php
$company          = new \WeDevs\ERP\Company();
$status           = $transaction->status == 'draft' ? false : true;
$url              = admin_url( 'admin.php?page=erp-accounting-sales&action=new&type=invoice&transaction_id=' . $transaction->id );
$more_details_url = erp_ac_get_journal_invoice_url( $transaction->id );
$total_debit = 0;
$total_credit = 0;
?>
<div class="wrap">

    <h2>
        <?php
        _e( 'Journal', 'erp' );
        if ( isset( $popup_status ) ) {
            printf( '<a href="%1$s" class="erp-ac-more-details">%2$s &rarr;</a>', $more_details_url, __('More Details','accounting') );
        }
        ?>
    </h2>

    <div class="invoice-preview-wrap">

        <div class="erp-grid-container">
            <?php
            if ( ! isset( $popup_status ) ) {
                ?>
                <div class="row invoice-buttons erp-hide-print">
                    <div class="col-6">
                        <?php if ( $status ) {
                            ?>
                            <a href="#" class="button button-large erp-ac-print erp-hide-print"><?php _e( 'Print', 'erp' ); ?></a>
                            <?php
                        } else {
                            ?>
                            <a href="<?php echo $url; ?>" class="button button-large"><?php _e( 'Edit Invoice', 'erp' ); ?></a>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="row">
                <div class="invoice-number">
                    <?php
                        printf( __( 'Journal: <strong>%s</strong>', 'erp' ), $transaction->id );
                    ?>
                </div>
            </div>

            <div class="page-header">
                <div class="row">
                    <div class="col-3 company-logo">
                        <?php echo $company->get_logo(); ?>
                    </div>

                    <div class="col-3 align-right">
                        <strong><?php echo $company->name ?></strong>
                        <div><?php echo $company->get_formatted_address(); ?></div>
                    </div>
                </div><!-- .row -->
            </div><!-- .page-header -->

            <hr>

            <div class="row">
                <div class="col-3">
                    <table class="table info-table">
                        <tbody>
                            <tr>
                                <th><?php _e( 'Payment Number', 'erp' ); ?>:</th>
                                <td><?php echo $transaction->ref; ?></td>
                            </tr>
                            <tr>
                                <th><?php _e( 'Payment Date', 'erp' ); ?>:</th>
                                <td><?php echo strtotime( $transaction->issue_date ) < 0 ? '&mdash;' : erp_format_date( $transaction->issue_date ); ?></td>
                            </tr>
                            <tr>
                                <th><?php _e( 'Due Date', 'erp' ); ?>:</th>
                                <td><?php echo strtotime( $transaction->due_date ) < 0 ? '&mdash;' : erp_format_date( $transaction->due_date ); ?></td>
                            </tr>
                            <tr>
                                <th><?php _e( 'Amount Due', 'erp' ); ?>:</th>
                                <td><?php echo erp_ac_get_price( $transaction->due ); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><!-- .row -->

            <hr>

            <div class="row align-right">
                <table class="table fixed striped">
                    <thead>
                        <tr>
                            <th class="align-left product-name"><?php _e( 'Product', 'erp' ) ?></th>

                            <th><?php _e( 'Debit', 'erp' ) ?></th>

                            <th><?php _e( 'Credit', 'erp' ) ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ( $transaction->items as $line ) {
                            $total_debit  = $total_debit + $line->journal->debit;
                            $total_credit = $total_credit + $line->journal->credit;

                            ?>

                            <tr>
                                <td class="align-left product-name">
                                    <strong><?php echo $line->journal->ledger->name; ?></strong>
                                    <div class="product-desc"><?php echo $line->description; ?></div>
                                </td>

                                <td><?php echo erp_ac_get_price( $line->journal->debit ); //echo erp_ac_get_price( $line->unit_price ); ?></td>

                                <td><?php echo erp_ac_get_price( $line->journal->credit ); ?></td>
                            </tr>

                        <?php } ?>
                        <tr>
                            <td class="align-left product-name"><?php echo $transaction->summary; ?></td>
                            <td>
                                <strong><?php _e( 'Total', 'erp' ); ?>
                                <?php echo erp_ac_get_price( $total_debit ); ?></strong>
                            </td>
                            <td>
                                <strong><?php _e( 'Total', 'erp' ); ?>
                                <?php echo erp_ac_get_price( $total_credit ); ?></strong>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div><!-- .row -->

    <!--         <div class="row">
                <div class="col-3">
                    <?php echo $transaction->summary; ?>
                </div>
                <div class="col-3">
                    <table class="table info-table align-right">
                        <tbody>
                            <tr>
                                <th><?php _e( 'Total Debit', 'erp' ); ?></th>
                                <td><?php echo erp_ac_get_price( $transaction->total ); ?></td>

                                <th><?php _e( 'Total Credit', 'erp' ); ?></th>
                                <td><?php echo erp_ac_get_price( $transaction->total ); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> -->

        </div><!-- .erp-grid-container -->
    </div>

</div>

