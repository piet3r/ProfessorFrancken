# T.F.V. 'Professor Francken' [![Build Status](https://travis-ci.org/ProfessorFrancken/ProfessorFrancken.svg?branch=master)](https://travis-ci.org/ProfessorFrancken/ProfessorFrancken) [![Coverage Status](https://coveralls.io/repos/github/ProfessorFrancken/ProfessorFrancken/badge.svg?branch=master)](https://coveralls.io/github/ProfessorFrancken/ProfessorFrancken?branch=master)

This is the repository containing code for the new website of
[T.F.V. Porfessor Francken](http://professorfrancken.nl/).
We are using the [Laravel v5.2](http://laravel.com/docs/5.2) framework in
combination with [Broadway](http://github.com/qandidate-labs/broadway), an
infrastructure library for creating CQRS and Event Sourced applications.

You can find some high quality introductory videos on
[laravelfromscratch.com](laravelfromscratch.com).
For more info on Broadway, watch
[Willem-Jan Zijderveld speak on CQRS and Event Sourcing](https://www.youtube.com/watch?v=d1PDPsxWGqM).

- [Contributing](#contributing)
    - [Generating css (or compiling assets)](#generating-css-or-compiling-assets)
        - [Windows configuration](#windows-configuration)
    - [Testing](#testing)
    - [Code style](#code-style)
    - [Git Usage](#git-usage)
- [An introduction to this application's folder and namespace structure](#an-introduction-to-this-applications-folder-and-namespace-structure)
    - [Domain layer](#domain-layer)
    - [Application layer](#application-layer)
        - [Adding projectors](#adding-projectors)
    - [Infrastructure layer](#infrastructure-layer)
- [Setting up a VM](#setting-up-a-vm)
    - [Generate a ssh key](#generate-a-ssh-key)
    - [Setup homestead](#setup-homestead)
    - [Verify that everything is working](#verify-that-everything-is-working)
    - [Configure your hosts file](#configure-your-hosts-file)
        - [Using the xip.io service](#using-the-xipio-service)

## Contributing
Before you push your changes to this repository make sure that the tests are all
green and that there are no issues with code style.

Below you will find a quick summary on how to run the tests and a code style
fixer.
This summary assumes you've correctly setup your development environment (i.e.
it assumes you've setup a vagrant box with Homestead and you've run `composer install`).

### Generating css (or compiling assets)

We use [scss](http://sass-lang.com/) which compiles to css.

To compile our assets you will first have to install some necessary javascript
dependencies. You can do this using [npm](npmjs.com).
Assuming you've installed npm locally you can run the command,
```
npm install
```
Alternatively if you prefer to use a docker container, then you can run
`docker-compose run npm npm install` (assuming you're
using [our docker repo](https://github.com/ProfessorFrancken/Docker)).


Once you've installed the javascript dependencies you can compile our assets by
running
```
npm run dev
```
in the root of this project (the folder in which a `package.json` file  is
present), or alternatively use `docker-compose run npm npm run dev`.
The compiled files will be placed in the `/public/dist` folder. This folder is
added to our `.gitignore` so you won't see the folder until you run the above
command.

Check the documentation
of [Laravel Mix](https://laravel.com/docs/5.4/mix#introduction) to learn more
about how our assets (css & js) are compiled.

#### Windows configuration

I haven't yet tested whether this works on Windows.. If you're using Docker
(which you should :-)) then the docker commands should work.

### Testing
Once your VM has been setup you should be able to run the unit and integration
tests and verify that everything is working correctly.

Run the tests from your VM in the root directory:
```
vendor/bin/phpunit
```

### Code style
We're using the PSR-2 code style guidelines as well as PSR-4 for autoloading classes.
See the [PHP-fig](http://www.php-fig.org/psr/) for more info on the standards.
You can use `fabpot/php-cs-fixer` to verify that your code conforms to the PSR-2
code style guidelines. By running the following command in the root directory
all code style issues be found.
```
vendor/bin/php-cs-fixer fix . --level=psr2 --dry-run --fixers=-psr0
```
By removing the `--dry-run` option any issues found by the program will be fixed
automatically. Add the `--diff` option to show the diff for each file.
N.B. once we've gone open source we will use a continuous integration service
that automatically checks whether your PR conforms to the code style guidelines.

*Note* we omit the `psr0` fixer. This fixer looks at the namespace declarations
however it is not compatible with PSR-4, which is an improvement over PSR-0.

### Git Usage
The master branch is a protected branch, meaning you won't be able to force push changes to the master branch and status checks (i.e. tests should be green) are required before merging to master.
Before sending a pull request with your latest changes you should make sure that your branch is up to date by rebasing your branch onto master.
This makes it easier to review your pull request since there won't be many merge commits and it gives us a nice linear history.

We generally use a [git flow](http://nvie.com/posts/a-successful-git-branching-model/) ish git model.
Since this application hasn't been pushed to production yet we won't be very strict about this, however you should try to be consistent with the naming of branching (i.e. using feature and branches and bug fix branches).

## An introduction to this application's folder and namespace structure
This application is build upon an hexagonal architecture and is inspired by the
talk [Hexagonal architecture - message oriented software design](http://www.slideshare.net/matthiasnoback/hexagonal-architecture-messageoriented-software-design-php-benelux-2016)
by Matthias Noback.

The application is divided by three layers: a Domain layer, Application layer
and an Infrastructure layer.
Each layer is only allowed to be dependent on its own layer, or a layer "below"
it.
Doing so ensures that we can use dependency inversion and makes our application
more testable.

### Domain layer
The domain layer contains all of the business logic. Except for
[Broadway](https://github.com/qandidate-labs/broadway) this layer should not
have any external dependencies and *must* be framework agnostic.

The code in the domain layer should have 100% line and mutation coverage and
should only need unit tests.

The [docs](docs/) folder contains some documentation of important domain concepts.

### Application layer
The application layer gives us an entry point into the domain layer.
Inside the application layer we can find use cases of some our domain concepts.
These use cases can be commands and command handlers, projections, event
handlers and processors.
The code inside this layer *should* be framework agnostic and should not need
acceptance tests, however each class should be unit and integration tested.

#### Adding projectors
We use projectors to generate read models. Currently these projectors are placed
in `App\ReadModels`. Once you've created a projector you should add its Fully
Qualified Classs Name (FQCN) to the `event_sourcing.php` config file.
The application will then automatically call each of the projectors in the
config file when an event is published.

### Infrastructure layer
The infrastructure layer is the only layer that is not framework agnostic and
could as well be called the "framework layer".
This layer contains Laravel's service providers and the console and http
kernels and their commands and controllers.
It may also contain implementations of repositories, notification services etc.

Code inside the infrastructure layer should be tested using acceptance criteria.
Unit and integration tests should be written whenever they are useful.


## Setting up a VM
You should first install [virtualbox](https://www.virtualbox.org/wiki/Downloads)
and [vagrant](https://www.vagrantup.com/).
Vagrant is used to provision our VM.
You will now need to download Homestead, e.g. by cloning the repository:
```
git clone https://github.com/laravel/homestead.git Homestead
```

### Generate a ssh key
If you do not yet have an ssh key (if you've installed
[Github Desktop](https://desktop.github.com/) then you can skip this step) then
you should generate a new, see
[generating a new ssh key](https://help.github.com/articles/generating-a-new-ssh-key/).

Next copy and rename the `Homestead.yaml.dist` file to `Homestead.yaml`, also
copy and rename the `.env.example` file to `.env`.
If you are on windows, then change the `authorize` as well as the `keys` property in `Homestead.yaml` to point to your
public ssh key.

### Setup homestead
Once virtualbox and thereafter vagrant have been installed and you've correctly
cloned Homestead and configured your sshkey, then you should install the
`homestead` box:
```
vagrant box add laravel/homestead
```
Downloading the box might take a while.

Next you *should* be able to run `vagrant up`, which starts the VM,
```
vagrant up
```
Once vagrant has finished you can ssh into the VM by using `vagrant ssh`. The VM
is automatically configured with nginx and MYSQL database. See the
[homestead documentation](https://laravel.com/docs/5.2/homestead) for some
additional info.

### Verify that everything is working
Once you've successfully ssh'd into your VM you'll need to install some
dependencies using [composer](http://getcomposer.org/).
Next `cd` into the `francken` directory and then run `composer install`.
Once that's finished you should be able to run the unit and integration tests,
```
vendor/bin/phpunit
```

Next if you want to visit the website you should run some migrations (these will
configure your MYSQL database) and setup an random application key (this is used
for security stuff),
```
php artisan key:generate
php artisan migrate
```

### Configure your hosts file
In order to actually visit the website you will have to configure your (local,
so not the VM's)
`/etc/hosts` file to redirect `francken.app` to `192.168.10.10`, by adding the
next line to your `/etc/hosts` file (this requires root permission, so use sudo)
```
sudo echo "192.168.10.10  francken.app" >> /etc/hosts
```

#### Using the xip.io service
Instead of configuring your host file you should also be able to open the
website by visiting [francken.192.168.10.10.xip.io/](http://francken.192.168.10.10.xip.io/).
