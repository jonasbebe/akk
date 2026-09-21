<?php

$db = new mydb();
if ($info->ebene == "EP") {
    $selebene="nation";
    $selhead="Nation";
} else if ($info->ebene == "LV") {
    $hasBezirk = $db->query("SELECT COUNT(*) AS cnt FROM tblakk WHERE bezirk IS NOT NULL AND bezirk != ''")->fetchColumn();
    if ($hasBezirk > 0) {
        $selebene = "bezirk";
        $selhead = "Bezirk";
    } else {
        $selebene = "kv";
        $selhead = "KV";
    }
} else if ($info->ebene == "KV") {
    $selebene="ort";
    $selhead="Ort";
} else { # Ebene = BV
    $selebene="lv";
    $selhead="LV";
}

$sql = "select count(akkId) AS mitglieder,
               sum(akkPT) as akkreditiertPT,
               sum(akkAV) as akkreditiertAV,
               sum(offenerbeitrag<1) AS stimmbPT,
               sum(IF (nation IN ('" . implode("','", $info->nations) . "'),1,0)) AS stimmbAV
        from tblakk";
$bigrow = $db->query($sql)->fetch();

$sql = "select " . $selebene . ",
               count(akkId) AS mitglieder,
               sum(akkPT) as akkreditiertPT,
               sum(akkAV) as akkreditiertAV,
               sum(offenerbeitrag<1) AS stimmbPT,
               sum(IF (nation IN ('" . implode("','", $info->nations) . "'),1,0)) AS stimmbAV
    from tblakk
    group by " . $selebene . "
    order by " . $selebene . "";
$q=$db->query($sql);
echo "<table class='table table-condensed table-striped'>\n";
if ( $info->PT == 1 && $info->AV == 1 ) {
    echo "<colgroup>";
    echo "<col style='width:14%'><col style='width:9%'><col style='width:10%'><col style='width:7%'>";
    echo "<col style='width:9%'><col style='width:12%'><col style='width:10%'><col style='width:9%'><col style='width:12%'>";
    echo "</colgroup>\n";
    echo "<thead><tr>";
    echo "<th>" . $selhead . "</th>";
    echo "<th class='r' title=\"Mitglieder\">Mtgld</th>";
    echo "<th class='r' title=\"Stimmberechtigte Parteitag\">Stimmb. PT</th>";
    echo "<th class='r'>%</th>";
    echo "<th class='r' title=\"Akkreditierte Parteitag\">Akk. PT</th>";
    echo "<th class='r' title=\"Stimmgewicht auf dem Parteitag\">Anteil PT</th>";
    echo "<th class='r' title=\"Stimmberechtigte Aufstellungsversammlung\">Stimmb. AV</th>";
    echo "<th class='r' title=\"Akkreditierte Aufstellungsversammlung\">Akk. AV</th>";
    echo "<th class='r' title=\"Stimmgewicht auf der Aufstellungsversammlung\">Anteil AV</th>";
    echo "</tr></thead>\n";
} else {
    echo "<colgroup>";
    echo "<col style='width:16%'><col style='width:10%'><col style='width:10%'><col style='width:8%'>";
    echo "<col style='width:10%'><col style='width:16%'><col style='width:16%'><col style='width:14%'>";
    echo "</colgroup>\n";
    echo "<thead><tr>";
    echo "<th>" . $selhead . "</th>";
    echo "<th class='r' title=\"Mitglieder\">Mtgld</th>";
    echo "<th class='r' title=\"Stimmberechtigte\">Stimmb.</th>";
    echo "<th class='r'>%</th>";
    echo "<th class='r' title=\"Akkreditierte\">Akk.</th>";
    echo "<th class='r' title=\"Anteil Akkreditierte / Mitglieder\">% Akk. / Mtgld</th>";
    echo "<th class='r' title=\"Anteil Akkreditierte / Stimmberechtigte\">Akk. Stimmb.</th>";
    echo "<th class='r' title=\"Stimmgewicht auf dem Parteitag\">Parteitag Anteil</th>";
    echo "</tr></thead>\n";
}

