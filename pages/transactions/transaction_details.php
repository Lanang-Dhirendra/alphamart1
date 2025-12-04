<?php declare(strict_types=1);
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/config/config.php";
include_once ROOTPATH . "/includes/header.html";

session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_SESSION['id_transaction'])) {
    $id = $_SESSION['id_transaction'];
} else {
    $id = "";
}

$conn = mysqlConnect();

$header_query = mysqli_query($conn, "SELECT transaction.*, cashier.name AS cashiername 
FROM transaction 
JOIN cashier ON cashier.id_ = transaction.id_cashier 
WHERE transaction.id_ = \"$id\"");

$detail = mysqli_fetch_assoc($header_query);

$query = mysqli_query($conn, "SELECT transact_details.*, product.name AS productname, id_voucher
FROM transact_details 
JOIN product ON product.id_ = transact_details.id_product
JOIN transaction ON transaction.id_ = transact_details.id_transaction
LEFT JOIN voucher ON product.id_voucher = voucher.id_
WHERE transact_details.id_transaction = \"$id\"");
?>


<center>
    <h1 class="my-2 text-4xl">Transaction Details</h1>

    <form action="/process/transactions_process.php" method="post">
        <input type="hidden" name="id_transaction" value="<?= $id ?>" />
        <input type="hidden" name="action" value="add" />

        <datalist id="products">
            <?php
            $query_product = mysqli_query($conn, "SELECT * FROM product WHERE deletedat IS NULL");
            while ($product = mysqli_fetch_assoc($query_product)) {
            ?>
                <option value="<?= $product['name'] ?>">
                <?php
            }
                ?>
        </datalist>

        <input type="text" list="products" name="product_name" placeholder="Search Products..." autocomplete="off" />
        <input type="number" name="qty" placeholder="Qty" autocomplete="off" />

        <input type="submit" value="Add Product" class="inline-block h-full px-4 py-2 bg-bgblue hover:bg-bgbluehov text-white rounded-sm transition duration-200 cursor-pointer hover:underline" />
    </form>
    <br><br>



    <table class="mt-4" border="2" cellpadding="10" cellspacing="0" style="width: 50%; border: solid 2px #000;">
        <tr>
            <td>
                <?= $detail['date'] ?>
            </td>
            <td colspan="3" style="text-align: right;">
                <?= $detail['code'] ?>/
                <?= $detail['cashiername'] ?>/
                <?= $detail['id_cashier'] ?>
            </td>
        </tr>

        <?php
        while ($detail_product = mysqli_fetch_assoc($query)) {
        ?>
            <tr>
                <td> <?= $detail_product['productname'] ?> </td>

                <td align="center">
                    <?= $detail_product['qty'] ?>
                </td>

                <?php
                $voucher_id = $detail_product['id_voucher'];
                $diskon = mysqli_query($conn, "SELECT discount, max_discount FROM voucher WHERE id_ = '$voucher_id' AND deletedat IS NULL");

                if ($voucher_id != "00000000") {
                    $diskon = mysqli_fetch_assoc($diskon);

                    $harga_diskon = $detail_product['price'] - ($detail_product['price'] * $diskon['discount'] / 100);

                    if (($detail_product['price'] * $diskon['discount'] / 100) > $diskon['max_discount']) {
                        $harga_diskon = $detail_product['price'] - $diskon['max_discount'];
                    }
                ?>
                    <td align="right">
                        <del style="color:red"><?= number_format(floatval($detail_product['price']), 0, ',', '.') ?></del><br>
                        <?= number_format(floatval($harga_diskon), 0, ',', '.') ?>
                    </td>
                <?php
                } else {
                    $harga_diskon = $detail_product['price'];
                ?>
                    <td align="right"><?= number_format(floatval($detail_product['price']), 0, ',', '.') ?></td>
                <?php
                }
                ?>

                <td align="right">
                    <?= number_format($harga_diskon*$detail_product['qty'], 0, ',', '.') ?>
                </td>
            </tr>
        <?php
        }
        ?>

        <tr>
            <td colspan="3" align="right"><strong>Total</strong></td>
            <td align="right">
                <strong><?= number_format(floatval($detail['total']), 0, ',', '.') ?></strong>
            </td>
        </tr>

    </table>
</center>

<?= include ROOTPATH . "/includes/footer.html"; ?>