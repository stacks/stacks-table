<?php
include_once("php/properties.php");

// initialize the global database object
try {
  $database = new PDO("sqlite:database/properties.sqlite");
  $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
  print "Something went wrong with the database.";
  // if there is actually a persistent error: add output code here to check it
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.1.min.js"></script>

<link rel="stylesheet" type="text/css" href="css/main.css">

<script type="text/javascript">
</script>

</head>

<body>

<h1>Stability of properties</h1>

<table>
<thead>
<tr>
  <th></th>
<?php
$situations = getSituations();
foreach ($situations as $situation)
  print "<th>" . $situation["name"] . "</th>";
?>
</tr>
</thead>

<tbody>
<?php
$properties = getProperties();
$relations = getRelations();
foreach ($properties as $property) {
  print "<tr>";
  print "<th data-tag='" . $property["tag"] . "'><a href='http://stacks.math.columbia.edu/tag/" . $property["tag"] . "'>" . $property["name"] . "</a></th>";

  foreach ($situations as $situation) {
    if ($relations[$property["name"]][$situation["name"]] == "")
      print "<td></td>";
    else
      print "<td data-tag='" . $relations[$property["name"]][$situation["name"]] . "'><a href='http://stacks.math.columbia.edu/tag/" . $relations[$property["name"]][$situation["name"]] . "'>&#x2713;</a></td>";
  }

  print "</tr>";
}
?>
</tbody>
</table>
</body>
</html>

