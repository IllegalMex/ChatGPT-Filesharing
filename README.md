# Simple PHP Filesharing

The File Upload Web Application is a simple web application developed by ChatGPT that allows users to upload files to a server and generate links to those files that can then be shared. The application has an admin backend where the administrator can manage the uploaded files and configure the application.

## Features

* File upload: Users can upload files to the server.
* Unique links: Unique links are automatically generated and provided to users.
* Cookie support: The application supports cookies to allow users to find their uploaded files later.
* Password change: Users can change their password.
* Admin backend: The administrator can manage uploaded files, manage user accounts, and configure settings.

## Requirements

* PHP 7 or higher
* MySQL or another database supported by PHP
* Write permissions for the data folder to store uploaded files

## Installation

1. Download the files from GitHub.
2. Copy all files to the directory intended for your web application.
3. Import the database.sql file into your MySQL database.
4. Edit the config.php file to adjust the database information and other settings.
5. Open the index.php file in your web browser and you should see the application's homepage.

## Usage

#### Uploading files

Open the application's homepage.
Click the "Choose File" button and select the file you want to upload.
Click the "Upload" button.
A unique link to the uploaded file will be generated and displayed on the page.

#### Managing files (for administrators only)

Open the admin backend by clicking the "Admin" link.
Log in as an administrator.
You can now view, download, and delete uploaded files.

#### Changing passwords

Open the "Change Password" page.
Enter your old password and choose a new password. The default password is 'admin123' for the user 'admin'.
Click the "Change Password" button.
Your password will be updated.

## Customization

The application uses a CSS file named style.css that can be edited to customize the look and feel of the application.

## Notes

This project was created with the help of ChatGPT. It is important to ensure that no illegal content is uploaded. The author of this project (otterside) bears no liability for the use of this project by third parties.

A default password is used for the first login to the admin backend. It is strongly recommended to change the password immediately after the first login to ensure the security of the admin area.
  
## Author

This script was created by ChatGPT, a large language model based on the GPT-3.5 architecture by OpenAI.

## License

This script is released under the MIT license. A copy of the license can be found in the LICENSE file.
