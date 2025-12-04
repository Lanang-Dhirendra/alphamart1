<?php declare(strict_types=1);
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/config/config.php";
include_once ROOTPATH . "/includes/header.html";

$conn = mysqlConnect();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $id = 0;
}

$product = false;
if ($id > 0) {
    $result = mysqli_query($conn, "SELECT * FROM product WHERE id_ = \"$id\" AND deletedat IS NULL");
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    }
}

if (!$product) {
    redirect("/pages/products/list.php");
    exit;
}
?>

<center>
    <h2 class="my-2 text-4xl">Edit Product</h2>
    <?php
    if (isset($_COOKIE["errcode"])) {
        setcookie("errcode", "", 0);
        echo "<h2>Name exists.</h2>";
    }
    ?>
    <form action="/process/products_process.php" method="post">
        <table cellpadding="10">
            <!-- hidden action:edit & id data -->
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id_']); ?>" />

            <tr>
                <td class="pt-2 pb-1"><label>Product Name:</label></td>
                <td class="pt-2 pb-1">
                    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" placeholder="Insert name..." maxlength="100" required />
                </td>
            </tr>

            <tr>
                <datalist id="voucher" required>
                    <option value="" disabled selected>Select Voucher</option>
                    <?php
                    $query = "SELECT * FROM voucher WHERE deletedat IS NULL";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['id_'] . "'>" . $row['name'] . " - " . $row['discount'] . "%</option>";
                    }
                    ?>
                </datalist>

                <td class="py-1"><label>Voucher:</label></td>
                <td class="py-1">
                    <input type="text" list="voucher" name="id_voucher"
                        value="<?php echo htmlspecialchars($product['id_voucher']); ?>" placeholder="Insert voucher..." maxlength="8" />
                </td>
            </tr>

            <tr>
                <td class="py-1"><label>Price:</label></td>
                <td class="py-1">
                    <input type="number" name="price"
                        value="<?php echo htmlspecialchars($product['price']); ?>" placeholder="Insert price..." required />
                </td>
            </tr>

            <tr>
                <td class="py-1"><label>Stock:</label></td>
                <td class="py-1">
                    <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>"
                        placeholder="Insert stock..." required />
                </td>
            </tr>

            <tr>
                <td class="py-1"></td>
                <td class="pt-1 pb-2">
                    <button type="submit" class="inline-block h-full px-4 py-2 bg-bgblue hover:bg-bgbluehov text-white rounded-sm transition duration-200 float-right">Update</button>
                </td>
            </tr>
        </table>
    </form>
</center>

<?= include ROOTPATH . "/includes/footer.html"; ?>