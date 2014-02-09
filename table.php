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
    print "nooo";
    return "<th>" . $row["name"] . "</th>";
  }

  // get the elements in the column headers
  private function getColumnHeaders() {
    // TODO caching this hardly seems worth the effort?
    $sql = $this->database->prepare("SELECT name FROM [" . $this->getTableName("columns") . "]");
    if ($sql->execute())
      return $sql->fetchAll();

    return array();
  }

  // get the elements in the row headers
  private function getRowHeaders() {
    // TODO caching this hardly seems worth the effort?
    $sql = $this->database->prepare("SELECT * FROM [" . $this->getTablename("rows") . "]");
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
    // TODO handle the different scenarios: false (link to example, if present), true (link to tag, if present), unknown
    $output = "";

    if (empty($relation)) // empty string implies that there is no relation in the database
      $output .= "<td></td>";
    else
      $output .= "<td data-tag='" . $relation["tag"] . "'><a href='http://stacks.math.columbia.edu/tag/" . $relation["tag"] . "'>&#x2713;</a></td>";
      // TODO this could be moved to some external function, as I imagine that this format will be (often) reused

    return $output;
  }

  protected function outputRowHeader($row) {
    // TODO this could be moved to some external function, as I imagine that this format will be (often) reused
    return "<th data-tag='" . $row["tag"] . "'><a href='" . StacksLinks::tag($row["tag"]) . "'>" . $row["name"] . "</a></th>";
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
