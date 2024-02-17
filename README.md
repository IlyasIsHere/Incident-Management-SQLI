# Incident Management Web Application

## Overview

This repository contains the codebase for an **incident management web application** I developed during a one-month internship at **SQLI Oujda**. The application aims to streamline incident reporting, tracking, and resolution within an organization.

## Features

1. **Real-Time Chat**: The application includes a real-time chat feature that facilitates communication between collaborators and technicians. Users can exchange messages, share updates, and collaborate effectively.

2. **File Attachments**: Users can attach files (such as screenshots, logs, or documents) related to specific incidents. This feature enhances the incident resolution process by providing additional context.

3. **Technology Stack**:
   - **Frontend**: The user interface is built using HTML, CSS, and Bootstrap. It provides an intuitive and responsive design for users.
   - **Backend**: The backend is powered by PHP based on the MVC design pattern, which handles user authentication, incident data storage, and real-time chat functionality. Real-time chat was implemented using SSEs (Server-sent events), and the Mercure hub API.
   - **Database**: MySQL is used to store incident details and chat messages.
   - **Containerization**: Docker ensures consistent and easy deployment across different environments.

## Installation

1. **Clone the Repository**:
    ``` git clone https://github.com/IlyasIsHere/Incident-Management-SQLI.git ```
2. **Setup Environment**:
    - Install Docker and ensure it's running.
3. **Build and Run**:
    - In the project directory, run the following command:
    ``` docker-compose up -d ```
4. **Access the Application**:
    - Open your web browser and navigate to `http://localhost:8080`.
    - There is some dummy data that will already be inserted in the MySQL database (dummy incidents + dummy users). To connect, you can use the emails **collab1@sqli.com**, **collab2@sqli.com**, **tech1@sqli.com**, or **tech2@sqli.com**. The password for all accounts is **abc**.

## Usage

1. **Incident Reporting**:
    - Collaborators can submit new incidents, providing details such as title, description, date, and time.
    - Attach relevant files to the incident.

2. **Real-Time Chat**:
    - Collaborators and technicians can communicate via the chat feature.
    - Messages are delivered instantly, allowing efficient collaboration.

3. **Incident Tracking**:
    - View a list of open incidents.
    - Filter incidents by status, title, or date.

4. **Incident Editing**:
    - Edit an existing incident's details.
    - Delete specific attached files.
    - Attach other files.
    - Technicians can update an incident's status.
    - Collaborators can confirm whether the incident was resolved or not, once the technician reports that he has finished fixing it.

## Contributing

Contributions are welcome! If you'd like to enhance the application, fix bugs, or add new features, follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/my-feature`).
3. Make your changes and commit them (`git commit -m "Add feature: My Feature"`).
4. Push to your fork (`git push origin feature/my-feature`).
5. Create a pull request.

## License

This project is licensed under the MIT License. Feel free to use, modify, and distribute it as needed.
  
