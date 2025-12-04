<?php declare(strict_types=1);

define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/config/config.php";
include_once ROOTPATH . "/database/process.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = mysqlConnect();

    $action         = $_POST['action'];
    $product_name   = $_POST['product_name'];
    $transaction_id = $_POST['id_transaction'];

    $product = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT *, product.id_ AS id_product
         FROM product
         LEFT JOIN voucher ON product.id_voucher = voucher.id_ 
         WHERE product.name = '$product_name'"
    ));

    $product_id = $product['id_product'];
    $qty = $_POST['qty'];
    $unit_price = $product['price'];

    $cut = $product['price'] * $product['discount'] / 100;
    $sub_total = ($unit_price - $cut) * $qty;

    $sum_total = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT SUM(subtotal) AS total FROM transact_details WHERE id_transaction = '$transaction_id'"
    ))['total'];

    $sum_discount = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT SUM(subtotal * discount / 100) AS total_discount FROM transact_details WHERE id_transaction = '$transaction_id'"
    ))['total_discount'];

    $discount = $product['discount'];
    $total = $sub_total + $sum_total;

    if ($action == 'add') {
        createData("transact_details", ["id_transaction" => $transaction_id, "id_product" => $product_id, "qty" => $qty, "price" => $unit_price, "discount" => $discount, "subtotal" => $sub_total], $conn);

        mysqli_query(
            $conn,
            "UPDATE transaction SET total = '$total' WHERE transaction.id_ = '$transaction_id'"
        );
    }

    redirect("/pages/transactions/transaction_details.php?id=$transaction_id");
}
