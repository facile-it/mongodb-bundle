# Upgrading notes

## Upgrading to 1.1.0

The release [1.1.0] introduced a BC change in the configuration semantics.

Before 1.1.0, if not explicitly provided, the value of `authSource` was set to the database name specified in the
connection section, or to `admin` if even the database name was empty.

With 1.1.0, if the `authSource` key is not set in the configuration, it won't be set in connection options either.

Take a look to [CHANGELOG.md](../CHANGELOG.md) for further details.

[1.1.0]: https://github.com/facile-it/mongodb-bundle/releases/tag/1.1.0
