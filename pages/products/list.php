<?php declare(strict_types=1);
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/config/config.php";
include_once ROOTPATH . "/includes/header.html";

$conn = mysqlConnect();
$result = mysqli_query($conn, "SELECT * FROM product WHERE deletedat IS NULL");
?>

<center>
    <h2 class="my-2 text-4xl">Product List</h2>
    <h2 class="mt-4"><a href="add.php" class="inline-block h-full px-4 py-2 bg-bgblue hover:bg-bgbluehov text-white rounded-sm transition duration-200">Add Product</a></h2>

    <table class="mt-4">
        <thead>
            <tr>
                <th class="p-4 bg-bgblue text-white">No</th>
                <th class="p-4 bg-bgblue text-white">Product Name</th>
                <th class="p-4 bg-bgblue text-white">Price</th>
                <th class="p-4 bg-bgblue text-white">Voucher Name</th>
                <th class="p-4 bg-bgblue text-white">Discount</th>
                <th class="p-4 bg-bgblue text-white">Stock</th>
                <th class="p-4 bg-bgblue text-white" colspan="2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;

            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr class="odd:bg-slate-200 even:bg-neutral-100">
                    <td class="tableData"><?= $no++ ?></td>
                    <td class="tableData"><?= htmlspecialchars($row['name']) ?></td>

                    <?php
                    $voucher_id = $row['id_voucher'];
                    $price = floatval($row['price']);

                    if ($voucher_id != "00000000") {
                        $diskon = mysqli_query($conn, "SELECT name, discount, max_discount FROM voucher WHERE id_ = '$voucher_id' AND deletedat IS NULL");

                        $diskon = mysqli_fetch_assoc($diskon);

                        $harga_diskon = $price - ($price * $diskon['discount'] / 100);

                        if ($diskon['max_discount'] > 0 && ($price * $diskon['discount'] / 100) > $diskon['max_discount']) {
                            $harga_diskon = $price - $diskon['max_discount'];
                        }
                    ?>
                        <td class="tableData"><del style="color:red"><?= number_format($price, 0, ',', '.') ?></del><br>
                            <?= number_format($harga_diskon, 0, ',', '.') ?></td>
                    <?php
                    } else {
                    ?>
                        <td class="tableData"><?= number_format($price, 0, ',', '.') ?></td>
                    <?php
                    }
                    ?>

                    <td class="tableData"><?= htmlspecialchars($voucher_id == "00000000" ? "-" : $diskon["name"]) ?></td>
                    <td class="tableData"><?= htmlspecialchars($voucher_id == "00000000" ? "-" : number_format(floatval($diskon["discount"]), 2, ",") . "%") ?></td>
                    <td class="tableData"><?= number_format(floatval($row['stock']), 0, ',', '.') ?></td>

                    <td class="tableData"> <a href="edit.php?id=<?= $row['id_'] ?>">Edit</a> </td>

                    <td class="tableData">
                        <?php
                        $query_cek = mysqli_query(
                            $conn,
                            "SELECT transact_details.id_product 
                         FROM product 
                         JOIN transact_details 
                         ON product.id_ = transact_details.id_product 
                         WHERE transact_details.id_product = '$row[id_]'"
                        );

                        if (mysqli_num_rows($query_cek) > 0) {
                            echo "<button disabled class='opacity-40'>Delete</button>";
                        } else {
                        ?>
                            <form action="/process/products_process.php" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete?')">
                                <input type="hidden" name="id" value="<?= $row['id_'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="cursor-pointer hover:underline">Delete</button>
                            </form>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</center>

<?= include ROOTPATH . "/includes/footer.html"; ?>