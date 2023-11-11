# Contributing to Facile.it MongoDB Bundle

## Guidelines

Any contribution is always welcome and appreciated.
However, be sure to follow these rules please.

### Did you find a bug?

- Ensure the bug wasn't already reported by searching under [Issues] (include also closed one).
- If you find a closed issue, add a comment on it before opening another issue.
- In any case, when reporting a bug, provide a **clear description**, and a **code sample** or an **executable test case**. 

[Issues]: (https://github.com/facile-it/mongodb-bundle/issues)

### Do you have a fix for a reported bug?

- Open a new PR with the patch.
- Link your PR with the existing issue (using *Resolves #xyz*, or other [GitHub keywords], in the PR description is fine).
- Add a short description of the problem and of the solution proposed.
- Open Draft PR if you are still working on it, and you're not ready for a review.

[GitHub keywords]: https://docs.github.com/en/issues/tracking-your-work-with-issues/creating-issues/linking-a-pull-request-to-an-issue?utm_campaign=Front%2BEnd%2BDev%2BWeekly&utm_medium=web&utm_source=Front_End_Dev_Weekly_33#linking-a-pull-request-to-an-issue-using-a-keyword

### Do you have worked on a purely cosmetic patch?

Since we defined our code stile, and configured a quality tool for enforce its usage, we won't accept cosmetic patch anymore.

### Do you want to add a new feature or change an existing one?

- For anything longer than a few lines, open an issue first for discussing it.
- Provide a complete description of the feature (or of the part you'd like to change if it's the case).

## Development Environment

We like a lot Docker and Make.

We use`docker-compose` to set up and insulated, reproducible development environment.
Make helps us to define easy-to-use build targets.

Run `make usage` or just `make` inside the project folder to get a list of available targets.

### Docker and docker-compose

Using Docker is not mandatory, but strongly recommended.
If you have it installed along with `docker-compose`, then run:

    make setup

...and it'll pull the images and install dependencies with composer.
This target eventually stops the containers.

To create the containers and log into the one with the php interpreter, run:

    make sh

Once you're done, stop the container composition with:

    make stop

Feel free to look into the [Makefile](../Makefile) to know what those targets do.

#### Port Binding Setup

If you use Docker also for other project, you might have other containers running and bound to some network ports.
If you get a port binding error when starting the containers with `make setup` or `make sh`, then you need to select
another port for the binding.

Change the port binding into the `docker-compose.override.yml` file.
It is generated from the `docker-compose.override.yml.dist` template, and it's not tracked by git.

### Run a different version of PHP and/or the ext-mongodb

By default `make setup` will build a docker image with the lower supported version of PHP 
and `ext-mongodb`.

If you need a different environment, you should exec one of the following target **instead** of `make setup`.
    
    make setup-81 # PHP 8.1, ext-mongodb 1.12
    make setup-82 # PHP 8.2, ext-mongodb 1.15

Feel free to add other `setup-*` targets to meet your needs (take a look at the [Makefile](../Makefile) and just follow the same convention).

Remember to edit the [ci.yaml](../.github/workflows/ci.yaml), if you need to test the bundle against
a specific set of PHP and ext-mongodb versions.

NB: Changing the `mongo` image version, used in [docker-compose.yaml](../docker-compose.yml) could lead to
start-up errors such as:
> This version of MongoDB is too recent to start up on the existing data files

This is due to the `mongo` image declaring a `VOLUME` in its definition.
Be sure to run `make stop` to effectively clean containers and volumes before trying again.

### Tests and Quality Tools

This project uses `phpunit` to run its test suite.
Also, it uses `phpstan` for static code analysis and `php-cs-fixer` for code style.

It's common to run the following in sequence.

    make test
    make cs-fix
    make phpstan
