<?php declare(strict_types=1);
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/config/config.php";
include_once ROOTPATH . "/includes/header.html";
include_once ROOTPATH . "/database/process.php";

$conn = mysqlConnect();
$result = mysqli_query($conn, "SELECT * FROM transaction WHERE deletedat IS NULL");
?>

<center>
    <h2 class="my-2 text-4xl">Transaction List</h2>
    <h2 class="mt-4"><a href="add.php" class="inline-block h-full px-4 py-2 bg-bgblue hover:bg-bgbluehov text-white rounded-sm transition duration-200">Add Transaction</a></h2>
    <table class="mt-4">
        <thead>
            <tr>
                <th class="p-4 bg-bgblue text-white">#</th>
                <th class="p-4 bg-bgblue text-white">Date</th>
                <th class="p-4 bg-bgblue text-white">Code</th>
                <th class="p-4 bg-bgblue text-white">Cashier Name</th>
                <th class="p-4 bg-bgblue text-white">Total</th>
                <th class="p-4 bg-bgblue text-white" colspan="2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr class="odd:bg-slate-200 even:bg-neutral-100">
                    <td class="tableData"><?= $no++ ?></td>
                    <td class="tableData"><?= htmlspecialchars($row['date']) ?></td>
                    <td class="tableData"><?= htmlspecialchars($row['code']) ?></td>
                    <td class="tableData"><?= htmlspecialchars(readData("cashier", ["id_" => $row['id_cashier']])["data"][$row['id_cashier']]["name"]) ?></td>
                    <td class="tableData text-right"><?= number_format(floatval($row['total']), 0, ',', '.') ?></td>
                    <td class="tableData">
                        <a href="transaction_details.php?id=<?= $row['id_'] ?>">Details</a>
                    </td>
                    <td class="tableData">
                        <form action="/process/transactions_process.php" method="post"
                            onsubmit="return confirm('Are you sure you want to delete?')">
                            <input type="hidden" name="id" value="<?= $row['id_'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</center>

<?= include ROOTPATH . "/includes/footer.html"; ?>