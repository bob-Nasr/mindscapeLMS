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

# Function to get Moodle ID based on name from Mindscapes
def get_moodle_id(source_id, source_table, source_id_column, source_name_column, target_table, target_name_column, target_id_column):
    # Get the name from Mindscapes based on ID
    source_cursor.execute(f'SELECT {source_name_column} FROM {source_table} WHERE {source_id_column} = %s', (source_id,))
    source_name = source_cursor.fetchone()[0]

    # Get the corresponding Moodle ID based on name
    target_cursor.execute(f'SELECT {target_id_column} FROM {target_table} WHERE {target_name_column} = %s', (source_name,))
    result = target_cursor.fetchone()
    return result[0] if result else None

# Fetch education types from Mindscapes
source_cursor.execute('SELECT idEdType, EducationType_name, location_idlocation, institution_idInstitution FROM education_type')
education_types = source_cursor.fetchall()

# Create a set to store names already inserted into Moodle
inserted_names = set()

# Insert unique education types into Moodle
for idEdType, EducationType_name, location_idlocation, institution_idInstitution in education_types:
    # Check if the education type name has already been inserted into Moodle
    if EducationType_name not in inserted_names:
        
        # Check if the education type already exists in Moodle
        target_cursor.execute('SELECT education_typeid FROM mdl_education_type WHERE education_typename = %s', (EducationType_name,))
        existing_education_type = target_cursor.fetchone()
        if not existing_education_type:  # Education type does not exist in Moodle, insert it
            # Get Moodle IDs for location and institution
            moodle_location_id = get_moodle_id(location_idlocation, 'location', 'idlocation', 'locationName', 'mdl_location', 'locationname', 'locationid')
            moodle_institution_id = get_moodle_id(institution_idInstitution, 'institution', 'idInstitution', 'Institution_name', 'mdl_institution', 'institutiontype', 'institutionid')
            target_cursor.execute('INSERT INTO mdl_education_type (education_typename, location_id, institution_id) VALUES (%s, %s, %s)', (EducationType_name, moodle_location_id, moodle_institution_id))
            target_conn.commit()
            # Add the inserted name to the set
            inserted_names.add(EducationType_name)
        else:
            logging.debug(f"Education type '{EducationType_name}' already exists in Moodle. Skipping insertion.")
    else:
        logging.debug(f"Education type '{EducationType_name}' has already been inserted into Moodle. Skipping insertion.")

# Commit changes and close connections
target_conn.commit()
source_conn.close()
target_conn.close()
logging.debug('Script execution completed')
