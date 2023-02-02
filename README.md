# Projet 8 - OpenClassrooms - Améliorez une application existante de ToDo & Co

### Parcours Développeur d'application - PHP / Symfony
**Armand Selbmann**


---
### Code quality
The code has benn verified by Codacy.<br>
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/810839a413754e15bad106e755196569)](https://www.codacy.com/gh/armandselbmann/P8-TODOLIST/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=armandselbmann/P8-TODOLIST&amp;utm_campaign=Badge_Grade)

---
## How to Setup project
### Technical requirements
- [Docker](https://www.docker.com/)
- [Makefile](https://www.gnu.org/software/make/)

### 1/ Clone the Repository
SSH :
```
git@github.com:armandselbmann/P8-TODOLIST.git
```
HTTPS :
```
https://github.com/armandselbmann/P8-TODOLIST.git
```

### 2/ Docker setup and init Databases
Run your Docker desktop and run the following command on the root of your cloning folder.<br/>
This will launch you the Docker containers, create and set up the databases for application and tests, and create an initial dataset in each. <br/>
```
make install
```
### 3/ Configuring global environment variables
Duplicate and rename the .env file to .env.local and modify the necessary information.<br/>
If you change the name of database, think to change too in .env.test file <br/>
Test the application on prod environnement.


## Run tests
If you want to execute the tests, use the following command. <br/>
This will reinitialise the test database and launch tests.
```
make test
```


## Documentation
You can find these documentation on the doc folder at the root of the project :
- Audit on code quality and performance analyse
- Technical documentation on authentication and authorization implementation

UML diagrams are on diagrams folder.


---
## Default account credentials

Here are the roles assigned to the different users and their credentials in order to test the application : </br>

Registered User : ROLE_USER </br>
Administrator : ROLE_ADMIN </br>

```
Registered User
username : lolo
password : lolo
_________________
Administrator
username : jane
password : jane
```

# You want to contribute in this project !
You will find [documentation](contributing.md) at the root of the project.<br/>
This contains all the procedure and details for setting up the project and integrate the TodoList & Co team. <br/>
See you soon !