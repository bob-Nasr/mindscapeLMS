import logging
import mysql.connector
import bcrypt
import secrets # For passwords
import string
import pandas as pd  # For excel

# Configure logging
logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

# Connect to the source database (mindscapes)
logging.debug('Connecting to source database (mindscapes)')
source_conn = mysql.connector.connect(host="localhost", user="root", password="", database="mindscapes")
source_cursor = source_conn.cursor()

# Connect to the Moodle database (moodlefyp)
logging.debug('Connecting to Moodle database (moodlefyp)')
target_conn = mysql.connector.connect(host="localhost", user="root", password="", database="moodlefyp")
target_cursor = target_conn.cursor()

# Initialize a list to store user data for the Excel export
users_for_excel = []

# Generate a random password
def generate_password(length=12):
    alphabet = string.ascii_letters + string.digits + string.punctuation
    password = ''.join(secrets.choice(alphabet) for i in range(length))
    return password

def insert_users_with_dynamic_role_check(user_data):
    for user in user_data:
        username, email, firstName, lastName, role_name = user
        plain_password = generate_password()

        # Check if the role exists in Moodle and get its ID
        check_role_sql = "SELECT id FROM mdl_role WHERE shortname = %s"
        target_cursor.execute(check_role_sql, (role_name,))
        role_result = target_cursor.fetchone()
        
        if not role_result:
            logging.debug(f"Role {role_name} not found in Moodle. Skipping user {username}.")
            continue

        role_id = role_result[0]

        # Check if user already exists in Moodle
        check_user_sql = "SELECT id FROM mdl_user WHERE username = %s"
        target_cursor.execute(check_user_sql, (username,))
        if target_cursor.fetchone():
            logging.debug(f"User {username} already exists. Skipping.")
        else:
            # Add user details to the list before inserting them into the database
            users_for_excel.append([username, plain_password, firstName, lastName, email, role_name])

            password_hash = bcrypt.hashpw(plain_password.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')

            # Insert user into Moodle database with confirmed and mnethostid set to 1
            insert_user_sql = """
            INSERT INTO mdl_user (username, password, firstname, lastname, email, confirmed, mnethostid)
            VALUES (%s, %s, %s, %s, %s, 1, 1)
            """
            target_cursor.execute(insert_user_sql, (username, password_hash, firstName, lastName, email))

            user_id = target_cursor.lastrowid

            # Assign role to user
            insert_role_sql = """
            INSERT INTO mdl_role_assignments (roleid, userid, contextid, timemodified, modifierid)
            VALUES (%s, %s, 1, UNIX_TIMESTAMP(), 2)
            """
            target_cursor.execute(insert_role_sql, (role_id, user_id))

    target_conn.commit()

# Combine students and employees into one dataset and process
logging.debug('Combining and processing student and employee data with role check')

# Fetch and combine student data
source_cursor.execute("""
    SELECT LOWER(CONCAT(studentFirstName, studentLastName)), CONCAT(LOWER(studentFirstName), '@example.com'), studentFirstName, studentLastName, 'student' AS role_name
    FROM Student
""")
student_data = source_cursor.fetchall()

# Fetch and combine employee data
source_cursor.execute("""
    SELECT LOWER(CONCAT(first_name, last_name)), email, first_name, last_name, (
        SELECT role FROM Employee_Role WHERE idemployee_role = Employee.role
    ) AS role_name
    FROM Employee
""")
employee_data = source_cursor.fetchall()

# Combine both datasets
combined_data = student_data + employee_data

# Process combined data
insert_users_with_dynamic_role_check(combined_data)

# After processing all users, create a pandas DataFrame and export it to an Excel file
df = pd.DataFrame(users_for_excel, columns=['Username', 'Password', 'First Name', 'Last Name', 'Email', 'Role'])
df.to_excel('exported_users.xlsx', index=False)

# Close connections
source_conn.close()
target_conn.close()
logging.debug('Script execution completed')
