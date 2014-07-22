<?php

/**
 * Base class for a comparison table
 */
class ComparisonTable {
  protected $database; // reference to the database handler
  protected $tablePrefix = ""; // the prefix of the tables in the database

  // constructor
  public function __construct($database, $tablePrefix) {
    $this->database = $database;
    $this->tablePrefix = $tablePrefix;
  }

  // this function returns a selector for comments (this doesn't make sense without the table)
  public function outputSelector() {
    $output = "";

    $output .= "<h2>Select the columns</h2>";
    $output .= "<form class='selector' id='" . $this->tablePrefix . "-selector'>";

    $columns = $this->getColumnHeaders();
    foreach ($columns as $column)
      $output .= "<label><input type='checkbox' checked value='" . $column["name"] . "'>" . $column["name"] . "</label>";

    $output .= "</form>";

    return $output;
  }

  // this function returns the whole table (TODO would __toString be reasonable for this?)
  public function outputTable() {
    $output = "";

    $output .= "<table id='" . $this->tablePrefix . "-table'>";

    // header of the table
    $output .= "<thead>";
    $output .= "<tr>";
    // empty table cell
    $output .= "<th></th>";

    $columns = $this->getColumnHeaders();
    foreach ($columns as $column) {
      // the column headers
      $output .= $this->outputColumnHeader($column);
    }
    $output .= "</tr>";
    $output .= "</thead>";

    // body of the table
    $output .= "<tbody>";

    $rows = $this->getRowHeaders();
    $relations = $this->getRelations();
    foreach ($rows as $row) {
      $output .= "<tr>";
      // the row headers
      $output .= $this->outputRowHeader($row);

      foreach ($columns as $column) {
        // the main table cells
        $output .= $this->outputRelation($relations[$row["name"]][$column["name"]]);
      }

      $output .= "</tr>";
    }
    $output .= "</tbody>";

    $output .= "</table>";

    return $output;
  }

  // output a column header
  protected function outputColumnHeader($column) {
    // this is the default, no frills
    return "<th data-name='" . $column["name"] . "'>" . $column["name"] . "</th>";
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
    // TODO caching this hardly seems worth the effort?
    $sql = $this->database->prepare("SELECT * FROM [" . $this->getTableName("columns") . "]");
    if ($sql->execute())
      return $sql->fetchAll();

    return array();
  }

  // get the elements in the row headers
  private function getRowHeaders() {
    // TODO caching this hardly seems worth the effort?
    $sql = $this->database->prepare("SELECT * FROM [" . $this->getTablename("rows") . "] ORDER BY name");
    if ($sql->execute())
      return $sql->fetchAll();

    return array();
  }

  // get the relations for the comparison table
  private function getRelations() {
    $rows = $this->getRowHeaders();
    $columns = $this->getColumnHeaders();

    $relations = array();
    // initialise the table with empty strings (= no relation)
    foreach ($rows as $row) {
      foreach ($columns as $column) {
        $relations[$row["name"]][$column["name"]] = "";
      }
    }

    $query = "";
    $query .= "SELECT [" . $this->getTableName("rows") . "].name, [" . $this->getTableName("columns") . "].name, [" . $this->getTableName("relations") . "].* ";
    $query .= "FROM [" . $this->getTableName("rows") . "], [" . $this->getTableName("columns") . "], [" . $this->getTableName("relations") . "] ";
    $query .= "WHERE [" . $this->getTableName("rows") . "].id = [" . $this->getTableName("relations") . "].row ";
    $query .= "AND [" . $this->getTableName("columns") . "].id = [" . $this->getTableName("relations") . "].column";

    $sql = $this->database->prepare($query);
    if ($sql->execute()) {
      $result = $sql->fetchAll();

      // TODO check these indices (!)
      foreach ($result as $relation)
        $relations[$relation[0]][$relation[1]] = $relation;
    }

    return $relations;
  }

  // get the table name, based on the prefix and the required table
  private function getTableName($table) {
    return $this->tablePrefix . "-" . $table;
  }

  // print the mark corresponding the status field
  protected function printMark($relation) {
    $output = "";

    // if the tag is not empty we make the symbol clickable
    if (!empty($relation["tag"]))
      $output .= "<a href='" . StacksLinks::tag($relation["tag"]) . "'>";

    // choose the correct symbol
    switch ($relation["status"]) {
      case "true":
        $output .= "&#x2713;";
        break;
      case "false":
        $output .= "&#x2717;";
        break;
      case "unknown":
        $output .= "?";
        break;
      default:
        $output .= $relation["status"];
        //exit("should not happen"); # TODO improve
    }

    // if the tag is not empty we make the symbol clickable
    if (!empty($relation["tag"]))
      $output .= "</a>";

    return $output;
  }

  // print a cell containing a check mark
  protected function printMarkCell($relation) {
    $output = "";

    // turn non-standard status into true
    if (in_array($relation["status"], array("true", "false", "unknown")))
      $class = $relation["status"];
    else
      $class = "true";

    if (!empty($relation["tag"]))
      $output .= "<td class='" . $class . "' data-tag='" . StacksLinks::tag($relation["tag"]) . "'>";
    else
      $output .= "<td class='" . $class . "'>";

    $output .= $this->printMark($relation) . "</td>"; // TODO change this to static again but I don't know syntax to call static functions in the same class...

    return $output;
  }

  // print a cell containing a definition
  protected function printDefinition($row) {
    $output = "";

    if (!empty($row["tag"]))
      $output .= "<th data-tag='" . $row["tag"] . "'><a href='" . StacksLinks::tag($row["tag"]) . "'>" . $row["name"] . "</a></th>";
    else
      $output .= "<th>" . $row["name"] . "</th>";

    return $output;
  }
}

/**
 * Instance of a comparison table
 *
 * This table indicates which properties of morphisms are preserved by which operations
 */
class MorphismPropertiesPreservationTable extends ComparisonTable {
  public function __construct($database) {
    global $config;
    parent::__construct($database, "morphism-properties-preservation");
  }

  protected function outputRelation($relation) {
    $output = "";
    
    if (empty($relation)) // empty string implies that there is no relation in the database
      $output .= "<td></td>";
    else
      $output .= parent::printMarkCell($relation);

    return $output;
  }

  protected function outputRowHeader($row) {
    return parent::printDefinition($row);
  }
}

/**
 * Simple utility class providing links to the Stacks project website
 */
class StacksLinks {
  public static function tag($tag) {
    return "http://stacks.math.columbia.edu/tag/" . $tag;
  }
}

?>
