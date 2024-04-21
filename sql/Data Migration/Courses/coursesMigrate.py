import logging
import mysql.connector
import pandas as pd

# Configure logging
logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

# Establish connections to the source and target databases
source_conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    port=3306,
    database="mindscapes"
)
source_cursor = source_conn.cursor(dictionary=True)

target_conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    port=3306,
    database="moodlefyp"
)
target_cursor = target_conn.cursor()

courses_for_excel= []

#Function to fetch course data from Moodle
def fetch_moodle_course_data():
    try:
        target_cursor.execute("SELECT * FROM mdl_course")
        return target_cursor.fetchall()
    except Exception as e:
        logging.error(f"Error occured while fetching course data from Moodle: {str(e)}")
        return[]
    
#Function to fetch course category data from Moodle
def fetch_moodle_course_categories():
    try:
        target_cursor.execute("SELECT * FROM mdl_course_categories")
        return target_cursor.fetchall()
    except Exception as e:
        logging.error(f"Error occured while fetching course categories from Moodle: {str(e)}")
        return[]

# Function to fetch course data
def fetch_course_data():
    try:
        # Fetch data from the source database (mindscapes)
        source_cursor.execute("SELECT * FROM course")
        return source_cursor.fetchall()
    except Exception as e:
        logging.error(f"Error occured while fetching course data:{str(e)}")
        return []

# Function to migrate course-related data
def migrate_course(courses_data):
    try:
        for course in courses_data:
            #Check if the course already exists in the target database based on the fullname
            target_cursor.execute("SELECT id FROM mdl_course WHERE fullname = %s", (course['fullname'],))
            existing_course = target_cursor.fetchone()
            if existing_course:
                logging.debug(f"Course '{course['fullname']}' already exists. Skipping insertion.")
                continue
            # Insert the transformed data into the target database (moodle)
            target_cursor.execute("""
                INSERT INTO mdl_course (category, fullname, shortname, startAge, endAge) 
                VALUES (%s, %s, %s, %s, %s)
                """,(
                    course['type'],
                    course['fullname'],
                    course['shortname'],
                    course['startAge'],
                    course['endAge']
                ))
        # Commit the transaction
        target_conn.commit()
        logging.debug("Course migration completed.")
    except Exception as e:
        logging.error(f"Error occurred while migrating course data: {str(e)}")

# Function to migrate course data from Moodle to mindscapes
def migrate_moodle_course_to_mindscapes(moodle_courses_data):
    try:
        for course in moodle_courses_data:
            # Check if the course already exists in the target database based on the fullname
            source_cursor.execute("SELECT id FROM course WHERE fullname = %s", (course['fullname'],))
            existing_course = source_cursor.fetchone()
            if existing_course:
                logging.debug(f"Course '{course['fullname']}' already exists in mindscapes. Skipping insertion.")
                continue
            # Insert the transformed data into the target database (mindscapes)
            source_cursor.execute("""
                INSERT INTO course (type, fullname, shortname, startAge, endAge) 
                VALUES (%s, %s, %s, %s, %s)
                """, (
                    course['type'],
                    course['fullname'],
                    course['shortname'],
                    course['startAge'],
                    course['endAge']
                ))
        # Commit the transaction
        source_conn.commit()
        logging.debug("Moodle course migration to mindscapes completed.")
    except Exception as e:
        logging.error(f"Error occurred while migrating Moodle course data to mindscapes: {str(e)}")


# Function to migrate course categories
def migrate_course_categories():
    try:
        #Fetch course categories from the source database
        source_cursor.execute("SELECT * FROM domain")
        domains = source_cursor.fetchall()

        #Iterate through the fetched course domains
        for domain in domains:
            domain_id, domain_name = domain['iddomain'], domain['type']

            #Check if the domain already exists in Moodle
            target_cursor.execute("SELECT id FROM mdl_course_categories WHERE name = %s", (domain_name,))
            existing_domain = target_cursor.fetchone()
            if existing_domain:
                logging.debug(f"Domain '{domain_name}' already exists in Moodle. Skipping insertion.")
                continue

            #Insert domain as a top-level category
            target_cursor.execute("""
                INSERT INTO mdl_course_categories (name, parent, depth, path)
                VALUES (%s, 0, 0, %s)
                """,(domain_name, f"/{domain_id}/"))
            
            # Fetch course types associated with the current domain
            source_cursor.execute("""
                SELECT * FROM course_type WHERE idDomain = %s
            """, (domain_id,))
            course_types = source_cursor.fetchall()
            
            #Iterate through the fetched course types
            for course_type in course_types:
                course_type_id, course_type_name, course_type_description = course_type['idcourseType'], course_type['type'], course_type['description']
                
                #Check if the course type already exists in Moodle
                target_cursor.execute("SELECT id FROM mdl_course_categories WHERE name = %s AND parent = (SELECT id FROM mdl_course_categories WHERE name = %s)",
                                      (course_type_name, domain_name))
                existing_course_type = target_cursor.fetchone()
                if existing_course_type:
                    logging.debug(f"Course type '{course_type_name}' already exists in Moodle. Skipping insertion.")
                    continue

                 # Insert course type as a subcategory under the domain
                target_cursor.execute("""
                    INSERT INTO mdl_course_categories (name, description, parent, depth, path)
                    SELECT %s, %s, id, 1, CONCAT(path, id)
                    FROM mdl_course_categories
                    WHERE name = %s
                """, (course_type_name, course_type_description, domain_name))

        #Commit the transaction
        target_conn.commit()
        logging.debug("Course categories migration completed.")
    except Exception as e:
        logging.error(f"Error occurred while migrating course categories: {str(e)}")

# Function to migrate course categories from Moodle to mindscapes
def migrate_moodle_categories_to_mindscapes(moodle_categories_data):
    try:
        for category in moodle_categories_data:
            # Check if the category already exists in the target database based on the name
            source_cursor.execute("SELECT id FROM domain WHERE type = %s", (category['name'],))
            existing_category = source_cursor.fetchone()
            if existing_category:
                logging.debug(f"Category '{category['name']}' already exists in mindscapes. Skipping insertion.")
                continue
            # Insert the category into the target database (mindscapes)
            source_cursor.execute("""
                INSERT INTO domain (type) 
                VALUES (%s)
                """, (
                    category['name'],
                ))
        # Commit the transaction
        source_conn.commit()
        logging.debug("Moodle category migration to mindscapes completed.")
    except Exception as e:
        logging.error(f"Error occurred while migrating Moodle category data to mindscapes: {str(e)}")


# Main function to execute migration tasks
def main():
    try:
        # Fetch course data
        courses_data = fetch_course_data()

        # Create a DataFrame from the fetched data
        df = pd.DataFrame(courses_data)

        # Save the DataFrame to a CSV file
        df.to_csv('course_data.xlsx', index=False)
        logging.debug("Course data saved to course_data.xlsx file.")

        # Migrate course data
        migrate_course(courses_data)

        # Migrate course categories
        migrate_course_categories()

    except Exception as e:
        logging.error(f"An error occurred: {str(e)}")

    finally:
        # Close database connections
        source_conn.close()
        target_conn.close()
        logging.debug("Script execution completed.")

# Execute the main function
if __name__ == "__main__":
    main()