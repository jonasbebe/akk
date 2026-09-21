<?php
$id="statistik";
ini_set('include_path', '../inc');
include("db.php");
include("functions.php");

?>
<html lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="refresh" content="30">
<title>Akk Akkreditierungsstatistik</title>
<link rel="shortcut icon" href="/favicon.ico" >
<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" media="screen">
<link rel="stylesheet" type="text/css" href="/css/akk.css" media="screen">
<link rel="stylesheet" type="text/css" href="/css/print.css" media="print">
<style>
body { font-size: 1.6vw; }
h1 { font-size: 3vw; }
h2 { font-size: 2.4vw; }
.table th, .table td { font-size: 1.6vw; padding: 0.8vh 1vw; }
.table-striped tbody tr:nth-child(odd) td { background-color: #ffffff; }
.table-striped tbody tr:nth-child(even) td { background-color: #f5f5f5; }
tfoot tr td { background-color: #e8e8e8 !important; font-weight: bold; }
.akkCount { font-size: 2.4vw; font-weight: bold; }
#titel { height: auto; position: relative; min-width: unset; border-top: 0.5vw solid orange; }
#result { margin-top: 1em; }
</style>
<!-- DO NOT REMOVE THIS
Hier steht ein Dank an Wilm, der das erste Akk-Tool überhaupt für die Piratenpartei programmiert hat,
und an Hendrik und Sebastian, die die Akkreditierung immer reibungslos zum Laufen gebracht haben.
Das Akk-tool wurde zum ersten Mal auf dem BPT 12.2 in Offenbach eingesetzt.
Es steht unter beerware-lizenz - denkt daran, wenn ihr sie seht.
Es wurde neu geschrieben, weil inzwischen neue Dinge dazugekommen sind.
END -->
</head>
<body>
<?php
$info = new allginfo("akk.ini",1);
echo "<div id='titel'>\n";
echo "<h1>Statistik " . $info->veranstaltung . " " . $info->ort  . "</h1>\n";

$db = new mydb();
$sql = "select count(akkId) AS mitglieder,sum(akkPT) as akkreditiertPT,sum(akkAV) as akkreditiertAV,
               sum(offenerbeitrag<1) AS stimmbPT, sum(IF (nation IN ('" . implode("','", $info->nations) . "'),1,0)) AS stimmbAV
        from tblakk";
$row = $db->query($sql)->fetch();
if ($info->PT == 1 && $info->AV == 1) {
   echo "<h2>Akkreditiert PT: <span class='akkCount'>&nbsp;",$row['akkreditiertPT'],"&nbsp;</span>&nbsp;-&nbsp;Akkreditiert AV: <span class='akkCount'>&nbsp;",$row['akkreditiertAV'],"&nbsp;</span></h2>";
} else {
   echo "<h2>Akkreditiert: <span class='akkCount'>&nbsp;" . (($info->PT==1) ? $row['akkreditiertPT'] : $row['akkreditiertAV']) . "&nbsp;</span></h2>";
}

echo "<ul></ul>\n";
echo "</div>\n";
echo "<div id = 'result'>\n";
include("stat.php");
echo "</div>\n";

?>
</body>
</html>
