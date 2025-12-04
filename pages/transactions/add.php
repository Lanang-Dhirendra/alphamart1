<?php
session_start();
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);

include ROOTPATH . "/config/config.php";
include ROOTPATH . "/includes/header.html";
include ROOTPATH . "/database/process.php";

$conn = mysqlConnect();

if (@$_POST['selanjutnya']) {
    $query = mysqli_query($conn, "SELECT id_, code FROM transaction WHERE deletedat IS NULL ORDER BY createdat DESC LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $kode_terakhir = $data['code'];
        $urutan = (int) substr($kode_terakhir, 3, 4);
        $urutan++;

        $kode_transaksi = "TRX" . str_pad($urutan, 4, "0", STR_PAD_LEFT); // TRX0006

        $id = $data['id_'];
    } else {
        $kode_transaksi = "TRX0001";
    }

    $nama_kasir = $_POST['nama_kasir'];

    $kasir = readData("cashier", ["name" => $nama_kasir], 1, 0, false, $conn)["data"];
    $id_cashier = array_key_first($kasir);

    date_default_timezone_set('Asia/Makassar');
    $date = date("Y-m-d H:i:s");

    $insert = createData("transaction", ["date" => $date, "code" => $kode_transaksi, "id_cashier" => $id_cashier, "total" => 0]);

    $newData = readData("transaction", ["code" => $kode_transaksi], 1, 0, false, $conn)["data"];
    $id = array_key_first($newData);
    $_SESSION['id_transaction'] = $id;

    if ($insert > 0) {
        echo "<p>Gagal menyimpan transaksi: " . mysqli_error($conn) . "</p>";
    }
    redirect("/pages/transactions/transaction_details.php");
}
?>

<br><br>
<center>
    <div style="width:60%; text-align:left;">
        <h2 class="my-2 text-4xl">Add Transaction</h2>
        <hr>
        <form action="" method="POST">

            <label for="nama_kasir">Cashier:</label>
            <input type="text" class="form-control" name="nama_kasir" placeholder="Insert name..." list="kasirList"
                required autocomplete="off">

            <datalist id="kasirList">
                <?php
                $qKasir = mysqli_query($conn, "SELECT name FROM cashier WHERE deletedat IS NULL");
                while ($k = mysqli_fetch_assoc($qKasir)) {
                    echo "<option value='{$k['name']}'></option>";
                }
                ?>
            </datalist>
            <br>

            <input type="submit" name="selanjutnya" class="inline-block h-full px-4 py-2 bg-bgblue hover:bg-bgbluehov text-white rounded-sm transition duration-200" value="Add">
        </form>
    </div>
</center>

<?php include ROOTPATH . "/includes/footer.html"; ?>