import logging
import mysql.connector

# Configure logging
logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

# Connect to the source database (mindscapes)
logging.debug('Connecting to source database (mindscapes)')
source_conn = mysql.connector.connect(host="localhost", user="root", password="", port="3308", database="mindscapes")
source_cursor = source_conn.cursor()

# Connect to the Moodle database (moodlefyp)
logging.debug('Connecting to Moodle database (moodlefyp)')
target_conn = mysql.connector.connect(host="localhost", user="root", password="", port="3308", database="moodlefyp")
target_cursor = target_conn.cursor()

# Fetch locations from Mindscapes
source_cursor.execute('SELECT idlocation, locationName FROM location')
mindscapes_locations = source_cursor.fetchall()

# Insert unique locations into Moodle
for idlocation, locationName in mindscapes_locations:
    # Check if the location already exists in Moodle
    target_cursor.execute('SELECT locationid FROM mdl_location WHERE locationname = %s', (locationName,))
    existing_location = target_cursor.fetchone()
    if not existing_location:  # Location does not exist in Moodle, insert it
        target_cursor.execute('INSERT INTO mdl_location (locationname) VALUES (%s)', (locationName,))
    else:
        logging.debug(f"Location '{locationName}' already exists in Moodle. Skipping insertion.")

# Fetch institution types from Mindscapes
source_cursor.execute('SELECT idInstitution, Institution_name FROM Institution')
institution_types = source_cursor.fetchall()

# Insert unique institution types into Moodle
for idInstitution, Institution_name in institution_types:
    # Check if the institution type already exists in Moodle
    target_cursor.execute('SELECT institutionid FROM mdl_institution WHERE institutiontype = %s', (Institution_name,))
    existing_institution_type = target_cursor.fetchone()
    if not existing_institution_type:  # Institution type does not exist in Moodle, insert it
        target_cursor.execute('INSERT INTO mdl_institution (institutiontype) VALUES (%s)', (Institution_name,))
    else:
        logging.debug(f"Institution type '{Institution_name}' already exists in Moodle. Skipping insertion.")

# Commit changes and close connections
target_conn.commit()
source_conn.close()
target_conn.close()
logging.debug('Script execution completed')
