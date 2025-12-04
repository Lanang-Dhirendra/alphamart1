<?php declare(strict_types=1);
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/config/config.php";
include_once ROOTPATH . "/includes/header.html";
include_once ROOTPATH . "/database/process.php";

$conn = mysqlConnect();
$result = readData("cashier", [], 100, 0, false, $conn)["data"]
?>

<center>
    <h2 class="my-2 text-4xl">Cashier List</h2>
    <h2 class="mt-4"><a href="add.php" class="inline-block h-full px-4 py-2 bg-bgblue hover:bg-bgbluehov text-white rounded-sm transition duration-200">Add Cashier</a></h2>
    <table class="mt-4">
        <thead>
            <tr>
                <th class="p-4 bg-bgblue text-white">#</th>
                <th class="p-4 bg-bgblue text-white">Cashier Name</th>
                <th class="p-4 bg-bgblue text-white" colspan="2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($result as $id => $val) { ?>
                <tr class="odd:bg-slate-200 even:bg-neutral-100">
                    <td class="tableData"><?= $no++ ?></td>
                    <td class="tableData"><?= htmlspecialchars($val['name']) ?></td>
                    <td class="tableData">
                        <a href="edit.php?id=<?= $id ?>">Edit</a>
                    </td>
                    <td class="tableData">
                        <?php
                        $query_cek = mysqli_query($conn, "
                        SELECT transaction.id_cashier
                        FROM cashier 
                        JOIN transaction ON cashier.id_ = transaction.id_cashier 
                        WHERE transaction.id_cashier = \"$id\"
                        AND cashier.deletedat IS NULL
                        AND transaction.deletedat IS NULL
                    ");
                        if (mysqli_num_rows($query_cek) > 0) {
                            echo "<button disabled class='opacity-40'>Delete</button>";
                        } else {
                        ?>
                            <form action="/process/cashiers_process.php" method="post"
                                onsubmit="return confirm('Are you sure you want to delete?')">
                                <input type="hidden" name="id" value="<?= $id ?>">
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