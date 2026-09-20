<?php
$id="aenderungen";
ini_set('include_path', 'inc');
include("db.php");
include("define.php");
include("head.php");
$db = new mydb();

$sql = "SELECT p.new, p.edit, p.mitgliedsnummer, p.nachname, p.vorname, p.strasse, p.plz, p.ort, p.lv, p.kv, p.bezirk, p.geaendert, p.kommentar FROM tbladress p ORDER BY p.adressID";
$q=$db->query($sql);
$hasBezirk = $db->query("SELECT COUNT(*) AS cnt FROM tblakk WHERE bezirk IS NOT NULL AND bezirk != ''")->fetchColumn();
echo "<table class='table table-borderes'>\n";
echo "<thead><tr>";
th("Neu?");
th("Mnr");
th("Nachname");
th("Vorname");
th("Strasse");
th("Ort");
th("LV");
if ($hasBezirk > 0) {
    th("Bezirk");
} else {
    th("KV");
}
th("geaendert");
th("Bemerkung");
echo "</tr></thead>\n";
echo "<tbody>";
while ($row=$q->fetch()) {
    echo "<tr>\n";
    if ($row['new'] == '1') {
    	td('Neu');
    } else {
    	td('Änderung');
    }
    td($row['mitgliedsnummer'],"r");
    td($row['nachname']);
    td($row['vorname']);
    td($row['strasse']);
    td($row['plz']);
    td($row['lv']);
    if ($hasBezirk > 0) {
        td($row['bezirk']);
    } else {
        td($row['kv']);
    }
    td($row['geaendert']);
    td($row['kommentar']);
    echo "</tr>\n";
}
echo "</tbody>";
echo "<tfoot>";
echo "</table>\n";
include("footer.php");
