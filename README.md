Test Project.

1. Clone project from github by using "git clone https://github.com/gridiron9/php-project".
2. Once it is finished open it in Phpstorm, VSCode or etc. Open terminal and go to Projects directory. Then run "docker compose up". There are 3 containers needs to be run. Wait for them and if it`s finished you are good to go.

Links:

-For website use http://localhost/. Used port here is 80 but there is no need to type it.

-For mysql management type http://localhost:8001/. 

Process:

-On first open code will check if there is table in the db or not. If there is not it will create it and inform you.

-Then after you refreshed the page, code automatically will start to read from csv file and insert data to table. You can see execution time.

-Voala you are ready. Data is displayed in table format, all the filters is there. You can play around. 

-To export table data to .csv file, click the button on top right corner and users.csv will be stored inside of exports folder. 



If on first open to localhost, you face "Call to undefined function mysqli_connect()", it is because php library is not installed. To solve it.
1. Get names of docker ps. Name should probably by "php-project-www-1".
2. Open internal command prompt of container with "docker exec -it <container_name> bash". In this case it should be
"docker exec -it php-project-www-1 bash".
3. Run following command "docker-php-ext-install mysqli && docker-php-ext-enable mysqli && apachectl restart". Problem should be solved.

