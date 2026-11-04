# Student Portal
A student portal in PHP and MySQL. Students register, log in and enroll in courses, and admins manage the course list. There is also a small JSON API protected with JWT tokens.

## Running it locally
You need XAMPP and Composer. Start Apache and MySQL, then clone the repo into htdocs and install the packages:

```
git clone https://github.com/cynthiaaghaogwu011-tech/student_portal_php.git
composer install
```

Create a database called student_portal_php in phpMyAdmin and import schema.sql.

Settings go in a file called .env. I keep it in C:\xampp, one level above htdocs, so it can't be opened from a browser and doesn't end up in Git. .env.example shows the names you need: DB_HOST, DB_NAME, DB_USER, DB_PASS and JWT_SECRET. To make a secret for JWT_SECRET, run:

```
php -r "echo bin2hex(random_bytes(32));"
```

Register an account on the site. To make it an admin, change its role to admin in the users table.

## The API

The endpoints are in the api folder and all return JSON.

login.php takes an email and password and returns a token that lasts an hour. courses.php and course.php need a logged-in user. course_create.php, course_update.php and course_delete.php are for admins only. A student gets a 403 on those.

Logging in:

```
curl -X POST -H "Content-Type: application/json" \
  -d '{"email": "student@example.com", "password": "your-password"}' \
  http://localhost/api/login.php
```

Using the token:

```
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  http://localhost/api/courses.php
```

Apache needs to pass the Authorization header to PHP. The .htaccess file in the api folder does that on XAMPP.

courses_api.html is a small page that logs in through the API and lists the courses.

## Notes

Passwords are hashed with password_hash, queries use prepared statements, and the forms have CSRF protection. A wrong email and a wrong password give the same error message, so the login doesn't reveal which emails exist.

Things I would add next: refresh tokens, rate limiting on login, tests for the API, and splitting the API into controllers.