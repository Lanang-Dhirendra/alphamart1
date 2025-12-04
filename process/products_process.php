<?php declare(strict_types=1);

define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/config/config.php";
include_once ROOTPATH . "/database/process.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = mysqlConnect();

    switch ($_POST['action']) {
        case "add":
            $name = $_POST['name'];
            if (count(readData("product", ["name" => $name], 1, 0, false, $conn)["data"]) > 0) {
                setcookie("errcode", "1", time() + 5, "/");
                redirect("/pages/products/add.php");
            }
            $price = intval($_POST['price']);
            $stock = intval($_POST['stock']);
            $voucher = empty($_POST['id_voucher']) ? "00000000" : $_POST['id_voucher'];
            createData("product", ["name" => $name, "price" => $price, "stock" => $stock, "id_voucher" => $voucher], $conn);
            break;
        case "edit":
            $id = $_POST['id'];
            $name = $_POST['name'];
            $tmp = readData("product", ["name" => $name], 1, 0, false, $conn)["data"];
            if (count($tmp) > 0 && !isset($tmp[$id])) {
                setcookie("errcode", "1", time() + 5, "/");
                redirect("/pages/products/edit.php?id=$id");
            }
            $price = intval($_POST['price']);
            $stock = intval($_POST['stock']);
            $voucher = empty($_POST['id_voucher']) ? "00000000" : $_POST['id_voucher'];
            updateData("product", $id, ["name" => $name, "price" => $price, "stock" => $stock, "id_voucher" => $voucher], $conn);
            break;
        case "delete":
            $id = $_POST['id'];
            deleteData("product", $id, $conn);
            break;
        default:
            break;
    }
    $conn->close();
}
redirect("/pages/products/list.php");
