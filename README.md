# loginwith_linkd
Login using linkd sign in. 

============ Introduction ============

This scripts helps web developers to implement the user registration with LinkedIn using PHP at their website and user information would be stored at the database.
This is for demo purpose only. We can use more coding standard and framework to develop large application.

============ Installation ============

1. Create a database (login_linkd) at phpMyAdmin.
2. Import the users.sql file into the database (login_linkd).
3. Open the User.class.php file and modify the $dbHost, $dbUsername, $dbPassword, $dbName variables value with your MySQL database credentials.
4. Open the configLinkd.php file and specify the $appId, $appSecret, and $redirectURL as per your LinkedIn App credentials.
5. Browse the index.php file in the browser and test the Login with LinkedIn functionality
