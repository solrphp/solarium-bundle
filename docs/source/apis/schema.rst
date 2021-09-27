Schema API
==========
| see the `official Schema API reference <https://solr.apache.org/guide/schema-api.html>`_ for the api's description.

.. |api-manager| replace:: ``SchemaManager``

.. include:: ../partials/api-manager.rstinc

.. include:: ../partials/config-handler.rstinc

.. include:: ../partials/console-commands.rstinc

update
~~~~~~
| the schema update is available by running

.. code-block:: bash

    $ php bin/console solr:schema:update <core-name>

| this command compares the properties configured under ``managed_schemas`` for given core name and syncs the ``managedschema`` of your solr instance accordingly.