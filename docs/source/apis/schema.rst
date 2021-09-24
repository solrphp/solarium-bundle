Schema API
==========
| see the `official Schema API reference <https://solr.apache.org/guide/schema-api.html>`_ for the api's description.

.. include:: ../partials/config-handler.rstinc

console commands
----------------
| in order to use these commands, make sure you've got at least one :doc:`endpoint <../reference/configuration/endpoints>`, :doc:`client <../reference/configuration/endpoints>` and :doc:`managed schema <../reference/configuration/schemas>` configured.
| currently supported features and commands:

update
~~~~~~
| the schema update is available by running

.. code-block:: bash

    $ php bin/console solr:schema:update <core-name>

| this command compares the properties configured under ``managed_schemas`` for given core name and syncs the ``managedschema`` of your solr instance accordingly.