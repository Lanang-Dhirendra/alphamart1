<?php declare(strict_types=1);
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";

function whereBody(string $tipe, ?array $ques)
{
    $queCount = count($ques);
    if ($queCount == 0) {
        return "";
    }
    switch ($tipe) {
        case "where":
            $strReturn = "WHERE ";
            break;
        case "set":
            $strReturn = "SET ";
            break;
        default:
            return "";
    }
    $i = 0;
    $keys = array_keys($ques);
    foreach ($keys as $key) {
        $val = $ques[$key];
        if ($val == null) {
            $sym = $tipe == "where" ? "IS" : "=";
            $strReturn = "$strReturn $key $sym NULL";
        } else {
            if (gettype($val) == "string") {
                $val = "\"" . $val . "\"";
            }
            $strReturn = "$strReturn $key = $val";
        }
        if ($i + 1 != $queCount) {
            $sym = $tipe == "where" ? " AND " : ", ";
            $strReturn = "$strReturn $sym";
        }
        $i++;
    }
    return $strReturn;
}

function createData(string $tbl, $val = [], ?\mysqli $conn = null)
{
    if ($closeConn = $conn == null) {
        $conn = mysqlConnect();
        if ($conn->errno > 0) {
            return $conn->errno;
        }
    }

    $id = "";
    if (isset($val["id_"])) {
        $id = $val["id_"];
    } else {
        while (true) {
            $chrs = '0123456789abcdefghijklmnopqrstuvwxyz';
            $chrsLen = strlen($chrs);
            $id = "";
            for ($i = 0; $i < 8; $i++) {
                $id .= $chrs[random_int(0, $chrsLen - 1)];
            }
            $test = readData($tbl, ["id_" => $id], 1, 0, true, $conn);
            if ($test["errCode"] > 0) {
                return $test["errCode"];
            } else {
                if (count($test["data"]) == 0) {
                    break;
                }
            }
        }
    }

    $time = time();
    $val["id_"] = $id;
    $val["createdat"] = $time;
    $val["updatedat"] = $time;
    $val["deletedat"] = null;
    $fields = getFields($tbl, true, $conn);

    $newVal = [];
    $i = 0;
    foreach ($fields as $field) {
        // echo $field, $val[$field];
        $newVal[$i] = $val[$field];
        $i++;
    }

    $stmtstr = "INSERT INTO $tbl VALUES (" . str_repeat("?,", count($fields) - 1) . "?);";
    // echo $stmtstr;
    $stmt = $conn->prepare($stmtstr);
    if ($stmt->errno > 0) {
        return $stmt->errno;
    }

    $stmt->execute($newVal);
    $res = $stmt->get_result();
    if (!$res) {
        return mysqli_stmt_errno($stmt);
    }

    if ($closeConn) {
        $conn->close();
    }
    return 0;
}

function getFields(string $tbl, bool $model, ?\mysqli $conn = null)
{
    if ($closeConn = $conn == null) {
        $conn = mysqlConnect();
        if ($conn->errno > 0) {
            return $conn->errno;
        }
    }

    $stmt = $conn->prepare("SHOW COLUMNS FROM $tbl");
    if ($stmt->errno > 0) {
        return $stmt->errno;
    }

    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res) {
        return mysqli_stmt_errno($stmt);
    }

    $returnArr = [];
    $i = 0;
    foreach ($res as $row) {
        $j = 0;
        foreach ($row as $val) {
            if ($j == 0) {
                $returnArr[$i] = $val;
            } else {
                break;
            }
            $j++;
        }
        $i++;
    }
    if (!$model) {
        $returnArr = array_slice($returnArr, 4);
    }

    if ($closeConn) {
        $conn->close();
    }
    return $returnArr;
}

function readData(string $tbl, array $filter = [], int $lim = 1, int $skip = 0, ?bool $allData = false, ?\mysqli $conn = null)
{
    if ($closeConn = $conn == null) {
        $conn = mysqlConnect();
        if ($conn->errno > 0) {
            return ["errCode" => $conn->errno, "data" => []];
        }
    }

    if (!$allData) {
        $filter["deletedat"] = null;
    }
    $whereStr = whereBody("where", $filter);
    $stmtstr = "SELECT * FROM $tbl $whereStr ORDER BY updatedat DESC LIMIT $lim OFFSET $skip";
    $stmt = $conn->prepare($stmtstr);
    if ($stmt->errno > 0) {
        return ["errCode" => $stmt->errno, "data" => []];
    }

    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res) {
        return ["errCode" => mysqli_stmt_errno($stmt), "data" => []];
    }

    if ($res->num_rows == 0) {
        return ["errCode" => 0, "data" => []];
    }

    $returnArr = [];
    $returnFiel = ["" => ""];
    $i = 0;
    foreach ($res as $row) {
        $returnRow = [];
        $j = 0;
        foreach ($row as $val) {
            if ($i == 0) {
                $returnFiel[$j] = mysqli_fetch_field($res)->name;
            }
            $returnRow[$returnFiel[$j]] = $val;
            $j++;
        }
        $returnArr[$returnRow["id_"]] = $returnRow;
        $i++;
    }

    if ($closeConn) {
        $conn->close();
    }
    return ["errCode" => 0, "data" => $returnArr];
}

function updateData(string $tbl, string $id, array $val = [], ?\mysqli $conn = null)
{
    if ($closeConn = $conn == null) {
        $conn = mysqlConnect();
        if ($conn->errno > 0) {
            return $conn->errno;
        }
    }

    $res = readData($tbl, ["id_" => $id], 1, 0, false, $conn);
    if ($res == null) {
        return 1;
    }

    $val["updatedat"] = time();
    $setStr = whereBody("set", $val);
    $whereStr = whereBody("where", ["id_" => $id, "deletedat" => null]);
    $stmtstr = "UPDATE $tbl $setStr $whereStr ORDER BY updatedat DESC LIMIT 1";
    $stmt = $conn->prepare($stmtstr);
    if ($stmt->errno > 0) {
        return $stmt->errno;
    }

    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res) {
        return mysqli_stmt_errno($stmt);
    }

    if ($closeConn) {
        $conn->close();
    }
    return 0;
}

function deleteData(string $tbl, string $id, ?\mysqli $conn = null)
{
    return updateData($tbl, $id, ["deletedat" => time()], $conn);
}
