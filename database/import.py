import json, sqlite3

# TODO write code to check whether the tag has been changed

prefix = "morphism-properties-preservation"
database = "stacks.sqlite"

rowsFile = "properties.json"
columnsFile = "preservation.json"
relationsFile = "properties-preservation.json"

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
  

# import rows from the JSON file
def importRows():
  f = open(rowsFile)
  rows = json.load(f)

  # TODO we should use named fields etc
  for name in rows.keys():
    if not rowExists(name):
      createRow(name)

# import rows from the JSON file
def importColumns():
  f = open(columnsFile)
  columns = json.load(f)

  # TODO we should use named fields etc
  for name in columns.keys():
    if not columnExists(name):
      createColumn(name)

# import relations from the JSON file
def importRelations():
  f = open(relationsFile)
  relations = json.load(f)

  for relation in relations:
    if not relationExists(relation["row"], relation["column"]):
      createRelation(relation["row"], relation["column"])

    # TODO update the other fields

# actual execution code
global connection
(connection, cursor) = connect()

#importRows()
#importColumns()
importRelations()

close(connection)
