<?php

/**
 * Base class for a comparison table
 */
class ComparisonTable {
  protected $tableName; // the prefix of the tables in the database

  // constructor
  public function __construct($tableName) {
    $this->tableName = $tableName;
  }

  // this function returns the whole table (TODO would __toString be reasonable for this?)
  public function outputTable() {
    $output = "";

    $output .= "<table>";

    // header of the table
    $output .= "<thead>";
    $output .= "<tr>";
    // empty table cell
    $output .= "<td></td>";

    $columns = $this->getColumnHeaders();
    foreach ($columns as $column) {
      // the column headers
      $output .= $this->outputColumnHeader($column);
    }
    $output .= "</tr>";
    $output .= "</thead>";

    // body of the table
    $output .= "<tbody>";

    $rows = $this->getColumnHeaders();
    $relations = $this->getRelations();
    foreach ($rows as $row) {
      $output .= "<tr>";
      // the row headers
      $output .= $this->outputRowHeader($column);

      foreach ($columns as $column) {
        // the main table cells
        $output .= $this->outputRelation($relations[$row["name"]][$column["name"]]);
      }

      $output .= "</tr>";
    }
    $output .= "</thead>";
    $output .= "</tbody>";

    $output .= "</table>";

    return $output;
  }

  // output a column header
  protected function outputColumnHeader($column) {
    // this is the default, no frills
    return "<th>" . $column["name"] . "</th>";
  }

  protected function outputRelation($relation) {
    // this is the default, no frills
    $output = "";

    if (empty($relation)) // empty string implies that there is no relation in the database
      $output .= "<td></td>";
    else
      $output .= "<td>&#x2713;</td>";

    return $output;
  }

  // output a row header
  protected function outputRowHeader($row) {
    // this is the default, no frills
    return "<th>" . $row["name"] . "</th>";
  }

  // get the elements in the column headers
  private function getColumnHeaders() {
    // TODO write database handling code: return an array of database rows (all fields!)
  }

  // get the elements in the row headers
  private function getRowHeaders() {
    // TODO write database handling code: return an array of database rows (all fields!)
  }

  // get the relations for the comparison table
  private function getRelations() {
    // TODO write database handling code: return a two-dimensional hash-map [row, column] which at position [i, j] has the database row corresponding to [i, j] (or is empty)
  }
}

// config for the table
$config = array("tableName" => "properties-morphism-preservation");

/**
 * Instance of a comparison table
 *
 * This table indicates which properties of morphisms are preserved by which operations
 */
class PropertiesMorphismPreservationTable extends ComparisonTable {
  public function __construct() {
    global $config;
    $this->__construct($config["tableName"]);
  }

  protected function outputRelation($relation) {
    // TODO handle the different scenarios: false (link to example, if present), true (link to tag, if present), unknown
    return "<td></td>";
  }

  protected function outputRowHeader($row) {
    // TODO this could be moved to some external function, as I imagine that this format will be (often) reused
    return "<th><a href='" . StacksLinks::tag($row["tag"]) . "'>" . $row["name"] . "</a></th>";
  }
}

/**
 * Simple utility class providing links to the Stacks project website
 */
class StacksLinks {
  public static function tag($tag) {
    return "http://stacks.math.columbia.edu/tag/ " . $tag;
  }
}

?>
