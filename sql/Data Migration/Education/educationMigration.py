import logging
import mysql.connector
import pandas as pd

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

# Function to check for duplicate education records
def is_duplicate_education(education_type_id, user_id):
    target_cursor.execute('SELECT COUNT(*) FROM mdl_education WHERE education_type_id = %s AND user_id = %s', (education_type_id, user_id))
    count = target_cursor.fetchone()[0]
    return count > 0

# Fetch education records from Mindscapes
source_cursor.execute('SELECT ideducation, startDate, endDate, Education_Type_idEdType, student_idStudent FROM education')
educations = source_cursor.fetchall()

# Create a set to store education records already inserted into Moodle
inserted_educations = set()

# Insert unique education records into Moodle
for education in educations:
    ideducation, startDate, endDate, Education_Type_idEdType, student_idStudent = education
    
    # Get Moodle ID for education type
    moodle_education_type_id = get_moodle_id(Education_Type_idEdType, 'education_type', 'idEdType', 'EductaionType_name', 'mdl_education_type', 'education_typename', 'education_typeid')
    
    # Get Moodle user ID for student
    moodle_user_id = get_moodle_id(student_idStudent, 'student', 'idStudent', 'studentFirstName', 'mdl_user', 'firstName', 'id')
    
    # Check for duplicate education record
    if (moodle_education_type_id, moodle_user_id) not in inserted_educations:
        # Insert education record into Moodle if not duplicate
        if not is_duplicate_education(moodle_education_type_id, moodle_user_id):
            target_cursor.execute('INSERT INTO mdl_education (start_date, end_date, education_type_id, user_id) VALUES (%s, %s, %s, %s)', (startDate, endDate, moodle_education_type_id, moodle_user_id))
            target_conn.commit()
            # Add the inserted education record to the set
            inserted_educations.add((moodle_education_type_id, moodle_user_id))
        else:
            logging.debug(f"Duplicate education record found for education type ID '{moodle_education_type_id}' and user ID '{moodle_user_id}'. Skipping insertion.")
    else:
        logging.debug(f"Education record for education type ID '{moodle_education_type_id}' and user ID '{moodle_user_id}' has already been inserted into Moodle. Skipping insertion.")

# Commit changes and close connections
target_conn.commit()
source_conn.close()
target_conn.close()
logging.debug('Script execution completed')
