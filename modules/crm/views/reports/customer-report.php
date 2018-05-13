<?php

$data         = [];
$start        = !empty( $_POST['start'] ) ? $_POST['start'] : false;
$end          = !empty( $_POST['end'] ) ? $_POST['end'] : date('Y-m-d');
$filter_type  = !empty( $_POST['filter_type'] ) ? $_POST['filter_type'] : 'life_stage';

$reports      = erp_crm_customer_reporting_query( $start, $end, $filter_type );

?><div class="wrap">
    <h2 class="report-title"><?php _e( 'Customer Report', 'erp' ); ?></h2>
    <div class="erp-crm-report-header-wrap">
        <?php erp_crm_customer_report_filter_form(); ?>
        <button class="print" onclick="window.print()">Print</button>
    </div>
    <table class="table widefat striped">
        <thead>
            <tr>
                <th><?php _e( 'Label', 'erp' ); ?></th>
                <th><?php _e( 'Subscriber', 'erp' ); ?></th>
                <th><?php _e( 'Opportunity', 'erp' ); ?></th>
                <th><?php _e( 'Lead', 'erp' ); ?></th>
                <th><?php _e( 'Customer', 'erp' ); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php if ( $filter_type === 'life_stage' ) :
                foreach ( $reports as $report ) {
                    $data[$report->life_stage] = $report->total;
                }
            ?>
            <tr>
                <td>All</td>
                <td><?php echo !empty( $data['subscriber'] )  ? $data['subscriber']  : 0; ?></td>
                <td><?php echo !empty( $data['opportunity'] ) ? $data['opportunity'] : 0; ?></td>
                <td><?php echo !empty( $data['lead'] )        ? $data['lead']        : 0; ?></td>
                <td><?php echo !empty( $data['customer'] )    ? $data['customer']    : 0; ?></td>
            </tr>

            <?php elseif ( $filter_type === 'contact_owner' ) :
                foreach ( $reports as $report ) {
                    $data[ucwords( $report->contact_owner )] = $report->owner_data;
                }

                foreach ( $data as $key => $value ) : ?>
                    <tr>
                        <td><?php echo $key; ?></td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $detail->life_stage === 'subscriber' ) {
                                    $num = $detail->num;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $detail->life_stage === 'opportunity' ) {
                                    $num = $detail->num;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $detail->life_stage === 'lead' ) {
                                    $num = $detail->num;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $detail->life_stage === 'customer' ) {
                                    $num = $detail->num;
                                }
                            }

                            echo $num;
                        ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php elseif ( $filter_type === 'country' ) :
                foreach ( $reports as $report ) {
                    $data[ $report->country ] = $report->country_data;
                }

                foreach ( $data as $key => $value ) : ?>
                    <tr>
                        <td><?php echo $key !== -1 ? erp_get_country_name( $key ) : 'Other'; ?></td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $detail->life_stage === 'subscriber' ) {
                                    $num = $detail->num;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $detail->life_stage === 'opportunity' ) {
                                    $num = $detail->num;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $detail->life_stage === 'lead' ) {
                                    $num = $detail->num;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $detail->life_stage === 'customer' ) {
                                    $num = $detail->num;
                                }
                            }

                            echo $num;
                        ?>
                        </td>
                    </tr>
                <?php endforeach;

            elseif ( $filter_type === 'source' ) :

                foreach ( $reports as $key => $value ) : ?>
                    <tr>
                        <td><?php echo $key; ?></td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $key === 'subscriber' ) {
                                    $num = $detail;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $key === 'opportunity' ) {
                                    $num = $detail;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $key === 'lead' ) {
                                    $num = $detail;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $key === 'customer' ) {
                                    $num = $detail;
                                }
                            }

                            echo $num;
                        ?>
                        </td>
                    </tr>
                <?php endforeach;

                
            elseif ( $filter_type === 'group' ) :

                foreach ( $reports as $key => $value ) : ?>
                    <tr>
                        <td><?php echo $key; ?></td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $key === 'subscriber' ) {
                                    $num = $detail;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $key === 'opportunity' ) {
                                    $num = $detail;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $key === 'lead' ) {
                                    $num = $detail;
                                }
                            }

                            echo $num;
                        ?>
                        </td>

                        <td>
                        <?php $num = 0;
                            foreach ( $value as $key => $detail ) {
                                if ( $key === 'customer' ) {
                                    $num = $detail;
                                }
                            }

                            echo $num;
                        ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>
    </table>

</div>

<style>
    .report-title {
        padding-bottom: 10px !important;
    }

    .erp-crm-report-filter-form {
        float: left;
        display: flex;
    }

    .erp-crm-report-header-wrap {
        height: 25px;
    }

    .print {
        float: right;
    }

    .table.widefat.striped {
        margin-top: 10px;
    }

    @media print {
        .report-title {
            text-align: center;
        }

        .erp-crm-report-header-wrap {
            display: none;
        }

        .table.widefat.striped {
            margin-top: 20px;
        }
    }
</style>
