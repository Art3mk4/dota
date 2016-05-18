<?php
/**
 * Created by PhpStorm.
 * User: Violeta
 * Date: 12.03.2016
 * Time: 17:33
 */

global $wpdb, $user_ID;

$page = 'wpPay';
if( isset($_GET['order']) ) {

    $id = intval($_GET['order']);

    $tnx = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->transaction} WHERE id = %d", $id)
    );

    $response = unserialize($tnx->response);

    ?>
    <div class="content-invoice">
        <div class="clearfix invoice-heading text-right">
            <h2 class="text-uppercase">Transaction: #<?php echo  $tnx->token ?></h2>
            <span class="date-create"><?php echo date('d F Y H:i', strtotime($tnx->date) )?></span>
        </div>

        <div class="row invoice-info">
            <div class="col-sm-30">
                <h5><?php echo $tnx->subscription = 1 ? __('Regular', 'elp') : __('Single', 'elp') ?></h5>
                <p>
                    <?php echo !empty($tnx->fullname) ? $tnx->fullname . '<br>' : '' ?>
                    <?php echo $tnx->email ?><br>
                    <?php _e('Status', 'elp') ?>: <?php echo $tnx->status ?><br>
                    <?php _e('Type', 'elp') ?>: <?php echo $tnx->type ?><br>
                    <?php _e('Payer ID', 'elp') ?>: <?php echo $tnx->payer_id ?><br>
                    <?php _e('Country', 'elp') ?>: <?php echo $tnx->country ?>
                </p>
            </div>
            <div class="col-sm-30 text-right">
                <h5><?php echo $tnx->name ?></h5>
                <p>
                    <?php echo $tnx->description ?><br><br>
                    <?php _e('Period', 'elp') ?>: <?php echo $tnx->period ?><br>
                    <?php _e('Frequency', 'elp') ?>: <?php echo $tnx->frequency ?><br>
                    <?php _e('Amount', 'elp') ?>: <?php echo $tnx->amount ?> <?php echo $tnx->currency_code ?><br>
                    <?php _e('Total Amount', 'elp') ?>: <?php echo $tnx->fullamount ?> <?php echo $tnx->currency_code ?><br>
                    <?php _e('Discount', 'elp') ?>: <strong><?php echo $tnx->coupon ?></strong> <?php echo $tnx->discount ?><br>
                    <?php //@todo full_amount coupon discount ?>
                </p>
            </div>
        </div>

        <div class="clearfix invoice-meta">
            <?php do_action('elp_transaction_info', $tnx->id); ?>

            <?php printf( '<h5>%1$s</h5>', __( 'About' ) ); ?>
            <p>
                <?php printf( '<span>%1$s: %2$s</span><br/>', __( 'FullName' ), $response['name'] ); ?>
                <?php printf( '<span>%1$s: %2$s</span><br/>', __( 'Email' ), $response['email'] ); ?>
                <?php printf( '<span>%1$s: %2$s</span><br/>', __( 'Phone' ), $response['phone'] ); ?>
                <?php printf( '<span>%1$s: %2$s</span><br/>', __( 'Address' ), $response['address'] ); ?>
                <?php printf( '<span>%1$s: %2$s</span><br/>', __( 'City' ), $response['city'] ); ?>
                <?php printf( '<span>%1$s: %2$s</span><br/>', __( 'State' ), $response['state'] ); ?>
                <?php printf( '<span>%1$s: %2$s</span>', __( 'Zip' ), $response['zip'] ); ?>
            </p>
            
            <?php
            $templates = str_replace('\\', '', $response['template']);
            $templates = json_decode( $templates );
            ?>

            <?php if( !empty($templates) ): ?>
                <div class="row">
                    <div class="col-xs-60">
                        <?php printf( '<h5>%1$s</h5>', __( 'Items' ) ); ?>
                    </div>
                    <?php foreach ($templates as $key => $value) {
                        printf( '<div class="col-lg-20">%1$s</div>', render_preview( $value ) );
                        // printf( '<div class="col-lg-20">
                        //         <a class="elp-download" href-lang="image/svg+xml" href="#">%2$s</a>
                        //     </div>', __( 'Download' ), render_preview( $value ) );
                    } ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

