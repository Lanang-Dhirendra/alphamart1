<?php declare(strict_types=1);
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/config/config.php";
include_once ROOTPATH . "/includes/header.html";

$conn = mysqlConnect();
?>

<center>
    <h2 class="my-2 text-4xl">Add Product</h2>
    <?php
    if (isset($_COOKIE["errcode"])) {
        setcookie("errcode", "", 0);
        echo "<h2>Name exists.</h2>";
    }
    ?>
    <form action="/process/products_process.php" method="POST">
        <table cellpadding="10">
            <!-- hidden action:add data -->
            <input type="hidden" name="action" value="add" />

            <tr>
                <td class="pt-2 pb-1"><label>Products Name:</label></td>
                <td class="pt-2 pb-1"><input type="text" name="name" placeholder="Insert name..." maxlength="100" required /></td>
            </tr>

            <tr>
                <td class="py-1"><label>Voucher:</label></td>
                <td class="py-1">
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

                    <input type="text" list="voucher" name="id_voucher" placeholder="Insert voucher..." maxlength="8" />
                </td>
            </tr>

            <tr>
                <td class="py-1"><label>Price:</label></td>
                <td class="py-1"><input type="number" name="price" placeholder="Insert price..." required /></td>
            </tr>

            <tr>
                <td class="py-1"><label>Stock:</label></td>
                <td class="py-1"><input type="number" name="stock" placeholder="Insert stock..." required /></td>
            </tr>

            <tr>
                <td class="pt-1 pb-2" colspan="2">
                    <button type="submit" class="inline-block h-full px-4 py-2 bg-bgblue hover:bg-bgbluehov text-white rounded-sm transition duration-200 float-right">Add</button>
                </td>
            </tr>
        </table>
    </form>
</center>

<?= include ROOTPATH . "/includes/footer.html"; ?>