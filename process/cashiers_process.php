<?php declare(strict_types=1);

define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
include_once ROOTPATH . "/config/config.php";
include_once ROOTPATH . "/database/process.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = mysqlConnect();

    switch ($_POST['action']) {
        case "add":
            $name = $_POST['name'];
            if (count(readData("cashier", ["name" => $name], 1, 0, false, $conn)["data"]) > 0) {
                setcookie("errcode", "1", time() + 5, "/");
                redirect("/pages/cashiers/add.php");
            }
            createData("cashier", ["name" => $name], $conn);
            break;
        case "edit":
            $id = $_POST['id'];
            $name = $_POST['name'];
            $tmp = readData("cashier", ["name" => $name], 1, 0, false, $conn)["data"];
            if (count($tmp) > 0 && !isset($tmp[$id])) {
                setcookie("errcode", "1", time() + 5, "/");
                redirect("/pages/cashiers/edit.php?id=$id");
            }
            updateData("cashier", $id, ["name" => $name], $conn);
            break;
        case "delete":
            $id = $_POST['id'];
            deleteData("cashier", $id, $conn);
            break;
        default:
            break;
    }
    $conn->close();
}

redirect("/pages/cashiers/list.php");
