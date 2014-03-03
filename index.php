<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once("php/properties.php");
include_once("table.php");

// initialize the global database object
try {
  $database = new PDO("sqlite:database/stacks.sqlite");
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
<script type="text/javascript" src="js/floatHead/dist/jquery.floatThead.js"></script>
<script type="text/x-mathjax-config">
  MathJax.Hub.Config({
    extensions: ["tex2jax.js"],
    jax: ["input/TeX", "output/HTML-CSS"],
    tex2jax: {
      inlineMath: [ ['$','$'], ["\\(","\\)"] ],
      displayMath: [ ['$$','$$'], ["\\[","\\]"] ],
      processEscapes: true,
    },
    "HTML-CSS": { availableFonts: ["TeX"], webFont: "Gyre-Pagella" },
    displayAlign: "left"
  });
</script>
<script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js"></script>

<link rel="stylesheet" type="text/css" href="css/table.css">
<link rel="stylesheet" type="text/css" href="http://stacks.math.columbia.edu/css/tag.css">

<script type="text/javascript">
// keep track of the location of the mouse and change the position of the tooltips
$(document).mousemove (function(e) {
  $("div.tooltip").css({"top": (20 + e.pageY) + "px", "left": (20 + e.pageX) + "px"});
});

// toggle the tooltip (fired by a JQuery event)
function toggleTooltip() {
  var tag = $(this).data("tag");

  // the tooltip doesn't exist yet, hence we create it
  if ($("div#tooltip-" + tag).length == 0) {
    // create the element
    $("body").append($("<div class='tooltip' id='tooltip-" + tag + "'></div>"));

    // create the blockquote containing the tag
    $("div#tooltip-" + tag).append($("<blockquote class='rendered'></blockquote>"));
    // load the HTML from the proxy script
    $("div#tooltip-" + tag + " blockquote").load("php/tag.php?tag=" + tag, function() {
      // render math once the text has been loaded
      MathJax.Hub.Queue(["Typeset", MathJax.Hub, "tooltip-" + tag]);
    });
  }
  // otherwise we can just toggle its visibility
  else
    $("div#tooltip-" + tag).toggle();
};

$(document).ready(function() {
  // hovering over a property shows its definition
  $("th[data-tag]").hover(toggleTooltip);
});
</script>

</head>

<body>

<h1>Stability of properties of morphisms</h1>
<?php
$table = new MorphismPropertiesPreservationTable($database);
print $table->outputTable();
?>
<h1>Stability of properties of objects in derived categories</h1>
<?php
//$table = new ComparisonTable($database, "derived-categories-preservation");
//print $table->outputTable();
?>
</body>
<script type="text/javascript">
$("table").floatThead();
</script>
</html>

