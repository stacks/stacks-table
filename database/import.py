import json, sqlite3

# close the connection
def close(connection):
  connection.commit()

# open the connection
def connect():
  connection = sqlite3.connect("properties.sqlite")

  return (connection, connection.cursor())

# create a property in the database
def createProperty(name, tag):
  assert not propertyExists(name)

  print "Creating the property named", name

  try:
    query = "INSERT INTO properties (name, tag) VALUES (?, ?)"
    cursor.execute(query, (name, tag))

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

# create a relation in the database
def createRelation(propertyName, situationName, tag):
  assert not relationExists(propertyName, situationName)

  print "Creating a relation between the property", propertyName, "and the situation", situationName

  try:
    propertyID = getPropertyByName(propertyName)[0]
    situationID = getSituationByName(situationName)[0]

    query = "INSERT INTO property_situation (property, situation, tag) VALUES (?, ?, ?)"
    cursor.execute(query, (propertyID, situationID, tag))

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

# create a situation in the database
def createSituation(name):
  assert not situationExists(name)

  print "Creating the situation named", name

  try:
    query = "INSERT INTO situations (name) VALUES (?)"
    cursor.execute(query, (name,))

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

# get the property with a given name
def getPropertyByName(name):
  assert propertyExists(name)

  try:
    query = "SELECT id, name, tag FROM properties WHERE name = ?"
    result = connection.execute(query, (name,))

    return result.fetchone()

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# get the situation with a given name
def getSituationByName(name):
  assert situationExists(name)

  try:
    query = "SELECT id, name FROM situations WHERE name = ?"
    result = connection.execute(query, (name,))

    return result.fetchone()

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# get the relationship between a property and a situation
def getRelation(propertyName, situationName):
  assert relationExists(propertyName, situationName)

  try:
    propertyID = getPropertyByName(propertyName)[0]
    situationID = getSituationByName(situationName)[0]

    query = "SELECT property, situation, tag FROM property_situation WHERE property = ? AND situation = ?"
    result = connection.execute(query, (propertyID, situationID))

    return result.fetchone()

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# get the situation with a given name
def getSituation(name):
  assert situationExists(name)

  try:
    query = "SELECT name FROM situations WHERE name = ?"
    result = connection.execute(query, (name,))

    return result.fetchone()

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# check whether a property exists in the database
def propertyExists(name):
  try:
    query = "SELECT COUNT(*) FROM properties WHERE name = ?"
    result = connection.execute(query, (name,))

    return result.fetchone()[0]

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# check whether a situation exists in the database
def situationExists(name):
  try:
    query = "SELECT COUNT(*) FROM situations WHERE name = ?"
    result = connection.execute(query, (name,))

    return result.fetchone()[0]

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False

# check whether a relation exists in the database
def relationExists(propertyName, situationName):
  assert propertyExists(propertyName)
  assert situationExists(situationName)

  propertyID = getPropertyByName(propertyName)[0]
  situationID = getSituationByName(situationName)[0]

  try:
    query = "SELECT COUNT(*) FROM property_situation WHERE property = ? AND situation = ?"
    result = connection.execute(query, (propertyID, situationID))

    return result.fetchone()[0]

  except sqlite3.Error, e:
    print "An error occurred:", e.args[0]

  return False
  

# import properties from the JSON file
def importProperties():
  f = open("properties.json")
  properties = json.load(f)

  for (name, tag) in properties:
    if not propertyExists(name):
      createProperty(name, tag)

# import property-situation relations from the JSON file
def importRelations():
  f = open("property_situation.json")
  relations = json.load(f)

  for situation in relations:
    for relation in relations[situation]:
      if not relationExists(relation[0], situation):
        createRelation(relation[0], situation, relation[1])

# import situations from the JSON file
def importSituations():
  f = open("property_situation.json")
  situations = json.load(f).keys()

  for name in situations:
    if not situationExists(name):
      createSituation(name)



# actual execution code
global connection
(connection, cursor) = connect()

importProperties()
importSituations()
importRelations()

close(connection)
