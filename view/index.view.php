<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>madN</title>
</head>
<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
<body>

<?php
    if(isset($error)){
        echo "<p style='background-color: red'>{$error}</p>";
    }else{
        if($winner !== false){
            echo "<h1>The Winner is {$winner->getName()}</h1>";
        }else{
            echo "<h1>Die Spiel wird beendet und hat keine Sieger</h1>";
        }
        echo "<h3>Spielerliste</h3><ul>";
        foreach ($spielers as $sp){
            echo "<li>{$sp->getName()}</li>";
        }
        echo "</ul>";
        echo "<table><tr><th>Zug</th><th>gewürfelte Zahl</th><th>gezogene Figur</th><th>geschlagene Figur</th></tr>";
        $zugNummer = 0;
        foreach ($zugLogs as $zug){
            $zugNummer++;
            echo "<tr>";
            echo "<td>{$zugNummer}</td>";
            echo "<td>{$zug['diceResult']}</td>";
            echo "<td>{$zug['gezogeneFigur']}</td>";
            echo "<td>{$zug['geschlageneFigur']}</td>";
            echo "</tr>";
        }
    }   
?>
</body>
</html>