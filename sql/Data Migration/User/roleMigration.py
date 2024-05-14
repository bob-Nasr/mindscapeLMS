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
target_conn = mysql.connector.connect(host="localhost", user="root", password="",port="3308", database="moodlefyp")
target_cursor = target_conn.cursor()

# Fetch roles from Mindscape
source_cursor.execute('SELECT idemployee_role, role FROM employee_role')
mindscape_roles = source_cursor.fetchall()

# Identify the maximum roleid in Moodle to continue the hierarchy
target_cursor.execute('SELECT MAX(id) FROM mdl_role')
max_roleid = target_cursor.fetchone()[0] or 0

# Insert unique roles into Moodle
for idemployee_role, role in mindscape_roles:
    target_cursor.execute('SELECT id FROM mdl_role WHERE shortname = %s', (role,))
    if target_cursor.fetchone() is None:  # Check for duplicate roleshortname
        max_roleid += 1
        target_cursor.execute('INSERT INTO mdl_role (id, shortname, sortorder, archetype) VALUES (%s, %s, %s, %s)', (max_roleid, role, max_roleid, role))
    else:
        logging.debug(f"Role {role} already exists. Skipping.")

# Commit changes and close connections
target_conn.commit()
source_conn.close()
target_conn.close()
logging.debug('Script execution completed')
