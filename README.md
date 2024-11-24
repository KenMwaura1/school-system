
# School Management System

## Overview

The School Management System is a web-based application designed to manage various aspects of a school's operations, including student information, subject assignments, and user authentication. This system provides a user-friendly interface for students, teachers, and administrators to interact with the school's database and perform necessary tasks efficiently.

## Features

- **User Authentication**: Secure login and registration for students, teachers, and administrators.
- **Role-Based Access Control**: Different access levels for students, teachers, and administrators to ensure data security and appropriate access.
- **Student Dashboard**: Personalized dashboard for students to view their subjects, assignments, and grades.
- **Subject Assignment**: Admin functionality to assign subjects to students and manage subject details.
- **Assignment Management**: Students can view and submit assignments, and teachers can grade and provide feedback.
- **Responsive Design**: User-friendly interface that works seamlessly on desktops, tablets, and mobile devices.

## Technologies Used

- **Frontend**: HTML, CSS (Tailwind CSS, DaisyUI), JavaScript (jQuery)
- **Backend**: PHP
- **Database**: MySQL
- **Session Management**: PHP Sessions

## Installation

1. **Clone the repository**:
    ```bash
    git clone https://github.com/KenMwaura1/school-system.git
    cd school-management-system
    ```

2. **Set up the database**:
    - Create a MySQL database named `students`.
    - Import the provided SQL file (`database.sql`) to set up the necessary tables and initial data.

3. **Configure the database connection**:
    - Update the `db_connection.php` file with your database credentials.

4. **Run the application**:
    - Use a local server environment like XAMPP, WAMP, or MAMP to run the application.
    - Open your browser and navigate to `http://localhost/school-system`.

## Usage

- **Login**: Use the login page to access the system. Different roles will have different access levels.
- **Dashboard**: After logging in, users will be redirected to their respective dashboards.
- **Assign Subjects**: Admins can assign subjects to students using the form provided.
- **Manage Assignments**: Students can view and submit assignments, and teachers can grade them.

## Contributing

Contributions are welcome! Please fork the repository and create a pull request with your changes. Ensure that your code follows the project's coding standards and includes appropriate tests.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Contact

For any questions or suggestions, please open an issue or contact me.
