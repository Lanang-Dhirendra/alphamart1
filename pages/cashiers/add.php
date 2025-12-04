<?php declare(strict_types=1);
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/includes/header.html";

?>

<center>
    <h2 class="my-2 text-4xl">Add Cashier</h2>
    <?php
    if (isset($_COOKIE["errcode"])) {
        setcookie("errcode", "", 0, "/");
        echo "<h2>Name exists.</h2>";
    }
    ?>
    <form action="/process/cashiers_process.php" method="POST">
        <table>
            <!-- hidden action:add data -->
            <input type="hidden" name="action" value="add" />
            <tr>
                <td class="py-2"><label>Cashier Name: </label></td>
                <td class="py-2"><input type="text" name="name" placeholder="Insert name..." maxlength="100" required /></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" class="inline-block h-full px-4 py-2 bg-bgblue hover:bg-bgbluehov text-white rounded-sm transition duration-200 float-right">Add</button>
                </td>
            </tr>
        </table>
    </form>
</center>

<?= include ROOTPATH . "/includes/footer.html"; ?>