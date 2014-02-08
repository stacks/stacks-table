# this script creates an empty database
# after creation it should be placed in a directory with the correct chmod

import os.path, sqlite3, sys

def execute(filename):
  query = open(filename, "r").read()
  cursor = connection.cursor()
  cursor.execute(query)

tables = ["properties.sql", "situations.sql", "property_situation.sql"]

if os.path.isfile("properties.sqlite"):
  print "The file properties.sqlite already exists in this folder, aborting"
  sys.exit()

print "Creating the database in properties.sqlite"

connection = sqlite3.connect("properties.sqlite")

map(execute, tables)

connection.commit()
connection.close()

print "The database has been created"
