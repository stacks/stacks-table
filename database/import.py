import collections, json, sqlite3

# TODO write code to check whether the tag has been changed

prefix = "morphism-properties-preservation"
#prefix = "derived-categories-preservation"
database = "stacks.sqlite"

# close the connection
def close(connection):
  connection.commit()

# open the connection
def connect():
  connection = sqlite3.connect(database)

  return (connection, connection.cursor())

# create a row header in the database
def createRow(name):
  assert not rowExists(name)

  print "Creating the row header", name

  try:
    query = "INSERT INTO [" + prefix + "-rows] (name) VALUES (?)"
    cursor.execute(query, (name,))

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

# create a relation in the database
def createRelation(rowName, columnName):
  assert not relationExists(rowName, columnName)

  print "Creating a relation between the row", rowName, "and the column", columnName

  try:
    rowID = getRowByName(rowName)[0]
    columnID = getColumnByName(columnName)[0]

    query = "INSERT INTO [" + prefix + "-relations] (row, column) VALUES (?, ?)"
    cursor.execute(query, (rowID, columnID))

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

# create a column in the database
def createColumn(name):
  assert not columnExists(name)

  print "Creating the column named", name

  try:
    query = "INSERT INTO [" + prefix + "-columns] (name) VALUES (?)"
    cursor.execute(query, (name,))

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

# get the row with a given name
def getRowByName(name):
  assert rowExists(name)

  try:
    query = "SELECT * FROM [" + prefix + "-rows] WHERE name = ?"
    result = connection.execute(query, (name,))

    return result.fetchone()

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# get the column with a given name
def getColumnByName(name):
  assert columnExists(name)

  try:
    query = "SELECT * FROM [" + prefix + "-columns] WHERE name = ?"
    result = connection.execute(query, (name,))

    return result.fetchone()

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# get the relationship between a row and a column
def getRelation(rowName, columnName):
  assert relationExists(rowName, columnName)

  try:
    rowID = getRowByName(rowName)[0]
    columnID = getColumnByName(columnName)[0]

    query = "SELECT * FROM [" + prefix + "-relations] WHERE property = ? AND situation = ?"
    result = connection.execute(query, (rowID, columnID))

    return result.fetchone()

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# get the column with a given name
def getColumn(name):
  assert columnExists(name)

  try:
    query = "SELECT * FROM [" + prefix + "-columns] WHERE name = ?"
    result = connection.execute(query, (name,))

    return result.fetchone()

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# check whether a row exists in the database
def rowExists(name):
  try:
    query = "SELECT COUNT(*) FROM [" + prefix + "-rows] WHERE name = ?"
    result = connection.execute(query, (name,))

    return result.fetchone()[0]

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# check whether a column exists in the database
def columnExists(name):
  try:
    query = "SELECT COUNT(*) FROM [" + prefix + "-columns] WHERE name = ?"
    result = connection.execute(query, (name,))

    return result.fetchone()[0]

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# check whether a relation exists in the database
def relationExists(rowName, columnName):
  assert rowExists(rowName)
  assert columnExists(columnName)

  rowID = getRowByName(rowName)[0]
  columnID = getColumnByName(columnName)[0]

  try:
    query = "SELECT COUNT(*) FROM [" + prefix + "-relations] WHERE row = ? AND column = ?"
    result = connection.execute(query, (rowID, columnID))

    return result.fetchone()[0]

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False
  
# update a field in one of the columns
def updateField(table, key, field, value):
  assert table in ["columns", "relations", "rows"]

  if table == "relations":
    assert relationExists(key[0], key[1])

    rowID = getRowByName(key[0])[0]
    columnID = getColumnByName(key[1])[0]

    query = "UPDATE [" + prefix + "-relations] SET " + field + "='" + value + "' WHERE row = ? AND column = ?"
    connection.execute(query, (rowID, columnID))
  else:
    if table == "columns":
      assert columnExists(key)
    else:
      assert rowExists(key)

    query = "UPDATE [" + prefix + "-" + table + "] SET " + field + "='" + value + "' WHERE name = ?"
    connection.execute(query, (key,))

# import rows from the JSON file
def importRows():
  f = open(prefix + "/rows.json")
  rows = json.load(f, object_pairs_hook=collections.OrderedDict)

  for name in rows.keys():
    if not rowExists(name):
      createRow(name)

    for field in rows[name].keys():
      updateField("rows", name, field, rows[name][field])

# import rows from the JSON file
def importColumns():
  f = open(prefix + "/columns.json")
  columns = json.load(f, object_pairs_hook=collections.OrderedDict)
  
  for name in columns.keys():
    if not columnExists(name):
      createColumn(name)

    for field in columns[name].keys():
      updateField("columns", name, field, columns[name][field])

# import relations from the JSON file
def importRelations():
  f = open(prefix + "/relations.json")
  relations = json.load(f)

  for relation in relations:
    if not relationExists(relation["row"], relation["column"]):
      createRelation(relation["row"], relation["column"])

    for field in relation.keys():
      if field not in ["column", "row"]:
        updateField("relations", (relation["row"], relation["column"]), field, relation[field])

# actual execution code
global connection
(connection, cursor) = connect()

importRows()
importColumns()
importRelations()

close(connection)
