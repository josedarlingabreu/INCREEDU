# INCREEDU

INCREEDU is a student request management dashboard designed to help educational institutions organize, track, and manage student applications efficiently.

### Dashboard

<img width="1335" height="662" alt="INCREEDU dashboard" src="https://github.com/user-attachments/assets/a0a58d1e-a53d-4cbe-ae8a-07a99b777768" />

## Features

- Google authentication
- Create, edit, and delete student requests
- Request status management
- Dashboard statistics
- Dynamic resolution chart
- Search and filter functionality
- PDF report generation
- Light and dark mode
- Custom accent colors
- Responsive design
- MySQL database integration
- PHP API with prepared statements

## Request Statuses

Requests can have one of the following statuses:

- Pending
- Approved
- Rejected

## Technologies

- PHP
- MySQL
- JavaScript
- HTML5
- CSS3
- PDO
- Google Identity Services
- jsPDF

## Project Structure

```text
INCREEDU/
├── index.php
├── .gitignore
├── api/
│   ├── db.php
│   ├── list.php
│   ├── create.php
│   ├── update.php
│   ├── delete.php
│   └── search.php
├── css/
│   ├── style.css
│   └── components.css
└── js/
    └── app.js