<?php } else { ?>

    <div class="row">
        <div class="col-sm-60">
            <table class="table table-hover table-white" cellpadding="0" cellspacing="0" id="license-table">
                <thead>
                <tr>
                    <th class="numb">#</th>
                    <th class="name text-left">Name</th>
                    <th class="country">Country</th>
                    <th class="email text-left hidden">Email</th>
                    <th class="token text-left">Transaction</th>
                    <th class="amount">Amount</th>
                    <th class="currency">Currency</th>
                    <th class="status">Status</th>
                    <th class="date">Date</th>
                    <th class="expired hidden">Expired</th>
                    <th class="recurring">Recurring</th>
                    <th class="action">Action</th>
                </tr>
                </thead>
                <?php
                include(dirname(__FILE__) . "/pagination.php");
                $param_s = "";
                $orderby_sql = "ORDER BY date DESC";
                $count = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->transaction}");

                if ($count > 0) {

                    $p = new pagination();
                    $p->parameterName = 'xpage';
                    $p->items($count);
                    $p->nextLabel('<span aria-hidden="true">&raquo;</span>');
                    $p->prevLabel('<span aria-hidden="true">&laquo;</span>');
                    $p->nextIcon("");
                    $p->prevIcon("");
                    $p->limit(20); // Limit entries per page

                    $param = "";
                    $p->target("admin.php?page=" . $page . "&xpage=order" . $param_s);
                    $paging = !isset($_GET[$p->parameterName]) ? 0 : intval($_GET[$p->parameterName]);

                    $p->currentPage($paging); // Gets and validates the current page
                    $p->calculate(); // Calculates what to show
                    $p->parameterName('paging');
                    $p->adjacents(1); //No. of page away from the current page

                    $p->page = (!isset($_GET[$p->parameterName])) ? 1 : $_GET[$p->parameterName];

                    $limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;

                    $sql = "SELECT * FROM {$wpdb->transaction} {$orderby_sql} {$limit}";

                    $result = $wpdb->get_results($sql);

                    if ($result) { ?>
                        <tbody>
                        <?php
                        $col = ($p->page - 1) * $p->limit;

                        foreach ($result as $res) {

                            $col++;

                            if ( $res->fullname == NULL ) {
                                $response = unserialize($res->response);
                                // pr($response);
                                $res->fullname = $response['name'];
                            }

                            printf(
                                '<tr>
                                    <td class="numb text-center">%d</td>
                                    <td class="name">%s</td>
                                    <td class="country text-center">%s</td>
                                    <td class="email hidden">%s</td>
                                    <td class="token">%s</td>
                                    <td class="amount text-center">%s</td>
                                    <td class="currency text-center">%s</td>
                                    <td class="status text-center">%s</td>
                                    <td class="date text-center">%s</td>
                                    <td class="expired text-center hidden">%s</td>
                                    <td class="recurring text-center">%s</td>
                                    <td class="action text-center">%s</td>
	                            </tr>',
                                $col,
                                $res->fullname,
                                $res->country,
                                '<a href="' . admin_url('admin.php?page=' . $page . '&xpage=order&order=') . $res->id . '">' .
                                    $res->email .
                                '</a>',
                                '<a href="' . admin_url('admin.php?page=' . $page . '&xpage=order&order=') . $res->id . '">' .
                                    $res->token .
                                '</a>',
                                $res->fullamount,
                                $res->currency_code,
                                $res->status,
                                date('d.m.Y H:i', strtotime($res->date)),
                                $res->expiration_date != '0000-00-00 00:00:00' ?
                                    date('d.m.Y H:i', strtotime($res->expiration_date)) : '',
                                $res->subscription = 1 ? __('Regular', 'elp') : __('Single', 'elp'),
                                $res->status == 'create' ?
                                    sprintf(
                                        '<a href="#" title="%s" data-pid="%s" class="elp-tnx-delete"><span class="fa fa-trash-o"></span></a>',
                                        __('Delete', 'elp'), $res->id
                                    ) : ''
                            );
                        }
                        ?>

                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="12">
                                <div class="text-center footer-nav"><?php echo $p->getOutput() ?></div>
                            </th>
                        </tr>
                        </tfoot>

                        <?php
                    } else { ?>
                        <tbody>
                        <?php printf('<tr><td colspan="12">%s</td></tr>', __('No records found')); ?>
                        </tbody>
                        <?php
                    }
                } else { ?>
                    <tbody>
                    <?php printf('<tr><td colspan="12">%s</td></tr>', __('No records found')); ?>
                    </tbody>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
    <?php

}