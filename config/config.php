<?php declare(strict_types=1);

$Host = "localhost";
$User = "root";
$Pass = "";
$DBName = "alphamart1";

function mysqlConnect()
{
   global $Host, $User, $Pass, $DBName;

   $conn = false;
   try {
      $conn = mysqli_connect($Host, $User, $Pass, $DBName);
      if ($conn->connect_error) {
         throw $conn->errno;
      }
   } catch (Exception $e) {
      switch ($e->getCode()) {
         case 1045:
            echo "incorrect user/pass, user = \"" . $User . "\", pass = \"" . $Pass . "\"";
            break;
         case 1049:
            echo "unk DBName, dbname = \"" . $DBName . "\"";
            break;
         default:
            echo "conn failed, unk error, " . $e->getMessage() . $e->getCode();
            break;
      }
      die();
   }
   return $conn;
}

function parseQue()
{
   $queStr = parse_url("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", PHP_URL_QUERY);
   if ($queStr == null) {
      return null;
   }
   $queParts = explode("&", $queStr, substr_count($queStr, "&") + 1);
   $ques = array();
   foreach ($queParts as $val) {
      $quePart = explode("=", $val, 2);
      $ques[$quePart[0]] = $quePart[1];
   }
   return $ques;
}

function redirect(string $to = "/")
{
   if (str_split($to)[0] != "/") {
      $to = "/" . $to;
   }
   header("Location: http://" . $_SERVER['HTTP_HOST'] . $to);
   die();
}
