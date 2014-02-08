<?php

function getProperties() {
  global $database;

  $sql = $database->prepare("SELECT name, tag FROM properties");
  if ($sql->execute())
    return $sql->fetchAll();

  return array();
}

function getRelations() {
  global $database;

  $properties = getProperties();
  $situations = getSituations();
  $relations = array();
  foreach ($properties as $property) {
    foreach ($situations as $situation) {
      $relations[$property["name"]][$situation["name"]] = "";
    }
  }

  $sql = $database->prepare("SELECT properties.name, situations.name, property_situation.tag FROM properties, situations, property_situation WHERE properties.id = property_situation.property AND situations.id = property_situation.situation");
  if ($sql->execute()) {
    $result = $sql->fetchAll();

    foreach ($result as $relation)
      $relations[$relation[0]][$relation[1]] = $relation[2];

    return $relations;
  }

  return array();
}

function getSituations() {
  global $database;

  $sql = $database->prepare("SELECT name FROM situations");
  if ($sql->execute())
    return $sql->fetchAll();

  return array();
}

?>
