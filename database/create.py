# this script creates an empty database
# after creation it should be placed in a directory with the correct chmod

import os.path, sqlite3, sys

# configuration variables
prefix = "morphism-properties-preservation"
#prefix = "derived-categories-preservation"
database = "stacks.sqlite"

def execute(filename):
  query = open(filename, "r").read()
  # we replace prefix by the actual prefix
  query = query.replace("prefix", prefix)

  cursor = connection.cursor()
  cursor.executescript(query)

tables = ["rows.sql", "columns.sql", "relations.sql"]
tables = tables + [prefix + "/fields.sql"] # this file will contain the extra fields

# TODO check existence of the tables, not of the .sqlite file
#if os.path.isfile("properties.sqlite"):
#  print "The file properties.sqlite already exists in this folder, aborting"
#  sys.exit()

print "Creating the tables with prefix '" + prefix + "' in '" + database + "'"

connection = sqlite3.connect(database)

map(execute, tables)

connection.commit()
connection.close()

print "The tables have been created"
