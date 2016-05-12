<?php
/**
 * User: Vitaly Kukin
 * Date: 12.03.2016
 * Time: 17:33
 */

global $wpdb;
?>
    <div class="row">
        <div class="col-sm-60">
            <table class="table table-hover table-white" cellpadding="0" cellspacing="0" id="license-table">
                <thead>
                <tr>
                    <th class="numb">#</th>
                    <th class="code text-left">Code</th>
                    <th class="limit">Limit</th>
                    <th class="used">Used</th>
                    <th class="type">Type</th>
                    <th class="price">Discount</th>
                    <th class="percent">Percent</th>
                    <th class="moreprice">More Price</th>
                    <th class="date">Date</th>
                    <th class="expired">Expired</th>
                    <th class="action">Action</th>
                </tr>
                </thead>
                <?php
                include(dirname(__FILE__) . "/pagination.php");

                $param_s = "";
                $orderby_sql = "ORDER BY id DESC";
                $count = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->discounts}");

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
                    $p->target("admin.php?page=elpay&xpage=discount" . $param_s);
                    $paging = !isset($_GET[$p->parameterName]) ? 0 : intval($_GET[$p->parameterName]);

                    $p->currentPage($paging); // Gets and validates the current page
                    $p->calculate(); // Calculates what to show
                    $p->parameterName('paging');
                    $p->adjacents(1); //No. of page away from the current page

                    $p->page = (!isset($_GET[$p->parameterName])) ? 1 : $_GET[$p->parameterName];

                    $limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;

                    $sql = "SELECT * FROM {$wpdb->discounts} {$orderby_sql} {$limit}";

                    $result = $wpdb->get_results($sql);

                    if ($result) { ?>
                        <tbody>
                        <?php
                        $col = ($p->page - 1) * $p->limit;

                        foreach ($result as $res) {

                            $col++;

                            printf(
                                '<tr>
                                    <td class="numb text-center">%d</td>
                                    <td class="code">%s</td>
                                    <td class="limit text-center">%d</td>
                                    <td class="used text-center">%d</td>
                                    <td class="type text-center">%s</td>
                                    <td class="price text-center">%s</td>
                                    <td class="percent text-center">%s</td>
                                    <td class="moreprice text-center">%s</td>
                                    <td class="date text-center">%s</td>
                                    <td class="expired text-center">%s</td>
                                    <td class="action text-center">%s</td>
	                            </tr>',
                                $col,
                                $res->code,
                                $res->limit,
                                $res->used,
                                $res->type,
                                $res->price,
                                $res->percent,
                                $res->moreprice,
                                date('d.m.Y H:i', strtotime($res->date_start)),
                                $res->date_end != '0000-00-00 00:00:00' ?
                                    date('d.m.Y H:i', strtotime($res->date_end)) : 'never',
                                sprintf(
                                    '<a href="#" title="%s" data-pid="%s" class="elp-discount-delete">
                                        <span class="fa fa-trash-o"></span></a>',
                                    __('Delete', 'elp'), $res->id
                                )
                            );
                        }
                        ?>

                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="11">
                                <div class="text-center footer-nav"><?php echo $p->getOutput() ?></div>
                            </th>
                        </tr>
                        </tfoot>

                        <?php
                    } else { ?>
                        <tbody>
                        <?php printf('<tr><td colspan="11">%s</td></tr>', __('No records found')); ?>
                        </tbody>
                        <?php
                    }
                } else { ?>
                    <tbody>
                    <?php printf('<tr><td colspan="11">%s</td></tr>', __('No records found')); ?>
                    </tbody>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>