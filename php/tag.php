<?php
// cross domain requests are not possible, hence we proxy them through this script

function isValidTag($tag) {
  return strlen($tag) == 4; // TODO this could be improved
}

if (isValidTag($_GET["tag"]))
  print file_get_contents("https://stacks.math.columbia.edu/data/tag/" . $_GET["tag"] . "/content/statement");
?>
