# Welcome to this contributing guide

This guide contains all the procedure and details for setting up the project and integrate the TodoList & Co team.

- [Setup the project](#setup-the-project) 
<br/><br/>
- [Continuous integration](#continuous-integration)
  - [Code quality](#code-quality)
  - [Tests](#tests)
<br/><br/>
- [Contributing on project](#contributing-on-project)
  - [working on an issue](#working-issue)
    - [creating an issue and working on it](#creating-issue)
    - [working on an existing issue](#existing-issue)
  - [creating a pull request](#creating-pull-request)
<br/><br/>
- [Contact](#contact) 



<h2 id="setup-the-project">Setup the project</h2>

If you're new contributor, please read the following steps :

**Step 1 -> Clone the Repository**

```
git clone -b develop https://github.com/armandselbmann/P8-TODOLIST.git
```

**Step 2 -> Docker setup and init Databases** <br/>
Run your Docker desktop and run the following command on the root of your cloning folder.
This will launch you the Docker containers, create and set up the databases for application and tests, and create an initial dataset in each.

```
make install
```

**Step 3 -> Configuring global environment variables** <br/>
Duplicate and rename the .env file to .env.local and modify the necessary information. <br/>
If you change the name of database, think to change too in .env.test file

## ___________________

<h2 id="continuous-integration">Continuous integration</h2>
<h3 id="code-quality">Code quality</h3>

Before aboard this subject, you have to know that the project follows the good practices of PSR-1 and PSR-12: <br/>
Here are the links : [PSR-1](https://www.php-fig.org/psr/psr-1/)  / [PSR-12](https://www.php-fig.org/psr/psr-12/)

To monitor code quality and compliance with these standards, PHP Stan and PHP CodeSniffer have been implemented. You can use them independently through these commands :

**PHP Stan** 
```
 make stan 
 ```

**Run PHP_Codesniffer** 
``` 
make sniffer 
```

Here is also a link to [Codacy](https://app.codacy.com/gh/armandselbmann/P8-TODOLIST/dashboard) which also statically analyzes the code and returns issues.


<h3 id="tests">Tests</h3>

PHPUnit is used to perform the tests related to this project. Pull requests must include testing for any new or changed code.

Tests can be run in two ways:
- during a commit
- with this following command : 
``` 
make test
```
If you are fixing a bug, please include a test that reproduces the error and verifies that it is fixed. <br />
Please make sure that all tests pass before commit your files. 

## ___________________

<h2 id="contributing-on-project">Contributing on project</h2>
<h3 id="working-issue">Working on an issue</h3>

<h4 id="creating-issue">Creating an issue and working on it</h4>

You can create a new issue at this [link](https://github.com/armandselbmann/P8-TODOLIST/issues/new) <br/>
Assign this issue to yourself, choose P8-TODOLIST on project and choose an corresponding milestone.

Create a branch with these naming rules _**feature/#number_of_the_issue**_ and work on it. <br/>
```Exemple : feature/#8```

After working on it, commit your files with these naming rules: _**description #number_of_the_issue**_ <br/>
```Exemple : git commit -m "Creation of the user profile #8"```

At this stage you will have the possibility to launch a test phase, and code review. <br/>
Do this to make sure you haven't forgotten those steps !

Follow the step "Creating a Pull Request".

<br/>
<h4 id="existing-issue">Working on an existing issue</h4>

If you want to contribute on an issue, you must clone the corresponding branch and create a new branch on your local repository with these naming rules: _**feature/#number_of_the_issue/name_of_your_branch**_ <br/>
```Exemple : feature/#8/form```

After working on it, commit your files with these naming rules: _**description #number_of_the_issue**_<br/>
```Exemple : git commit -m "Add contact form on user profile #8"```

At this stage you will have the possibility to launch a test phase, and code review. <br/>
Do this to make sure you haven't forgotten those steps !

Follow the next step.

<br/>
<h3 id="creating-pull-request">Creating a Pull Request</h3>

Then run the git push command to transfer your code to the remote repository.
Your branch is then created on Github and you can create now a pull request in order to merge it on the develop branch.
Please choose a reviewer to validate your work before it ends up on develop.
In any case, your pull request will be read again when put into production.

## ___________________

<h1 id="contact">Contact</h1>

If you have any question about this guide you can contact us at : [admindev@armand-selbmann.fr](mailto:admindev@armand-selbmann.fr)