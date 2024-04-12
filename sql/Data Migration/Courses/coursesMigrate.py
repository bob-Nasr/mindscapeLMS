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
    port=3308,
    database="mindscapes"
)
source_cursor = source_conn.cursor(dictionary=True)

target_conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    port=3308,
    database="moodlefyp"
)
target_cursor = target_conn.cursor()

courses_for_excel= []

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
            # Insert the transformed data into the target database (moodle)
            target_cursor.execute("""
                INSERT INTO mdl_course (category, fullname, startAge, endAge) 
                VALUES (%s, %s, %s, %s)
                """,(
                    course['category'],
                    course['fullname'],
                    course['startAge'],
                    course['endAge']
                ))
        # Commit the transaction
        target_conn.commit()
        logging.debug("Course migration completed.")
    except Exception as e:
        logging.error(f"Error occurred while migrating course data: {str(e)}")

# Function to migrate course categories
def migrate_course_categories():
    try:
        #Fetch course categories from the source database
        source_cursor.execute("SELECT * FROM domain")
        domains = source_cursor.fetchall()

        #Iterate through the fetched course domains
        for domain in domains:
            domain_id, domain_name = domain['iddomain'], domain['type']

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
                course_type_id, course_type_name = course_type['idcourseType'], course_type['type']

                #Insert course type as a subcategory under the domain
                target_cursor.execute("""
                    INSERT INTO mdl_course_categories (name, parent, depth, path)
                    VALUES(%s, %s, 1, %s)
                """,(course_type_name, target_cursor.lastrowid,f"/{domain_id}/{course_type_id}/"))
        
        #Commit the transaction
        target_conn.commit()
        logging.debug("Course categories migration completed.")
    except Exception as e:
        logging.error(f"Error occurred while migrating course categories: {str(e)}")

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