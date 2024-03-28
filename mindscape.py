# import pyodbc
# import logging

# # Configure logging
# logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

# # Connect to the source database (MarianneDB)
# logging.debug('Connecting to source database (MarianneDB)')
# source_conn = pyodbc.connect(
#     r'Driver={SQL Server};'
#     r'Server=DESKTOP-NA9IB30;'
#     r'Database=MarianneDB;'
#     r'Trusted_Connection=yes;'
# )
# source_cursor = source_conn.cursor()

# # Connect to the target database (moodlefyp)
# logging.debug('Connecting to target database (moodlefyp)')
# target_conn = pyodbc.connect(
#     r'Driver={SQL Server};'
#     r'Server=DESKTOP-NA9IB30;'
#     r'Database=moodlefyp;'
#     r'Trusted_Connection=yes;'
# )
# target_cursor = target_conn.cursor()

# # Fetch data from the source student table
# logging.debug('Fetching data from source student table')
# source_cursor.execute("SELECT fName, lName, phone FROM [dbo].[student]")
# student_data = source_cursor.fetchall()
# logging.debug(f'Fetched {len(student_data)} rows from source student table')

# # Create the target user table if it doesn't exist
# logging.debug('Creating target user table if it does not exist')
# target_cursor.execute("""
#     IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='user' and xtype='U')
#     CREATE TABLE [dbo].[user] (
#         fname VARCHAR(50),
#         lname VARCHAR(50),
#         phone VARCHAR(20)
#     )
# """)

# # Insert data into the target user table, skipping duplicates
# logging.debug('Inserting data into target user table, skipping duplicates')
# insert_query = """
#     INSERT INTO [dbo].[user] (fname, lname, phone)
#     SELECT s.fName, s.lName, s.phone
#     FROM (
#         VALUES %s
#     ) s(fName, lName, phone)
#     LEFT JOIN [dbo].[user] u
#         ON s.fName = u.fname
#         AND s.lName = u.lname
#         AND s.phone = u.phone
#     WHERE u.fname IS NULL
# """ % ', '.join(["(?, ?, ?)"] * len(student_data))

# target_cursor.execute(insert_query, [item for row in student_data for item in row])

# # Commit changes and close connections
# logging.debug('Committing changes and closing connections')
# target_conn.commit()
# source_conn.close()
# target_conn.close()
# logging.debug('Script execution completed')

import logging
import bcrypt
import mysql.connector

# Configure logging
logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

# Connect to databases
logging.debug('Connecting to source database (mindscapes)')
source_conn = mysql.connector.connect(host="localhost", user="root", password="", database="mindscapes")
source_cursor = source_conn.cursor()

logging.debug('Connecting to Moodle database (moodlefyp)')
target_conn = mysql.connector.connect(host="localhost", user="root", password="", database="moodlefyp")
target_cursor = target_conn.cursor()

# Define role IDs (replace these with the actual role IDs from your Moodle database)
STUDENT_ROLE_ID = 5  # Example ID, replace with actual student role ID
TEACHER_ROLE_ID = 4  # Example ID, replace with actual non-editing teacher role ID

def insert_users_roles(user_data, role_id):
    user_data_to_export = []

    for user in user_data:
        username, email, password, firstname, lastname = user
        password_hash = bcrypt.hashpw(password.encode('utf-8'), bcrypt.gensalt())

        # Insert into mdl_user
        insert_query = """
        INSERT INTO `mdl_user` (username, password, firstname, lastname, email, mnethostid)
        VALUES (%s, %s, %s, %s, %s, 1)
        ON DUPLICATE KEY UPDATE id=id
        """
        target_cursor.execute(insert_query, (username, password_hash, firstname, lastname, email, 1))
        user_id = target_cursor.lastrowid

        # Insert into mdl_role_assignments
        role_assign_query = """
        INSERT INTO `mdl_role_assignments` (roleid, contextid, userid, modifierid, timemodified)
        VALUES (%s, 1, %s, 2, UNIX_TIMESTAMP())
        """
        target_cursor.execute(role_assign_query, (role_id, user_id))

        user_data_to_export.append((username, password, firstname, lastname, email))

    # Export user data to a file
    with open(f'exported_users_role_{role_id}.txt', 'w') as file:
        for data in user_data_to_export:
            file.write(f"{data[0]},{data[1]},{data[2]},{data[3]},{data[4]}\n")

# Fetch and process student data
logging.debug('Processing student data')
source_cursor.execute("SELECT fName, lName, phone FROM `student`")
student_data = [(student[0].lower() + student[1].lower(), f"{student[0].lower() + student[1].lower()}@example.com", student[0].lower() + "123", student[0], student[1]) for student in source_cursor.fetchall()]
insert_users_roles(student_data, STUDENT_ROLE_ID)

# Fetch and process teacher data
logging.debug('Processing teacher data')
source_cursor.execute("SELECT fName, lName, phone FROM `teacher`")
teacher_data = [(teacher[0].lower() + teacher[1].lower(), f"{teacher[0].lower() + teacher[1].lower()}@example.com", teacher[0].lower() + "123", teacher[0], teacher[1]) for teacher in source_cursor.fetchall()]
insert_users_roles(teacher_data, TEACHER_ROLE_ID)

# Commit changes and close connections
logging.debug('Committing changes and closing connections')
target_conn.commit()
source_conn.close()
target_conn.close()
logging.debug('Script execution completed')