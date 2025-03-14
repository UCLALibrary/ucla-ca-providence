# ucla-ca-providence
UCLA customized version of CollectiveAccess Providence: Cataloguing and data/media management application.

## Developer Notes

This is initially built from the latest release of CollectiveAccess Providence: version 2.0.3.  We may need
to extend this software, so needed a developer-friendly way to create a consistent, containerized development
environment.  At present, the official (?) image ([Docker hub](https://hub.docker.com/r/tofran/providence), [Github](https://github.com/tofran/providence-docker)) is for the older 1.7.17.

For convenient development, all code in the image is available on the host. This makes it easy to change code and get immediate feedback.
This is done via a volume mount in `docker-compose.yml`.

Since docker containers share the users and groups from their host, the built-in `www-data` Apache user could not be used: setting
appropriate permissions in the container caused unwanted ownership changes of the files on the host. Instead, a new container
user `ca-www-data` is created, which *should* normally correspond to the developer's userid on their host machines.

### Build (or rebuild, if needed)
`docker compose build`

### Start 
`docker compose up -d`

### View logs
`docker compose logs -f` (all logs)

`docker compose logs -f providence` (Providence / Apache logs only)

### Restart application service only
Useful for restarting Apache

`docker compose restart providence`

### Stop
`docker compose down`

## Installation Notes

1. Build and start the system as above.
2. Visit the [installation page](127.0.0.1:8090/install).
3. Enter your email address for the local administrator account.
4. Choose an installation profile (probably `[Standard] PBCore v1.2 REVISED 2013`)
5. Click "Begin installation".

Installation takes a couple of minutes, as the database is built.  When done, a randomly-created administrator password
will be shown; be sure to preserve this in a safe place.

At present, installing PBCore profile gives an error message:
> processSettings: Relationship type distributor is not valid for ca_entities (ca_entities_x_occurrences); set in relationship type restriction setting distributor for UserInterface:standard_occurrences_ui:Screen intellectual:Placement ca_publishers

This does not seem to be important and can be ignored for now.

### Reinstallation

To try a different profile, the old database must be wiped out. Be sure you really want to do this!

1. Shut down the system, if running.
2. Delete the docker database volume: `docker volume rm ucla-ca-providence_db`
3. Start up the system.
4. Run the installation process as described above, starting at step 2.

## Login and logout

[Login page](http://127.0.0.1:8090/index.php/system/auth/login).

The UI doesn't seem to have a link to the [logout page](http://127.0.0.1:8090/index.php/system/auth/logout).
