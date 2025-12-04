<?php declare(strict_types=1);
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/config/config.php";
include_once ROOTPATH . "/includes/header.html";
include_once ROOTPATH . "/database/process.php";

if (isset($_GET['id'])) {
    $id = strval($_GET['id']);
} else {
    $id = "";
}

$cashier = readData("cashier", ["id_" => $id])["data"];
if (count($cashier) == 0) {
    redirect("/pages/cashiers/list.php");
}
?>
<center>
    <h2 class="my-2 text-4xl">Edit Cashier</h2>
    <?php
    if (isset($_COOKIE["errcode"])) {
        setcookie("errcode", "", 0, "/");
        echo "<h2>Name exists.</h2>";
    }
    ?>
    <form action="/process/cashiers_process.php" method="post">
        <table cellpadding="10">
            <!-- hidden action:edit & id data -->
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>" />

            <tr>
                <td class="py-2"><label>Cashier Name:</label></td>
                <td class="py-2">
                    <input type="text" name="name" value="<?php echo htmlspecialchars(strval($cashier[$id]["name"])); ?>" placeholder="Insert name..." maxlength="100" required />
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" class="inline-block h-full px-4 py-2 bg-bgblue hover:bg-bgbluehov text-white rounded-sm transition duration-200 float-right">Update</button>
                </td>
            </tr>
        </table>
    </form>
</center>

<?= include ROOTPATH . "/includes/footer.html"; ?>