echo "<tbody>";
while ($row=$q->fetch()) {

    echo "<tr>";
    td($row[$selebene]);
    td($row['mitglieder'],"r");
    td($row['stimmbPT'],"r");
    if ($row['mitglieder'] == 0)
        td("");
    else
        td(number_format(100 * $row['stimmbPT'] / $row['mitglieder'],1) . "&nbsp;%","r");
    td($row['akkreditiertPT'],"r");
    if ( $info->PT == 1 && $info->AV == 1 ) {
        if ($bigrow['akkreditiertPT'] == 0)
            td("");
        else
            td(number_format(100 * $row['akkreditiertPT'] / $bigrow['akkreditiertPT'],1) . "&nbsp;%","r");

        td($row['stimmbAV'],"r");
        td($row['akkreditiertAV'],"r");

        if ($bigrow['akkreditiertAV'] == 0)
            td("");
        else
            td(number_format(100 * $row['akkreditiertAV'] / $bigrow['akkreditiertAV'],1) . "&nbsp;%","r");
    } else {
        
        $akkreditiert = ($info->AV==1) ? $row['akkreditiertAV'] : $row['akkreditiertPT'];
        $akkreditiert_bigrow = ($info->AV==1) ? $bigrow['akkreditiertAV'] : $bigrow['akkreditiertPT'];
        $stimmb = ($info->AV==1) ? $row['stimmbAV'] : $row['stimmbPT'];
        if ($row['mitglieder'] == 0)
            td("");
        else
            td(number_format(100 * $akkreditiert / $row['mitglieder'],1) . "&nbsp;%","r");

        if ($stimmb == 0)
            td("");
        else
            td(number_format(100 * $akkreditiert / $stimmb,1) . "&nbsp;%","r");

        if ($akkreditiert_bigrow == 0)
            td("");
        else
            td(number_format(100 * $akkreditiert / $akkreditiert_bigrow,1) . "&nbsp;%","r");
    }

    echo "</tr>\n";
}
echo "</tbody>\n";

// Aggregierte Werte für die Summenzeile
$total_stimmbPT = $bigrow['stimmbPT'];
$total_stimmbAV = $bigrow['stimmbAV'];
$total_akkreditiertPT = $bigrow['akkreditiertPT'];
$total_akkreditiertAV = $bigrow['akkreditiertAV'];

if ( $info->PT == 1 && $info->AV == 1 ) {
    echo "<tfoot><tr>";
    td("Summe");
    td($bigrow['mitglieder'],"r");
    td($total_stimmbPT,"r");
    if ($bigrow['mitglieder'] == 0)
        td("");
    else
        td(number_format(100 * $total_stimmbPT / $bigrow['mitglieder'],2) . "&nbsp;%","r");
    td($total_akkreditiertPT,"r");
    if ($total_akkreditiertPT == 0)
        td("");
    else
        td(number_format(100 * $total_akkreditiertPT / $total_akkreditiertPT,2) . "&nbsp;%","r");

    td($total_stimmbAV,"r");
    td($total_akkreditiertAV,"r");
    if ($total_akkreditiertAV == 0)
        td("");
    else
        td(number_format(100 * $total_akkreditiertAV / $total_akkreditiertAV,2) . "&nbsp;%","r");
    echo "</tr></tfoot></table>\n";
} else {
    $total_akkreditiert = ($info->AV==1) ? $total_akkreditiertAV : $total_akkreditiertPT;
    $total_stimmb = ($info->AV==1) ? $total_stimmbAV : $total_stimmbPT;

    echo "<tfoot><tr>";
    td("Summe");
    td($bigrow['mitglieder'],"r");
    td($total_stimmb,"r");
    if ($bigrow['mitglieder'] == 0)
        td("");
    else
        td(number_format(100 * $total_stimmb / $bigrow['mitglieder'],2) . "&nbsp;%","r");
    td($total_akkreditiert,"r");
    if ($bigrow['mitglieder'] == 0)
        td("");
    else
        td(number_format(100 * $total_akkreditiert / $bigrow['mitglieder'],2) . "&nbsp;%","r");

    if ($total_stimmb == 0)
        td("");
    else
        td(number_format(100 * $total_akkreditiert / $total_stimmb,2) . "&nbsp;%","r");

    if ($total_akkreditiert == 0)
        td("");
    else
        td(number_format(100 * $total_akkreditiert / $total_akkreditiert,2) . "&nbsp;%","r");
    echo "</tr></tfoot></table>\n";
}
