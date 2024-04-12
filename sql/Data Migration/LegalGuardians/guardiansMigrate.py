from datetime import datetime, date  # Import the date class as well
import logging
import mysql.connector
import pandas as pd

# Configure logging
logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

# Connect to the source database (mindscapes)
logging.debug('Connecting to source database (mindscapes)')
source_conn = mysql.connector.connect(host="localhost", user="root", password="", port = 3308, database="mindscapes")
source_cursor = source_conn.cursor()

# Connect to the Moodle database (moodlefyp)
logging.debug('Connecting to Moodle database (moodlefyp)')
target_conn = mysql.connector.connect(host="localhost", user="root", password="", port = 3308, database="moodlefyp")
target_cursor = target_conn.cursor()

# Initialize a list to store legal guardian data for the Excel export
guardians_for_excel = []

def map_student_ids(student_first_name, student_last_name, phone_nb):
    target_cursor.execute("""
        SELECT id FROM mdl_user WHERE 
        firstname = %s AND lastname = %s AND 
        (phone1 = %s OR phone2 = %s)
    """, (student_first_name, student_last_name, phone_nb, phone_nb))
    
    result = target_cursor.fetchone()
    if result:
        logging.debug(f"Found Moodle user ID {result[0]} for {student_first_name} {student_last_name}")
    else:
        logging.debug(f"No Moodle user ID found for {student_first_name} {student_last_name}, Phone: {phone_nb}")
    return result[0] if result else None

def insert_legal_guardians():
    source_cursor.execute("SELECT idLegalGuardians, nameLegalGuardians, phoneNumber, address FROM legal_guardians")
    legal_guardians = source_cursor.fetchall()

    for lg in legal_guardians:
        idLegalGuardians, nameLegalGuardians, phoneNumber, address = lg

        target_cursor.execute("SELECT guardianid FROM mdl_legalguardians WHERE namelegalguardians = %s AND phonenumber = %s", (nameLegalGuardians, phoneNumber,))
        result = target_cursor.fetchone()
        if result:
            logging.debug(f"Legal guardian {nameLegalGuardians} already exists. Using existing guardianid.")
            guardian_id = result[0]
        else:
            target_cursor.execute("INSERT INTO mdl_legalguardians (namelegalguardians, phonenumber, address) VALUES (%s, %s, %s)", (nameLegalGuardians, phoneNumber, address))
            guardian_id = target_cursor.lastrowid
            target_conn.commit()

        source_cursor.execute("""
            SELECT s.idStudent, s.studentFirstName, s.studentLastName, s.phoneNb, gs.relationShip 
            FROM guardians_students gs
            JOIN student s ON gs.idStudent = s.idStudent
            WHERE gs.idLegalGuardians = %s
        """, (idLegalGuardians,))
        
        relationships = source_cursor.fetchall()
        for idStudent, student_first_name, student_last_name, phone_nb, relation in relationships:
            new_id_student = map_student_ids(student_first_name, student_last_name, phone_nb)
            if new_id_student:
                target_cursor.execute("SELECT idguardiansstudents FROM mdl_legalguardians_relationships WHERE guardianid = %s AND userid = %s", (guardian_id, new_id_student))
                if not target_cursor.fetchone():
                    target_cursor.execute("INSERT INTO mdl_legalguardians_relationships (guardianid, relationship, userid) VALUES (%s, %s, %s)", (guardian_id, relation, new_id_student))
                    logging.debug(f"Inserted relationship for guardian {guardian_id} and Moodle user {new_id_student}")
                    target_conn.commit()
                    # Add data to the list for Excel export
                    guardians_for_excel.append([nameLegalGuardians, phoneNumber, address, f"{student_first_name} {student_last_name}", relation])
                else:
                    logging.debug(f"Relationship for guardian {guardian_id} and Moodle user {new_id_student} already exists. Skipping.")
            else:
                logging.debug(f"Failed to insert relationship for {student_first_name} {student_last_name} due to missing Moodle user ID")

insert_legal_guardians()

# Export to Excel
df = pd.DataFrame(guardians_for_excel, columns=['Guardian Name', 'Phone Number', 'Address', 'Student Full Name', 'Relationship'])
df.to_excel('exported_legal_guardians.xlsx', index=False)

source_conn.close()
target_conn.close()
logging.debug('Legal guardians migration and export completed.')
