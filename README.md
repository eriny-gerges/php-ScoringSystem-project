
HOW TO RUN THE PROGRAM

1. Install and start XAMPP (Apache + MySQL).
2. Open phpMyAdmin.
3. Import "Program Code/database/schema.sql"  (creates the
   database, tables, views, and triggers).
4. Import "Program Code/database/sample_data.sql"  (adds the
   sample teams, individuals, events, and scores).
5. Generate a real admin password and put it in the admins table.
   In a terminal run:
       php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
   Then, in phpMyAdmin, paste the result into the password_hash
   field of the 'admin' row in the admins table.
6. Copy the contents of the "Program Code" folder into the XAMPP
   "htdocs" folder (for example htdocs/competition).
7. Open the site in a browser, for example:
       http://localhost/competition/index.php
8. Log in as admin (username: admin) to add scores, then view the
   scoreboard.

